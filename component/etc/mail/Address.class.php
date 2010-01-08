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
 * Etc_Mailでメールアドレスを体現
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Mail_Address
{
    /**
     * メールアドレス
     *
     * @access   public
     * @var      string
     */
    public $mail = '';

    /**
     * 名前
     *
     * @access   public
     * @var      string
     */
    public $name = '';


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * 文字列に変換
     *
     * @access     public
     * @return     string  整形されたメールアドレス文字列
     */
    public function toString()
    {
        if(!$this->name){
            return $this->mail;
        } else {
            return sprintf('%s <%s>', $this->name, $this->mail);
        }
    }


    /**
     * encode_mimeheaderされた文字列
     *
     * @access     public
     * @param      string   $charset
     */
    public function encodeMimeheader($charset = NULL)
    {
        if(!$this->name){
            return $this->mail;
        } else {
            return sprintf('%s <%s>', $charset ?
                                        mb_encode_mimeheader($this->name, $charset) : mb_encode_mimeheader($this->name), $this->mail);
        }
    }
}

