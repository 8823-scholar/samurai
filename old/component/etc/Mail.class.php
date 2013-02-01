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

Samurai_Loader::loadByClass('Etc_Mail_Mime');
Samurai_Loader::loadByClass('Etc_Mail_Mime_Part');
Samurai_Loader::loadByClass('Etc_Mail_Address');

/**
 * メール送信用クラス
 *
 * Mimeメールに対応し、ファイル添付、HTMLメールに対応可能。
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Mail
{
    /**
     * To:
     *
     * @access   protected
     * @var      array
     */
    protected $_to = array();

    /**
     * Cc:
     *
     * @access   protected
     * @var      array
     */
    protected $_cc = array();

    /**
     * Bcc:
     *
     * @access   protected
     * @var      array
     */
    protected $_bcc = array();

    /**
     * From:
     *
     * @access   protected
     * @var      object
     */
    protected $_from;

    /**
     * その他header
     *
     * @access   protected
     * @var      array
     */
    protected $_headers = array();

    /**
     * 件名
     *
     * @access   protected
     * @var      string
     */
    protected $_subject = '';

    /**
     * 本文
     *
     * @access   protected
     * @var      array
     */
    protected $_bodies = array();

    /**
     * 添付ファイル
     *
     * @access   protected
     * @var      array
     */
    protected $_attaches = array();

    /**
     * mimeバージョン
     *
     * @access   public
     * @var      string
     */
    public $mime_ver = '1.0';

    /**
     * 正式な送信元
     *
     * @access   public
     * @var      string
     */
    public $envelop_from = '';

    /**
     * 文字コード
     *
     * @access   public
     * @var      string
     */
    public $charset = '';

    /**
     * エンコーディング
     *
     * @access   public
     * @var      string
     */
    public $encoding = '';

    /**
     * 改行コード
     *
     * @access   public
     * @var      string
     */
    public $lineend = Etc_Mail_Mime::LINEEND;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct($charset = 'UTF-8', $encoding = '8bit', $lineend = Etc_Mail_Mime::LINEEND)
    {
        $this->setCharset($charset);
        $this->setEncoding($encoding);
        $this->lineend = $lineend;
    }





    /**
     * To:追加
     *
     * @access     public
     * @param      string  $mail   メールアドレス
     * @param      string  $name   名前
     */
    public function addTo($mail, $name = '')
    {
        $Address = $this->getAddress($mail, $name);
        $this->_to[] = $Address;
        return $Address;
    }

    /**
     * Cc:追加
     *
     * @access     public
     * @param      string  $mail   メールアドレス
     * @param      string  $name   名前
     */
    public function addCc($mail, $name = '')
    {
        $Address = $this->getAddress($mail, $name);
        $this->_cc[] = $Address;
        return $Address;
    }

    /**
     * Bcc:追加
     *
     * @access     public
     * @param      string  $mail   メールアドレス
     * @param      string  $name   名前
     */
    public function addBcc($mail, $name = '')
    {
        $Address = $this->getAddress($mail, $name);
        $this->_bcc[] = $Address;
        return $Address;
    }


    /**
     * 受取人を取得する
     * 特定のフィールドに限定する事も可能
     *
     * @access     public
     * @param      mixed   $field   フィールドの指定
     * @return     array
     */
    public function getRecipients($field = NULL)
    {
        $recipients = array();
        if($field === NULL){
            $recipients = array_merge($recipients, $this->getRecipients('to'));
            $recipients = array_merge($recipients, $this->getRecipients('cc'));
            $recipients = array_merge($recipients, $this->getRecipients('bcc'));
        } else {
            switch($field){
                case 'to':
                case 'cc':
                case 'bcc':
                    $recipients = $this->{'_' . $field};
                    break;
                default:
                    throw new Samurai_Exception('unkown field... -> ' . $field);
                    break;
            }
        }
        return $recipients;
    }
    
    
    /**
     * To:取得
     * 複数指定されている可能性があるので、indexを指定する必要もあり
     *
     * @access     public
     * @param      int     $index
     * @return     object  Etc_Mail_Address
     */
    public function getTo($index = 0)
    {
        return isset($this->_to[$index]) ? $this->_to[$index] : NULL;
    }

    /**
     * Cc:取得
     * 複数指定されている可能性があるので、indexを指定する必要もあり
     *
     * @access     public
     * @param      int     $index
     * @return     object  Etc_Mail_Address
     */
    public function getCc($index = 0)
    {
        return isset($this->_cc[$index]) ? $this->_cc[$index] : NULL;
    }

    /**
     * Bcc:取得
     * 複数指定されている可能性があるので、indexを指定する必要もあり
     *
     * @access     public
     * @param      int     $index
     * @return     object  Etc_Mail_Address
     */
    public function getBcc($index = 0)
    {
        return isset($this->_bcc[$index]) ? $this->_bcc[$index] : NULL;
    }



    /**
     * 送信者設定
     *
     * @access     public
     * @param      string  $mail           メールアドレス
     * @param      string  $name           名前
     * @param      string  $envelop_from   envelop from
     */
    public function setFrom($mail, $name = '', $envelop_from = NULL)
    {
        $Address = $this->getAddress($mail, $name);
        $this->_from = $Address;
        $this->_envelop_from = $envelop_from ? $envelop_from : $Address->mail;
        return $Address;
    }


    /**
     * 送信者の取得
     *
     * @access     public
     * @return     object  Etc_Mail_Address
     */
    public function getFrom()
    {
        return $this->_from;
    }



    /**
     * 件名の設定
     *
     * @access     public
     * @param      string  $subject   件名
     */
    public function setSubject($subject)
    {
        $subject = preg_replace("/[\r\n\t]/", ' ', $subject);
        $this->_subject = $subject;
    }


    /**
     * 件名を取得する
     *
     * @access     public
     * @param      boolean $encode   エンコードするかどうか
     * @return     string
     */
    public function getSubject($encode = false)
    {
        return $encode ? mb_encode_mimeheader($this->_subject, $this->charset) : $this->_subject ;
    }


    /**
     * 本文の設定
     *
     * @access     public
     * @param      mixed   $body   内容
     * @return     object  Etc_Mail_Mime_Part
     */
    public function setBody($body)
    {
        if(!is_object($body) || !$body instanceof Etc_Mail_Mime_Part){
            $body = (string)$body;
            $body = $this->createBody($body);
        }
        $this->_bodies[] = $body;
        return $body;
    }


    /**
     * 本文の作成
     *
     * @access     public
     * @param      string  $string   本文
     * @return     object  Etc_Mail_Mime_Part
     */
    public function createBody($string = '')
    {
        $Body = new Etc_Mail_Mime_Part($string);
        $Body->type = Etc_Mail_Mime::TYPE_TEXT;
        $Body->encoding = Etc_Mail_Mime::ENCODING_BASE64;
        $Body->charset = $this->charset;
        $Body->encoding = $this->encoding;
        return $Body;
    }


    /**
     * 本文の取得
     * 複数設定されている場合があるから、indexを指定する必要も
     *
     * @access     public
     * @param      int     $index
     * @return     object  Etc_Mail_Mime_Part
     */
    public function getBody($index = 0)
    {
        return isset($this->_bodies[$index]) ? $this->_bodies[$index] : NULL ;
    }


    /**
     * bodiesを取得する
     *
     * @access     public
     * @return     array   本文
     */
    public function getBodies()
    {
        return $this->_bodies;
    }



    /**
     * 添付ファイルの追加
     *
     * @access     public
     * @param      mixed   $attachment
     * @return     object  Etc_Mail_Mime_Part
     */
    public function addAttachment(Etc_Mail_Mime_Part $attachment)
    {
        $this->_attaches[] = $attachment;
        return $attachment;
    }


    /**
     * 添付ファイルの作成
     *
     * @access     public
     * @param      string  $string         バイナリ文字列
     * @param      string  $type
     * @param      string  $dispposition
     * @param      string  $encoding
     * @param      string  $filename       添付ファイル名
     */
    public function createAttachment($string, $type, $disposition = Etc_Mail_Mime::DISPOSITION_ATTACHMENT,
                                        $encoding = Etc_Mail_Mime::ENCODING_BASE64, $filename = 'attachment')
    {
        $attach = new Etc_Mail_Mime_Part($string);
        $attach->type = $type;
        $attach->disposition = $disposition;
        $attach->encoding = $encoding;
        $attach->filename = $filename;
        $this->addAttachment($attach);
        return $attach;
    }


    /**
     * 添付ファイルを取得する
     *
     * @access     public
     * @return     array
     */
    public function getAttaches()
    {
        return $this->_attaches;
    }





    /**
     * 送信トリガー
     *
     * @access     public
     * @param      mixed   $Transporter   Etc_Mail_Transporter
     */
    public function send($Transporter = NULL)
    {
        //Transporter補完
        if($Transporter === NULL || !$Transporter instanceof Etc_Mail_Transporter){
            $Transporter = $this->getTransporter();
        }
        //送信
        $Transporter->send($this);
        return $this;
    }





    /**
     * 文字コードをセットする
     *
     * @access     public
     * @param      string  $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * 文字コードを取得する
     *
     * @access     public
     * @return     string
     */
    public function getCharset()
    {
        return $this->charset;
    }


    /**
     * エンコーディングをセット
     *
     * @access     public
     * @param      string  $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * エンコーディングを取得
     *
     * @access     public
     * @return     string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }


    /**
     * ヘッダーをセットする
     *
     * @access     public
     * @param      string  $key     キー
     * @param      string  $value   値
     */
    public function setHeader($key, $value)
    {
        $this->_headers[strtolower($key)] = $value;
    }

    /**
     * ヘッダーを削除する
     *
     * @access     public
     * @param      string  $key
     */
    public function delHeader($key)
    {
        $key = strtolower($key);
        if(isset($this->_headers[$key])) unset($this->_headers[$key]);
    }

    /**
     * ヘッダーがあるかどうか確認する
     *
     * @access     public
     * @param      string  $key   キー
     * @return     boolean
     */
    public function hasHeader($key)
    {
        return isset($this->_headers[strtolower($key)]);
    }
    
    /**
     * ヘッダーを全て取得する
     *
     * @access     public
     * @return     array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }


    /**
     * ボディか添付ファイルかがセットされているかをチェックする
     *
     * @access     public
     * @return     boolean
     */
    public function hasContent()
    {
        return count($this->_bodies) > 0 || count($this->_attaches) > 0 ;
    }


    /**
     * メールアドレスのDTOを取得する
     *
     * @access     public
     * @param      string  $mail   メールアドレス
     * @param      string  $name   名前
     * @return     object  Etc_Mail_Address
     */
    public function getAddress($mail = NULL, $name = NULL)
    {
        if(!is_object($mail) || !$mail instanceof Etc_Mail_Address){
            $address = new Etc_Mail_Address();
            $address->mail = $mail;
            $address->name = $name;
        } else {
            $address = $mail;
        }
        return $address;
    }


    /**
     * 送信クラスの取得
     *
     * @access     public
     */
    public function getTransporter($transporter = 'mail')
    {
        try{
            $class = 'Etc_Mail_Transporter_' . ucfirst($transporter);
            Samurai_Loader::loadByClass($class);
            $Transporter = new $class();
        } catch(Samurai_Exception $E){
            Samurai_Logger::fatal('Transporter is not found. -> %s', $transporter);
        }
        return $Transporter;
    }


    /**
     * Mailコンポーネントの初期化
     *
     * @access     public
     */
    public function clear()
    {
        $this->_to = array();
        $this->_cc = array();
        $this->_bcc = array();
        $this->_from = NULL;
        $this->_headers = array();
        $this->_subject = '';
        $this->_bodies = array();
        $this->_attaches =array();
        $this->_envelop_from = '';
    }
}

