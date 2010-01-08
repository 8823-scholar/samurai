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
 * triggerが引かれた際にメールを投げます
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Logger_Client_Mail extends Samurai_Logger_Client
{
    /**
     * From:
     *
     * @access   public
     * @var      string
     */
    public $from = 'alert@localhost';

    /**
     * Subject:
     *
     * @access   public
     * @var      string
     */
    public $subject = '[alert] Alert of samurai.';

    /**
     * 配送先メールアドレス
     *
     * @access   public
     * @var      array
     */
    public $mail = array();





    /**
     * @override
     */
    public function define(array $define)
    {
        parent::define($define);
        foreach($define as $_key => $_val){
            switch($_key){
                case 'from':
                case 'subject':
                    $this->$_key = (string)$_val;
                    break;
                case 'mail':
                    $this->$_key = (array)$_val;
                    break;
            }
        }
    }


    /**
     * @implements
     */
    public function trigger($level, $message, $file, $line)
    {
        $body = $this->_makeBody($level, $message, $file, $line);
        foreach($this->mail as $mail){
            $this->_sendMail($mail, $this->from, $this->subject, $body);
        }
    }


    /**
     * メッセージを生成
     *
     * @access     private
     * @param      string  $level     ログレベル
     * @param      string  $message   メッセージ
     * @param      string  $file      ファイル
     * @param      int     $line      ライン
     * @return     string
     */
    private function _makeBody($level, $message, $file, $line)
    {
        $messages = array();
        $messages[] = sprintf('%s：%s', '発生日時', date('Y/m/d H:i:s'));
        $messages[] = sprintf('%s：%s', 'レベル', $level);
        $messages[] = sprintf('%s：%s', 'メッセージ', $message);
        $messages[] = sprintf('%s：%s(line%s)', 'ファイル', $file, $line);
        $messages[] = sprintf('%s：%s', 'USER AGENT', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $messages[] = sprintf('%s：%s', 'REQUEST URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
        return join("\r\n", $messages);
    }

    
    /**
     * 送信
     *
     * @access     private
     * @param      string   $to
     * @param      string   $from
     * @param      string   $subject
     * @param      string   $body
     */
    private function _sendMail($to, $from, $subject, $body)
    {
        mb_send_mail($to, $subject, $body, 'From: ' . $from);
    }
}
