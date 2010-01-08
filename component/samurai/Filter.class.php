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
 * Filterの抽象クラス
 *
 * すべてのFilterはこのクラスを継承する
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Filter
{
    /**
     * 属性情報
     *
     * @access   private
     * @var      array
     */
    private $_attributes = array();


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * フィルター特有の処理を実装する
     *
     * @access    public
     */
    public function execute()
    {
        $this->_prefilter();
        $this->_chainfilter();
        $this->_postfilter();
    }


    /**
     * Action::execute前に呼び出される処理
     *
     * @access    protected
     */
    protected function _prefilter()
    {
        Samurai::getContainer()->injectDependency($this);
        Samurai_Logger::debug('Filter::_prefilter executed. -> %s', get_class($this));
    }


    /**
     * Action::execute後に呼び出される処理
     *
     * @access    protected
     */
    protected function _postfilter()
    {
        Samurai::getContainer()->injectDependency($this);
        Samurai_Logger::debug('Filter::_postfilter executed. -> %s', get_class($this));
    }

    /**
     * フィルターをつなげる関数
     *
     * @access    protected
     */
    protected function _chainfilter()
    {
        $FilterChain = Samurai::getContainer()->getComponent('FilterChain');
        $FilterChain->execute();
    }





    /**
     * 属性の長さを返却する
     *
     * @access    public
     * @return    int
     */
    public function getSize()
    {
        return count($this->_attributes);
    }


    /**
     * 指定された属性を返却する
     *
     * @access     public
     * @param      string  $key       属性キー
     * @param      mixed   $default   デフォルト値
     * @return     mixed
     */
    public function getAttribute($key, $default = NULL)
    {
        $keys = explode('.', $key);
        $attribute = $default;
        foreach($keys as $_key => $_val){
            if(!$_key && isset($this->_attributes[$_val])){
                $attribute = $this->_attributes[$_val];
            } elseif(is_array($attribute) && isset($attribute[$_val])){
                $attribute = $attribute[$_val];
            } else {
                $attribute = $default;
            }
        }
        return $attribute;
    }


    /**
     * 属性をすべて取得
     *
     * @access    public
     * @return    array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }


    /**
     * 指定された属性に値をセット
     *
     * @access    public
     * @param     string  $key     属性のキー
     * @param     mixed   $value   属性の値
     */
    public function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
    }


    /**
     * 指定された属性に値をまとめてセット
     *
     * @access    public
     * @param     array   $attributes   格納したい属性配列
     */
    public function setAttributes(array $attributes)
    {
        $this->_attributes = array_merge($this->_attributes, $attributes);
    }
}

