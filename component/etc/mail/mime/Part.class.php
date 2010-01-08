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
 * MailコンポーネントのMime_Partクラス
 *
 * Mime_PartはMime_Partを再帰的に格納する事が可能です
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Mail_Mime_Part
{
    /**
     * 内容
     *
     * @access   public
     * @var      string
     */
    public $content;

    /**
     * Content-Type
     *
     * @access   public
     * @var      string
     */
    public $type = Etc_Mail_Mime::TYPE_OCTETSTREAM;

    /**
     * Encoding
     *
     * @access   public
     * @var      string
     */
    public $encoding = Etc_Mail_Mime::ENCODING_7BIT;

    /**
     * 文字コード
     *
     * @access   public
     * @var      string
     */
    public $charset;

    /**
     * Content-Disposition
     *
     * @access   public
     * @var      string
     */
    public $disposition;

    /**
     * ファイル名
     *
     * @access   public
     * @var      string
     */
    public $filename;

    /**
     * ID
     *
     * @access   public
     * @var      string
     */
    public $id;

    /**
     * バウンダリー
     *
     * @access   public
     * @var      string
     */
    public $boundary;

    /**
     * 子Etc_Mail_Mime_Part格納
     *
     * @access   private
     * @var      array
     */
    private $_parts = array();

    /**
     * ヘッダー格納
     *
     * @access   private
     * @var      array
     */
    private $_headers = array();

    /**
     * 改行コード
     *
     * @const    string
     */
    const LINEEND = "\r\n";


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct($content=NULL)
    {
        $this->content = $content;
        $this->boundary = 'BOUNDARY_' . md5(uniqid(rand(), true));
    }



    /**
     * パートを追加する
     *
     * @access     public
     * @param      object  $Part   Etc_Mail_Mime_Part
     */
    public function addPart(Etc_Mail_Mime_Part $Part)
    {
        $this->_parts[] = $Part;
        if($this->isMultipart()){
            if($Part->type == Etc_Mail_Mime::TYPE_HTML){
                $this->type = Etc_Mail_Mime::MULTIPART_ALTERNATIVE;
            } else {
                $this->type = Etc_Mail_Mime::MULTIPART_MIXED;
            }
        }
    }


    /**
     * パートを取得する
     *
     * @access     public
     * @param      int      $index
     */
    public function fetchPart($pindex = NULL)
    {
        static $index = 0;
        if($pindex) $index = $pindex - 1;
        if(isset($this->_parts[$index])){
            $Part = $this->_parts[$index];
            $index++;
            return $Part;
        } else {
            $index = 0;
            return NULL;
        }
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
        $key = strtolower($key);
        $this->_headers[$key] = $value;
    }

    /**
     * ヘッダーを取得する
     *
     * @access     public
     * @param      string  $key   キー
     * @return     string
     */
    public function getHeader($key)
    {
        $key = strtolower($key);
        return $this->hasHeader($key) ? $this->_headers[$key] : NULL ;
    }

    /**
     * ヘッダーを削除する
     *
     * @access     public
     * @param      string  $key   キー
     */
    public function delHeader($key)
    {
        $key = strtolower($key);
        if(isset($this->_headers[$key])) unset($this->_headers[$key]);
    }

    /**
     * ヘッダーがあるかどうか
     *
     * @access     public
     * @param      string  $key   キー
     * @return     boolean
     */
    public function hasHeader($key)
    {
        return isset($this->_headers[$key]);
    }


    /**
     * ヘッダーを全て取得する
     *
     * @access     public
     * @return     array   headers
     */
    public function getHeaders()
    {
        $headers = array();
        if($this->isMultipart()){
            $headers['content-type'] = $this->type.'; boundary="'.$this->boundary.'"';
            if($this->encoding){
                $headers['content-transfer-encoding'] = $this->encoding;
            }
        } else {
            $Part = $this->_parts ? $this->_parts[0] : $this ;
            $headers['content-type'] = $Part->type;
            if($Part->charset){
                $headers['content-type'] .= '; charset="'.$Part->charset.'"';
            }
            if($Part->filename){
                $headers['content-type'] .= '; name="'.Etc_Mail_Mime::encode($Part->filename).'"';
            }
            if($Part->disposition){
                $headers['content-disposition'] = Etc_Mail_Mime::encode($Part->disposition);
                if($Part->filename){
                    $headers['content-disposition'] .= '; filename="'.Etc_Mail_Mime::encode($Part->filename).'"';
                }
            }
            if($Part->encoding){
                $headers['content-transfer-encoding'] = $Part->encoding;
            }
            if($Part->id){
                $headers['content-id'] = '<'.$Part->id.'>';
            }
        }
        $headers = array_merge($headers, $this->_headers);
        return $headers;
    }


    /**
     * 内容を取得する
     *
     * @access     public
     * @return     string  メッセージボディ
     */
    public function getContent()
    {
        if($this->isMultipart()){
            $contents = array();
            foreach($this->_parts as $Part){
                $contents[] = '--' . $this->boundary;
                foreach($Part->getHeaders(true) as $key => $value){
                    $key = join('-', array_map('ucfirst', explode('-', $key)));
                    $contents[] = "{$key}: {$value}";
                }
                $contents[] = '';
                $contents[] = $Part->getContent();
            }
            $contents[] = '--' . $this->boundary . '--';
            return join(self::LINEEND, $contents);
        } else {
            $Part = $this->_parts ? $this->_parts[0] : $this ;
            return Etc_Mail_Mime::encode($Part->content, $Part->encoding, $Part->charset);
        }
    }


    /**
     * contentに1行追加する
     * またその際に、sprintfがかけられる
     *
     * @access     public
     * @param      string  $line
     */
    public function addLine($line)
    {
        $args = func_get_args();
        $line = call_user_func_array('sprintf', $args);
        $this->content .= $line;
        $this->content .= Etc_Mail_Mime_Part::LINEEND;
    }





    /**
     * ストリーム判断
     *
     * @access     public
     * @return     boolean
     */
    public function isStream()
    {
        return is_resource($this->content);
    }


    /**
     * マルチかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function isMultipart()
    {
        return count($this->_parts) > 1 || preg_match('|^multipart/.+|', $this->type);
    }


    /**
     * テキストかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function isText()
    {
        return preg_match('|^text/.+|', $this->type);
    }
}

