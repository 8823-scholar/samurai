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
 * Etc_Mailの送信担当抽象クラス
 *
 * すべてのTransporterはこのクラスの実装クラスです。
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Etc_Mail_Transporter
{
    /**
     * Mailコンポーネント
     *
     * @access   protected
     * @var      object
     */
    protected $Mail;

    /**
     * TOPパート
     *
     * @access   protected
     * @var      object
     */
    protected $Part;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }



    /**
     * 送信トリガー
     *
     * @access     public
     * @param      object  $Mail   Etc_Mail
     */
    public function send(Etc_Mail $Mail)
    {
        //初期化
        $this->Mail = $Mail;
        if(!$Mail->hasContent()) throw new Samurai_Exception('Unsetted bodies or attaches');
        //TOPパートの構築
        $this->Part = new Etc_Mail_Mime_Part();
        foreach($this->Mail->getBodies() as $Body){
            $this->Part->addPart($Body);
        }
        foreach($this->Mail->getAttaches() as $Attach){
            $this->Part->addPart($Attach);
        }
        //送信
        $this->_prepareHeader();
        $this->_prepareBody();
        $this->_send();
    }


    /**
     * 抽象送信トリガー
     * 各Transporterで実装してください
     *
     * @access     protected
     */
    protected abstract function _send();


    /**
     * ヘッダーを調節、補完する
     *
     * @access     private
     */
    private function _prepareHeader()
    {
        //Return-Path
        $this->Part->setHeader('return-path', $this->Mail->getFrom()->mail);
        //To,Cc,Bcc
        foreach(array('to', 'cc', 'bcc') as $type){
            $_recipients = array();
            foreach($this->Mail->getRecipients($type) as $Recipient){
                $_recipients[] = $Recipient->encodeMimeheader($this->Mail->getCharset());
            }
            if($_recipients) $this->Part->setHeader($type, join(','.$this->Mail->lineend.' ', $_recipients));
        }
        //From
        $this->Part->setHeader('from', $this->Mail->getFrom()->encodeMimeheader($this->Mail->getCharset()));
        //Subject
        $this->Part->setHeader('subject', mb_encode_mimeheader($this->Mail->getSubject(), $this->Mail->getCharset()));
        //Date
        $this->Part->setHeader('date', date('r'));
        //その他
        foreach($this->Mail->getHeaders() as $key => $value){
            $this->Part->setHeader($key, $value);
        }
    }


    /**
     * メッセージボディを調節、補完する
     *
     * @access     private
     */
    private function _prepareBody()
    {
        
    }
}

