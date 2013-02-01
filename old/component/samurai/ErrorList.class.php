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
 * Actionで発生したエラーを格納するためのクラス
 *
 * 各Actionごとに生成される
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_ErrorList
{
    /**
     * エラータイプ
     *
     * @access   private
     * @var      string
     */
    private $_type = '';

    /**
     * エラー文字列格納
     *
     * @access   private
     * @var      array
     */
    private $_list = array();


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * エラー文字列を追加
     *
     * @access    public
     * @param     string  $key     エラーが発生した項目
     * @param     string  $value   エラー文字列
     */
    public function add($key, $value)
    {
        if(!isset($this->_list[$key])) $this->_list[$key] = array();
        $this->_list[$key][] = $value;
    }


    /**
     * ErrorListをクリア
     *
     * @access    public
     */
    public function clear()
    {
        $this->_list = array();
    }


    /**
     * 現在エラーがあるかどうかを返却
     *
     * @access     public
     * @return     boolean
     */
    public function isExists()
    {
        return (bool)$this->_list;
    }

    /**
     * isExistsのシノニム
     *
     * @access     public
     */
    public function hasError()
    {
        return $this->isExists();
    }


    /**
     * エラーの種類を返却
     *
     * @access    public
     * @return    string
     */
    public function getType()
    {
        return $this->_type;
    }


    /**
     * エラーの種類を設定
     *
     * @access    public
     * @param     string  $type   エラーの種類
     */
    public function setType($type)
    {
        if(!$this->_type){
            $this->_type = $type;
        }
    }





    /**
     * 指定された項目のエラーを返却
     *
     * @access     public
     * @param      string  $key   エラーキー
     * @return     string
     */
    public function getMessage($key)
    {
        $message = '';
        if(isset($this->_list[$key])){
            $message = array_shift($this->_list[$key]);
            array_unshift($this->_list[$key], $message);
        }
        return $message;
    }


    /**
     * 指定された項目の全てのエラーを返却
     *
     * @access     public
     * @param      string  $key   エラーキー
     * @return     array
     */
    public function getMessages($key)
    {
        return isset($this->_list[$key]) ? $this->_list[$key] : array() ;
    }


    /**
     * 全てのエラーを一元的に取得する
     * その際、同一のキーのものはグルーピングされる。(キーは保持される)
     *
     * @access     public
     * @return     array
     */
    public function getAllMessage()
    {
        $messages = array();
        foreach($this->_list as $key => $_val){
            $messages[$key] = $this->getMessage($key);
        }
        return $messages;
    }


    /**
     * 全てのエラーを一元的に取得する。
     * その際、キーは全て無視される。(キーは保持されない)
     *
     * @access     public
     * @return     array
     */
    public function getAllMessages()
    {
        $messages = array();
        foreach($this->_list as $key => $_val){
            $messages = array_merge($messages, $_val);
        }
        return $messages;
    }
}

