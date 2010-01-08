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
 * sendmailコマンドを利用して送信するTransporter
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Mail_Transporter_Sendmail extends Etc_Mail_Transporter
{
    /**
     * sendmailパス
     *
     * @access   public
     * @var      string
     */
    public $sendmail_path = '/usr/sbin/sendmail';

    /**
     * sendmailオプション
     *
     * @access   public
     * @var      string
     */
    public $sendmail_args = '';



    /**
     * @implements
     */
    protected function _send()
    {
        //準備
        $headers = $this->_adjustHeaders();
        $headers = join($this->Mail->lineend, $headers);
        $from   = escapeshellcmd($this->Mail->getFrom()->mail);
        $recipients = array();
        foreach($this->Mail->getRecipients() as $Recipient){
            $recipients[] = $Recipient->mail;
        }
        $recipients = escapeshellcmd(join(' ', $recipients));
        $mailtext = $headers.$this->Mail->lineend.$this->Mail->lineend.$this->Part->getContent();
        //Windowsでない場合は、改行コードを\nに統一してあげる必要がある
        if(strpos(PHP_OS, 'WIN') !== 0){
            $mailtext = preg_replace("/\r\n/", "\n", $mailtext);
        }
        
        //送信
        $handle = popen(sprintf('%s%s -f%s -- %s', $this->sendmail_path,
                                    $this->sendmail_args ? ' '.$this->sendmail_args : '', $from, $recipients), 'w');
        if(!$handle){
            throw new Samurai_Exception('Failed to open sendmail. -> ' . $this->sendmail_path);
        }
        fputs($handle, $mailtext);
        $result = pclose($handle);
        if ($result != 0) {
            throw new Samurai_Exception('sendmail returned error code. -> ' . $result);
        }
    }


    /**
     * ヘッダーの調節
     *
     * @access     private
     * @return     array   ヘッダー
     */
    private function _adjustHeaders()
    {
        $headers = array();
        foreach($this->Part->getHeaders() as $key => $value){
            switch($key){
                default:
                    $key = join('-', array_map('ucfirst', explode('-', $key)));
                    $headers[] = sprintf('%s: %s', $key, $value);
                    break;
            }
        }
        return $headers;
    }
}

