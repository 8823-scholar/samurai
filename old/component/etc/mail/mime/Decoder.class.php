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

Samurai_Loader::loadByClass('Etc_Mail');

/**
 * メール解析用クラス
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Mail_Mime_Decoder
{
    /**
     * Mailコンポーネント
     *
     * @access   private
     * @var      object
     */
    private $Mail;

    /**
     * メールアドレスの正規表現
     *
     * @access   private
     * @var      string
     */
    private $_pattern_mail = '[a-zA-Z0-9_\-\.\?\+\/"]+@(\[)?[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\])?';


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }



    /**
     * デコードトリガ
     *
     * @access     public
     * @param      string  $mail_text   メール全文
     * @return     object  Etc_Mail
     */
    public function decode($mail_text)
    {
        //Mailインスタンス生成
        $this->Mail = new Etc_Mail();
        //デコード
        $Part = $this->_decode($this->_adjustMailText($mail_text));
        if(!$Part->isMultipart()){
            $Part->isText() ? $this->Mail->setBody($Part) : $this->Mail->addAttachment($Part) ;
        } else {
            while($SubPart = $Part->fetchPart()){
                $SubPart->isText() ? $this->Mail->setBody($SubPart) : $this->Mail->addAttachment($SubPart) ;
            }
        }
        //その他
        $this->_decodeRecipients($this->Mail, $Part);
        $this->_decodeOthers($this->Mail, $Part);
        return $this->Mail;
    }


    /**
     * メインデコード処理
     *
     * @access     private
     * @param      string  $mail_text   メール本文
     * @return     object  Etc_Mail_Mime_Part
     */
    private function _decode($mail_text)
    {
        //初期化
        $Part = new Etc_Mail_Mime_Part();
        //ヘッダー部分を抜き出す
        $headers = $this->_extractHeaders($mail_text);
        $this->_intractHeaders($Part, $headers);
        //本文を抜き出す
        if($Part->isMultipart()){
            $parts = $this->_extractParts($mail_text, $Part->boundary);
            foreach($parts as $part_text){
                $Part->addPart($this->_decode($part_text));
            }
        } else {
            $Part->content = Etc_Mail_Mime::decode($mail_text, $Part->encoding, $Part->charset);
        }
        return $Part;
    }


    /**
     * メール本文からヘッダー部分を抜き出す
     *
     * @access     private
     * @param      string  &$mail_text
     * @return     array   headers
     */
    private function _extractHeaders(&$mail_text)
    {
        //とりあえず改行で区切る
        $texts = explode(Etc_Mail_Mime_Part::LINEEND, $mail_text);
        //ヘッダー部分を取得する
        $headers = array();
        while($header = array_shift($texts)){
            if($header == '') break;
            if(isset($_key) && preg_match('/^\s+/', $header)){
                $headers[$_key] .= Etc_Mail_Mime::decode(ltrim($header));
            }elseif(preg_match('/^.*?:.*$/', $header)){
                list($_key, $_val) = preg_split('/\s*:\s*/', ltrim($header));
                $headers[$_key] = Etc_Mail_Mime::decode($_val);
            } else {
                array_unshift($texts, $header);
                break;
            }
        }
        //その他の部分を本文に
        $mail_text = join(Etc_Mail_Mime_Part::LINEEND, $texts);
        return $headers;
    }


    /**
     * ヘッダーをPartに埋め込む
     * ヘッダーではなくプロパティへ埋め込む項目も考慮
     *
     * @access     private
     * @param      object  $Part      Etc_Mail_Mime_Part
     * @param      string  $headers   ヘッダー
     */
    private function _intractHeaders($Part, $headers = array())
    {
        //埋め込み
        foreach($headers as $key => $value){
            switch(strtolower($key)){
                case 'content-transfer-encoding':
                    $Part->encoding = $value;
                    break;
                case 'content-type':
                    $values = preg_split('/\s*;\s*/', trim($value));
                    $Part->type = array_shift($values);
                    foreach($values as $value){
                        $value = str_replace('"', '', $value);
                        if(preg_match('/charset=(.*)/', $value, $matches)){
                            $Part->charset = $matches[1];
                            $this->Mail->setCharset($Part->charset);
                        } elseif(preg_match('/boundary=(.*)/', $value, $matches)){
                            $Part->boundary = $matches[1];
                        }
                    }
                    break;
                case 'content-disposition':
                    $values = preg_split('/\s*;\s*/', trim($value));
                    foreach($values as $value){
                        $value = str_replace('"', '', $value);
                        if(preg_match('/filename=(.*)/', $value, $matches)){
                            $Part->filename = $matches[1];
                        }
                    }
                    break;
                default:
                    $Part->setHeader($key, $value);
                    break;
            }
        }
    }


    /**
     * 各パートをboundaryで分割する
     *
     * @access     private
     */
    private function _extractParts($part_text, $boundary)
    {
        $boundary = Etc_Mail_Mime_Part::LINEEND . '--' . $boundary;
        $part_texts = preg_split('/' . preg_quote($boundary) . "(\-\-)?[\r\n]+/", $part_text);
        array_shift($part_texts); //1番最初は必ず空
        array_pop($part_texts);   //1番最後も必ず空
        return $part_texts;
    }



    /**
     * 宛先をデコードする
     *
     * @access     private
     * @param      object  Etc_Mail
     * @param      object  Etc_Mail_Mime_Part
     */
    public function _decodeRecipients($Mail, $Part)
    {
        //To:
        if($Part->hasHeader('to')){
            $addresses = preg_split('/\s*,\s*/', $Part->getHeader('to'));
            foreach($addresses as $address) $Mail->addTo($this->_decodeAddress($address));
            $Part->delHeader('to');
        }
        //Cc:
        if($Part->hasHeader('cc')){
            $addresses = preg_split('/\s*,\s*/', $Part->getHeader('cc'));
            foreach($addresses as $address) $Mail->addTo($this->_decodeAddress($address));
            $Part->delHeader('cc');
        }
        //Bcc:
        if($Part->hasHeader('bcc')){
            $addresses = preg_split('/\s*,\s*/', $Part->getHeader('bcc'));
            foreach($addresses as $address) $Mail->addTo($this->_decodeAddress($address));
            $Part->delHeader('bcc');
        }
    }


    /**
     * アドレス文字列をデコードする
     *
     * @access     private
     * @param      string  $address
     * @return     object  Etc_Mail_Address
     */
    private function _decodeAddress($address)
    {
        $Address = new Etc_Mail_Address();
        //メールのみ
        if(preg_match("/\s*?({$this->_pattern_mail})\s*?$/", $address, $matches)){
            $Address->mail = trim($matches[1]);
        }
        //コメント内
        elseif(preg_match("/(.*?)\s*?<({$this->_pattern_mail})>\s*?$/", $address, $matches)){
            $Address->mail = $matches[2];
            $Address->name = $matches[1];
        }
        return $Address;
    }


    /**
     * その他のものをデコードする
     *
     * @access     private
     * @param      object  $Mail   Etc_Mail
     * @param      object  $Part   Etc_Mail_Mime_Part
     */
    private function _decodeOthers($Mail, $Part)
    {
        //From:
        $Mail->setFrom($this->_decodeAddress($Part->getHeader('from')));
        $Part->delHeader('from');
        //Subject:
        $Mail->setSubject($Part->getHeader('subject'));
        $Part->delHeader('subject');
        //その他
        foreach($Part->getHeaders() as $_key => $_val){
            $Part->delHeader($_key);
            $Mail->setHeader($_key, $_val);
        }
    }



    /**
     * メール本文をデコードしやすくするために変更する
     *
     * @access     private
     * @param      string   $mail_text
     */
    private function _adjustMailText($mail_text)
    {
        $mail_text = preg_replace("/\r\n/", "\n", $mail_text);
        $mail_text = preg_replace("/\r/", "\n", $mail_text);
        $mail_text = preg_replace("/\n/", "\r\n", $mail_text);
        return $mail_text;
    }


    /**
     * 初期化
     *
     * @access     public
     */
    public function clear()
    {
        $this->Mail->clear();
    }
}

