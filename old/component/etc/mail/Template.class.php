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

Samurai_Loader::loadByClass('Etc_Mail_Mime_Decoder');

/**
 * メール用テンプレートクラス
 *
 * Rendererを利用して、メール送信にテンプレートを使用可能にする
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @see        Samurai_Rednerer
 * @see        Etc_Mail_Mime_Decoder
 */
class Etc_Mail_Template
{
    /**
     * Etc_Mail_Mime_Decoder
     *
     * @access   public
     * @var      object
     */
    public $Decoder;

    /**
     * Rednererコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Renderer;

    /**
     * メール本文
     *
     * @access   private
     * @var      string
     */
    private $_mail_text;

    /**
     * テンプレートを格納しているディレクトリ
     *
     * @access   private
     * @var      string
     */
    private $_template_dir;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        $this->Decoder = new Etc_Mail_Mime_Decoder();
    }



    /**
     * 変数を割り当てる
     *
     * @access     public
     * @param      string  $key     キー
     * @param      mixed   $value   値
     */
    public function assign($key, $value)
    {
        $this->Renderer->assign($key, $value);
    }


    /**
     * テンプレートディレクトリを設定する
     *
     * @access     public
     * @param      string  $dir
     */
    public function setTemplateDir($dir)
    {
        $this->_template_dir = $dir;
    }


    /**
     * 指定されたテンプレートを描画してデコードする
     *
     * @access     public
     * @param      string  $template   テンプレート
     * @return     object  Etc_Mail
     */
    public function render($template)
    {
        try {
            $_template_dir1 = Samurai_Config::get('directory.template');
            $_template_dir2 = $this->Renderer->Engine->template_dir;
            $this->Renderer->Engine->template_dir = $this->_template_dir ? $this->_template_dir : $_template_dir2;
            Samurai_Config::set('directory.template', $this->Renderer->Engine->template_dir);
            $this->_mail_text = $this->_fetch($template);
            $Mail = $this->_decode($this->_mail_text);
            $this->Renderer->Engine->template_dir = $_template_dir2;
            Samurai_Config::set('directory.template', $_template_dir1);
            return $Mail;
        } catch(Exception $E){
            Samurai_Config::set('directory.template', $_template_dir);
            throw $E;
        }
    }


    /**
     * renderと似ているが、renderがMailコンポーネントを返却するのに対して、
     * attachは、渡されたMailコンポーネントに情報を上書きする
     *
     * @access     public
     * @param      object  $Mail   Etc_Mail
     * @param      string  $template
     */
    public function attach(Etc_Mail $Mail, $template)
    {
        $Mail2 = $this->render($template);
        $Mail->setSubject($Mail2->getSubject());
        $Mail->setBody($Mail2->getBody()->content);
        if($from = $Mail2->getFrom()){
            $Mail->setFrom($from->mail, $from->name);
        }
        if(!$this->_hasHeader('content-type', $this->_mail_text)) $Mail2->delHeader('content-type');
        if(!$this->_hasHeader('content-transfer-encoding', $this->_mail_text)) $Mail2->delHeader('content-transfer-encoding');
        foreach($Mail2->getHeaders() as $_key => $_val){
            $Mail->setHeader($_key, $_val);
        }
    }


    /**
     * テンプレートファイルを解釈する
     *
     * @access     public
     * @param      string  $template   テンプレート
     */
    public function _fetch($template)
    {
        $mail_text = $this->Renderer->render($template);
        return $mail_text;
    }


    /**
     * メール本文をデコード
     *
     * @access     private
     * @param      string  $mail_text   メール本文
     */
    private function _decode($mail_text)
    {
        $this->_adjustText($mail_text);
        $Mail = $this->Decoder->decode($mail_text);
        return $Mail;
    }


    /**
     * メール本文を調節する(Content-Typeの付加など)
     *
     * @access     private
     * @param      string  &$mail_text
     */
    private function _adjustText(&$mail_text)
    {
        //エンコード
        $this->_encodeHeader($mail_text);
        //Content-Typeの付加
        if(!$this->_hasHeader('content-type', $mail_text)){
            $content_type = sprintf('%s; charset="%s"', Etc_Mail_Mime::TYPE_TEXT, Samurai_Config::get('encoding.template'));
            $this->_intractHeader('content-type', $content_type, $mail_text);
        }
        //Content-Transfer-Encodingの付加
        if(!$this->_hasHeader('content-transfer-encoding', $mail_text)){
            $this->_intractHeader('content-transfer-encoding', Etc_Mail_Mime::ENCODING_8BIT, $mail_text);
        }
    }


    /**
     * ヘッダー部分をエンコードする
     *
     * @access     private
     * @param      string  &$mail_text   メール本文
     */
    private function _encodeHeader(&$mail_text)
    {
        $headers = array();
        if($this->_existsHeader($mail_text)){
            list($header, $mail_text) = $this->_splitText($mail_text);
            $headers = explode(Etc_Mail_Mime_Part::LINEEND, $header);
        }
        foreach($headers as $_i => $header){
            list($_key, $_value) = preg_split('/\s*:\s*/', trim($header));
            $headers[$_i] = sprintf('%s : %s', $_key, Etc_Mail_Mime::encode($_value));
        }
        $header = join(Etc_Mail_Mime_Part::LINEEND, $headers);
        $mail_text = join(Etc_Mail_Mime_Part::LINEEND, array($header, '', $mail_text));
    }


    /**
     * メール本文が指定のヘッダーらしきものを保持しているかどうか
     *
     * @access     private
     * @param      string  $key         キー
     * @param      string  $mail_text   メール本文
     * @return     boolean
     */
    private function _hasHeader($key, $mail_text)
    {
        if($this->_existsHeader($mail_text)){
            list($header, $body) = $this->_splitText($mail_text);
            foreach(explode(Etc_Mail_Mime_Part::LINEEND, $header) as $header){
                $headers = preg_split('/\s*:\s*/', trim($header));
                if(strtolower($key) == strtolower($headers[0])){
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * ヘッダー部分が存在するのかどうか
     *
     * @access     private
     * @param      string  $mail_text   メール本文
     * @return     boolean
     */
    private function _existsHeader($mail_text)
    {
        list($header) = $this->_splitText($mail_text);
        $headers = explode(Etc_Mail_Mime_Part::LINEEND, $header);
        foreach($headers as $header){
            if(!preg_match('/^\s+/', $header) && !preg_match('/:/', $header)){
                return false;
            }
        }
        return (bool)$headers;
    }


    /**
     * ヘッダーを埋め込む
     *
     * @access     private
     * @param      string  $key          キー
     * @param      mixed   $value        値
     * @param      string  &$mail_text   メール本文
     */
    private function _intractHeader($key, $value, &$mail_text)
    {
        $headers = array();
        if($this->_existsHeader($mail_text)){
            list($header, $mail_text) = $this->_splitText($mail_text);
            $headers = explode(Etc_Mail_Mime_Part::LINEEND, $header);
        }
        array_push($headers, "{$key} : {$value}");
        $header = join(Etc_Mail_Mime_Part::LINEEND, $headers);
        $mail_text = join(Etc_Mail_Mime_Part::LINEEND, array($header, '', $mail_text));
    }


    /**
     * メール本文をヘッダー部分と、本文部分に分ける
     *
     * @access     private
     * @param      string  $mail_text
     * @return     array   ヘッダー部分と本文部分
     */
    private function _splitText($mail_text)
    {
        $mail_text = preg_replace("/\r\n/", "\n", $mail_text);
        $mail_text = preg_replace("/\r/", "\n", $mail_text);
        $mail_text = preg_replace("/\n/", "\r\n", $mail_text);
        $texts = explode(Etc_Mail_Mime_Part::LINEEND.Etc_Mail_Mime_Part::LINEEND, $mail_text, 2);
        if(count($texts) < 2) array_unshift($texts, '');
        return $texts;
    }
}

