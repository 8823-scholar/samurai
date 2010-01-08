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

/**
 * 表示を担当するFilter
 *
 * Rendererは使用せずにピュアPHPで記述できる。
 * また、全てのView系フィルターの元になります。
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_ViewSimple extends Samurai_Filter
{
    /**
     * Responseコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Response;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * Cookieコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Cookie;

    /**
     * Sessionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Session;

    /**
     * Tokenコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Token;

    /**
     * Actionの結果から何を行うかの値(Template|Action|Locationなど)
     *
     * @access   protected
     * @var      string
     */
    protected $_do = 'Template';

    /**
     * _doの内容
     *
     * @access   protected
     * @var      string
     */
    protected $_do_value = '';

    /**
     * Actionの結果
     *
     * @access   protected
     * @var      string
     */
    protected $_view = '';

    /**
     * Actionの結果その他値
     *
     * @access   protected
     * @var      array
     */
    protected $_view_params = array();


    /**
     * @override
     */
    protected function _postfilter()
    {
        parent::_postfilter();
        //Actionの結果取得
        $this->_setActionResult();
        //処理切り分け
        if($this->_do){
            $method = '_do' . $this->_do;
            if(method_exists($this, $method)){
                $this->$method();
            } else {
                throw new Samurai_Exception("No exists {$method} method.");
            }
        }
    }
    
    
    
    
    
    /**
     * Location:
     *
     * @access     protected
     */
    protected function _doLocation()
    {
        $this->Response->setRedirect($this->_do_value);
        $this->Response->execute();
    }


    /**
     * Action:
     *
     * @access     protected
     */
    protected function _doAction()
    {
        $this->ActionChain->add($this->_do_value);
    }


    /**
     * Template:
     *
     * @access     protected
     */
    protected function _doTemplate()
    {
        if($this->_do_value){
            //解釈
            $template = Samurai_Loader::getPath(Samurai_Config::get('directory.template') . DS . $this->_do_value);
            if(Samurai_Loader::isReadable($template)){
                ob_start();
                include($template);
                $contents = ob_get_contents();
                ob_end_clean();
            } else {
                throw new Samurai_Exception('Template is not found. -> ' . $this->_do_value);
            }
            //表示
            $Body = $this->Response->setBody($contents);
            if(is_object($Body) && !$this->Response->hasHeader('content-type')){
                $Body->setHeader('content-type', sprintf('text/html; charset=%s', Samurai_Config::get('encoding.output')));
            }
            $this->Response->execute();
        }
    }





    /**
     * Actionの結果取得
     *
     * @access     private
     */
    protected function _setActionResult()
    {
        //結果セット
        $result = $this->ActionChain->getCurrentActionResult();
        if(is_array($result)){
            $this->_view = trim(array_shift($result));
            foreach($result as $_key => $_val){
                $this->_view_params[$_key] = $_val;
            }
        } else {
            $this->_view = $result;
        }
        //DOのセット
        if($this->_view){
            $this->_do_value = $this->getAttribute($this->_view);
            if($this->_do_value && preg_match('/^(.+?):(.*?)$/', $this->_do_value, $matches)){
                $this->_do = ucfirst(strtolower(trim($matches[1])));
                $this->_do_value = trim($matches[2]);
            } else {
                $this->_do = 'Template';
            }
            if(preg_match_all('/<(.+?)>/', $this->_do_value, $matches, PREG_SET_ORDER)){
                foreach($matches as $_key => $_val){
                    $param_key = $_val[1];
                    if(isset($this->_view_params[$param_key])){
                        $this->_do_value = str_replace("<{$param_key}>", $this->_view_params[$param_key], $this->_do_value);
                    }
                }
            }
            if(!$this->_do_value){
                throw new Samurai_Exception('Failed to get template for ' . $this->_view);
            }
        } else {
            $this->Response->execute();
        }
    }
}

