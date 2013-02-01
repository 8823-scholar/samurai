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
 * Samurai_Response_Http用のメッセージボディクラス
 * 
 * Samurai_Response_Http::setBody等で返却される。
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Response_Http_Body
{
    /**
     * メッセージ本文
     *
     * @access   private
     * @var      string
     */
    private $_body = '';

    /**
     * ヘッダー
     *
     * @access   private
     * @var      array
     */
    private $_headers = array();


    /**
     * コンストラクタ
     *
     * @access     public
     * @param      string  $body
     */
    public function __construct($body = NULL)
    {
        if(is_string($body)) $this->setBody($body);
    }





    /**
     * ボディをセットする
     *
     * @access     public
     * @param      string  $body        ボディ
     * @param      boolean $addtional   追加分かどうか
     */
    public function setBody($body, $addtional = false)
    {
        $this->_body = $addtional ? $this->_body . $body : $body;
        //debugフィルターでも出力を行うためにつじつまが合わなくなっている
        //TODO:出力トリガーはviewフィルターでおこなうのではなく、もっと外の枠でやる必要があるのかもしれない
        //$this->setHeader('content-length', strlen($this->_body));
    }


    /**
     * ボディを取得する
     *
     * @access     public
     * @return     string
     */
    public function getBody()
    {
        return $this->_body;
    }


    /**
     * ヘッダーのセット
     *
     * @access     public
     * @param      string  $key     キー
     * @param      string  $value   値
     */
    public function setHeader($key, $value)
    {
        $key = strtolower($key);
        $this->_headers[$key] = $value;
    }


    /**
     * ヘッダーの取得
     *
     * @access     public
     * @param      string  $key   キー
     * @return     string
     */
    public function getHeader($key)
    {
        return isset($this->_header[$key]) ? $this->_header[$key] : NULL ;
    }


    /**
     * ヘッダーの総取得
     *
     * @access     public
     * @return     array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }
}

