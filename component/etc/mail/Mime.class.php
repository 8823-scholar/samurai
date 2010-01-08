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
 * Etc_MailでMimeメールを送るためのクラス
 * 
 * @package    Samurai
 * @subpackage Etc.Mail
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Mail_Mime
{
    //const
    const TYPE_OCTETSTREAM = 'application/octet-stream';
    const TYPE_TEXT = 'text/plain';
    const TYPE_HTML = 'text/html';
    const ENCODING_7BIT = '7bit';
    const ENCODING_8BIT = '8bit';
    const ENCODING_QUOTEDPRINTABLE = 'quoted-printable';
    const ENCODING_BASE64 = 'base64';
    const DISPOSITION_ATTACHMENT = 'attachment';
    const DISPOSITION_INLINE = 'inline';
    const LINELENGTH = 74;
    const LINEEND = "\n";
    const MULTIPART_ALTERNATIVE = 'multipart/alternative';
    const MULTIPART_MIXED = 'multipart/mixed';
    const MULTIPART_RELATED = 'multipart/related';

    /**
     * sjis-winなどのエンコーディングを使用するかどうか
     *
     * @access   public
     * @var      boolean
     */
    public static $encoding_win = false;


    /**
     * コンストラクタ
     *
     * @access     private
     */
    private function __construct()
    {
        
    }



    /**
     * エンコード
     *
     * @access     public
     * @param      string  $string     文字列
     * @param      string  $encoding   エンコード種類
     * @return     string
     */
    public static function encode($string, $encoding = 'mimeheader', $charset = 'UTF-8')
    {
        switch($encoding){
            case self::ENCODING_BASE64:
                $string = self::encodeBase64($string);
                break;
            case self::ENCODING_7BIT:
            case self::ENCODING_8BIT:
                $string = self::encodeBit($string, $charset);
                break;
            case self::ENCODING_QUOTEDPRINTABLE:
                $string = self::encodeQuotedPrintable($string);
                break;
            case 'mimeheader':
                $string = mb_encode_mimeheader($string);
                break;
        }
        return $string;
    }


    /**
     * base64エンコード
     *
     * @access     public
     * @param      string  $string   文字列
     * @return     string
     */
    public static function encodeBase64($string, $linelength = self::LINELENGTH, $lineend = self::LINEEND)
    {
        $string = rtrim(chunk_split(base64_encode($string), $linelength, $lineend));
        return $string;
    }


    /**
     * bitエンコード
     *
     * @access     public
     * @param      string  $string    文字列
     * @param      string  $charset   文字コード
     * @return     string
     */
    public static function encodeBit($string, $charset)
    {
        return mb_convert_encoding($string, $charset, Samurai_Config::get('encoding.internal'));
    }


    /**
     * quoted-printableエンコード
     *
     * @access     public
     * @param      string  $string   文字列
     * @return     string
     */
    public static function encodeQuotedPrintable($string)
    {
        return $string;
    }



    /**
     * デコード
     *
     * @access     public
     * @param      string  $string     文字列
     * @param      string  $encoding   エンコード種類
     * @return     string
     */
    public static function decode($string, $encoding = 'mimeheader', $charset = 'UTF-8')
    {
        if(!is_string($string) || $string == '') return $string;
        switch($encoding){
            case self::ENCODING_BASE64:
                $string = self::decodeBase64($string);
                break;
            case self::ENCODING_7BIT:
            case self::ENCODING_8BIT:
                $string = self::decodeBit($string, $charset);
                break;
            case self::ENCODING_QUOTEDPRINTABLE:
                $string = self::decodeQuotedPrintable($string);
                break;
            case 'mimeheader':
                $string = mb_decode_mimeheader($string);
                break;
            default:
                break;
        }
        return $string;
    }


    /**
     * base64デコード
     *
     * @access     public
     * @param      string  $string   文字列
     * @return     string
     */
    public static function decodeBase64($string)
    {
        $string = base64_decode($string);
        return $string;
    }


    /**
     * bitデコード
     *
     * @access     public
     * @param      string  $string    文字列
     * @param      string  $charset   文字コード
     * @return     string
     */
    public static function decodeBit($string, $charset)
    {
        if(!$charset){
            return mb_convert_encoding($string, Samurai_Config::get('encoding.internal'));
        } else {
            if(self::$encoding_win){
                if(strtolower($charset) == 'shift_jis'){
                    $charset = 'SJIS-WIN';
                }
                elseif(strtolower($charset) == 'iso-2022-jp'){
                    $string = mb_convert_encoding($string, 'Shift_JIS', $charset);
                    $charset = 'SJIS-WIN';
                }
            }
            return mb_convert_encoding($string, Samurai_Config::get('encoding.internal'), $charset);
        }
    }


    /**
     * quoted-printableデコード
     *
     * @access     public
     * @param      string  $string   文字列
     * @return     string
     */
    public static function decodeQuotedPrintable($string)
    {
        return $string;
    }
}

