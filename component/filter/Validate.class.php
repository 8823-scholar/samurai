<?php
/**
 * PHP version 5.
 *
 * Copyright (c) 2007-2010, Samurai Framework Project, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     * Neither the name of the Samurai Framework Project nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    SVN: $Id$
 */

Samurai_Loader::loadByClass('Filter_Validate_Manager');
Samurai_Loader::loadByClass('Filter_Validate_Rule');

/**
 * Validate処理を行うFilter
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Validate extends Samurai_Filter
{
    /**
     * 必須項目
     *
     * @access   protected
     * @var      array
     */
    protected $_required = array();

    /**
     * チェックルール
     *
     * @access   protected
     * @var      array
     */
    protected $_rules = array();

    /**
     * stopper
     *
     * @access   protected
     * @var      array
     */
    protected $_stoppers = array();

    /**
     * ValidateManagerコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Manager;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;


    /**
     * @override
     */
    public function _prefilter()
    {
        parent::_prefilter();
        $this->_createManager();
        $this->_build();
        $this->_validate();
        //エラー格納
        if($this->ErrorList->isExists()){
            $this->ErrorList->setType(Samurai_Config::get('error.validate'));
        }
    }



    /**
     * validateルール構築
     *
     * @access     protected
     */
    protected function _build()
    {
        foreach($this->getAttributes() as $_key => $_val){
            $_key = trim($_key);
            $_val = trim($_val);
            if($_key == '' || $_val == '') Samurai_Logger::error('Invalid Validator. -> %s', "{$_key}/{$_val}");
            //キーの分割
            list($attribute, $validator, $group, $negative) = $this->_resolveKeys($_key);
            //バリューの分割
            list($stopper, $message, $params) = $this->_resolveValues($_val);
            //必須項目は無条件にストッパーになる
            if($validator == 'required' || $validator == 'either'){
                $this->_required[] = $group ? $group : $attribute;
                $stopper = 1;
            }
            //Ruleの組み立て
            $Rule = new Filter_Validate_Rule();
            $Rule->attribute = $attribute;
            $Rule->validator = $validator;
            $Rule->group = $group;
            $Rule->stopper = $stopper;
            $Rule->message = $message;
            $Rule->params = $params;
            $Rule->negative = $negative;
            $this->_rules[] = $Rule;
        }
    }


    /**
     * キーを分割する
     *
     * <code>
     *     attribute.!validator:group
     * </code>
     *
     * @access     protected
     * @param      string  $key   キー
     * @return     array
     */
    protected function _resolveKeys($key)
    {
        $keys = explode('.', $key);
        if(count($keys) != 2) Samurai_Logger::error('Invalid Validator. -> %s', "{$_key}/{$_val}");
        $attribute = $keys[0];
        if(preg_match('/:/', $keys[1])){
            $keys2 = explode(':', $keys[1]);
            $validator = array_shift($keys2);
            $group = array_shift($keys2);
        } else {
            $validator  = $keys[1];
            $group = '';
        }
        //ネガチブ判断
        $negative = false;
        if(preg_match('/^!/', $validator)){
            $negative = true;
            $validator = substr($validator, 1);
        }
        return array($attribute, $validator, $group, $negative);
    }


    /**
     * バリューを分割する
     *
     * <code>
     *     stopper,error_message,...(validate_params)
     * </code>
     *
     * @access     protected
     * @param      string  $value   バリュー
     * @return     array
     */
    protected function _resolveValues($value)
    {
        $values = explode(',', $value);
        $stopper = $values[0] == '0' || $values[0] == '1' ? (int)array_shift($values) : 1 ;
        $message = (string)array_shift($values);
        return array($stopper, $message, $values);
    }



    /**
     * validate実処理
     *
     * @access     protected
     */
    protected function _validate()
    {
        foreach($this->_rules as $Rule){
            //STOP状態ならチェックしない
            if($this->_isStopping($Rule)) continue;
            //REQUEST値の取得
            $empty = true;
            if(preg_match('/,/', $Rule->attribute)){
                $value = array();
                foreach(explode(',', $Rule->attribute) as $attribute){
                    $parameter = $this->Request->getParameter($attribute);
                    if($parameter!==NULL && $parameter != '') $empty = false;
                    $value[] = $parameter;
                }
            } else {
                $parameter = $this->Request->getParameter($Rule->attribute);
                if($parameter!==NULL && $parameter != '') $empty = false;
                $value = $parameter;
            }
            //必須項目ではなく、かつ値が空の場合はチェックしない
            if(!$this->_isRequired($Rule) && $empty) continue;
            //Validate!!
            $Validator = $this->Manager->getValidator($Rule->validator);
            $result = $Validator->validate($value, $Rule->params);
            if(!$this->_isSuccess($result, $Rule)){
                $key = $Rule->group ? $Rule->group : $Rule->attribute;
                $this->ErrorList->add($key, $Validator->bindParams($Rule->message));
                if($Rule->stopper) $this->_stoppers[] = $key;
            }
        }
    }


    /**
     * STOP状態かどうか
     *
     * @access     protected
     * @param      object  $Rule   Filter_Validate_Ruleインスタンス
     * @return     boolean
     */
    protected function _isStopping(Filter_Validate_Rule $Rule)
    {
        if($Rule->group){
            return in_array($Rule->group, $this->_stoppers);
        } else {
            return in_array($Rule->attribute, $this->_stoppers);
        }
    }


    /**
     * 必須項目かどうか
     *
     * @access     protected
     * @param      object  $Rule   Filter_Validate_Ruleインスタンス
     * @return     boolean
     */
    protected function _isRequired(Filter_Validate_Rule $Rule)
    {
        if($Rule->group){
            return in_array($Rule->group, $this->_required);
        } else {
            return in_array($Rule->attribute, $this->_required);
        }
    }


    /**
     * 検証が成功したかどうか
     *
     * @access     protected
     * @param      boolean $result   検証結果
     * @param      object  $Rule     Filter_Validate_Ruleインスタンス
     * @return     boolean
     */
    protected function _isSuccess($result, Filter_Validate_Rule $Rule)
    {
        return (!$Rule->negative && $result) || ($Rule->negative && !$result);
    }



    /**
     * ValidatorManagerの生成
     *
     * @access     protected
     */
    protected function _createManager()
    {
        $this->Manager = new Filter_Validate_Manager();
        $this->ErrorList = $this->ActionChain->getCurrentErrorList();
    }
}

