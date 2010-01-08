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

Samurai_Loader::loadByClass('Samurai_Response_Http_Body');

/**
 * 出力を担当するクラス
 *
 * HTTPクライアントに対して基本的な出力を行います
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Response_Http
{
    /**
     * Sessionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Session;

    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * HTTPバージョン
     *
     * @access   private
     * @var      string
     */
    private $_version = '1.1';

    /**
     * ステータスコード
     *
     * @access   private
     * @var      string
     */
    private $_status = '200';

    /**
     * ヘッダー
     *
     * @access   private
     * @var      array
     */
    private $_headers = array();

    /**
     * 出力body(multipartできるように配列)
     *
     * @access   private
     * @var      array
     */
    private $_bodys = array();

    /**
     * statusメッセージ定数群
     *
     * @access   public
     * @const    string
     */
    const
        //情報提供系
        STATUS_100 = 'Continue',
        STATUS_101 = 'Switching Protocols',
        STATUS_102 = 'Processing',
        //成功系
        STATUS_200 = 'OK',
        STATUS_201 = 'Created',
        STATUS_202 = 'Accepted',
        STATUS_203 = 'Non-Authoritative Information',
        STATUS_204 = 'No Content',
        STATUS_205 = 'Reset Content',
        STATUS_206 = 'Partial Content',
        STATUS_207 = 'Multi-Status',
        STATUS_226 = 'IM Used',
        //転送系
        STATUS_300 = 'Multiple Choices',
        STATUS_301 = 'Moved Permanently',
        STATUS_302 = 'Found',
        STATUS_303 = 'See Other',
        STATUS_304 = 'Not Modified',
        STATUS_305 = 'Use Proxy',
        STATUS_307 = 'Temporary Redirect',
        //クライアントエラー系
        STATUS_400 = 'Bad Request',
        STATUS_401 = 'Unauthorized',
        STATUS_402 = 'Payment Required',
        STATUS_403 = 'Forbidden',
        STATUS_404 = 'Not Found',
        STATUS_405 = 'Method Not Allowed',
        STATUS_406 = 'Not Acceptable',
        STATUS_407 = 'Proxy Authentication Required',
        STATUS_408 = 'Request Timeout',
        STATUS_409 = 'Conflict',
        STATUS_410 = 'Gone',
        STATUS_411 = 'Length Required',
        STATUS_412 = 'Precondition Failed',
        STATUS_413 = 'Request Entity Too Large',
        STATUS_414 = 'Request-URI Too Long',
        STATUS_415 = 'Unsupported Media Type',
        STATUS_416 = 'Requested Range Not Satisfiable',
        STATUS_417 = 'Expectation Failed',
        STATUS_418 = 'I\'m a teapot',
        STATUS_422 = 'Unprocessable Entity',
        STATUS_423 = 'Locked',
        STATUS_424 = 'Failed Dependency',
        STATUS_426 = 'Upgrade Required',
        //サーバーエラー系
        STATUS_500 = 'Internal Server Error',
        STATUS_501 = 'Not Implemented',
        STATUS_502 = 'Bad Gateway',
        STATUS_503 = 'Service Unavailable',
        STATUS_504 = 'Gateway Timeout',
        STATUS_505 = 'HTTP Version Not Supported',
        STATUS_506 = 'Variant Also Negotiates',
        STATUS_507 = 'Insufficient Storage',
        STATUS_510 = 'Not Extended';


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * HTTPバージョンの設定
     *
     * @access     public
     * @param      string  $version   HTTPバージョン
     */
    public function setVersion($version)
    {
        $this->_version = $version;
    }


    /**
     * ステータスコードの設定
     *
     * @access     public
     * @param      string  $status   ステータスコード
     */
    public function setStatus($status)
    {
        $this->_status = $status;
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
     * ヘッダーがセットされているかどうか
     *
     * @access     public
     * @param      string  $key   キー
     * @return     boolean
     */
    public function hasHeader($key)
    {
        $key = strtolower($key);
        return isset($this->_headers[$key]);
    }


    /**
     * bodyを取得する
     *
     * @access     public
     * @return     object
     */
    public function getBody()
    {
        return isset($this->_bodys[0]) ? $this->_bodys[0] : NULL ;
    }


    /**
     * Body値をセット
     *
     * @access     public
     * @param      string | object   $body   メッセージ本文 | Samurai_Response_Http_Body
     * @return     object   Samurai_Response_Http_Body
     */
    public function setBody($body = NULL)
    {
        $this->_bodys = array();
        if(is_string($body)){
            $body = new Samurai_Response_Http_Body($body);
            $this->_bodys[0] = $body;
        } elseif($body instanceof Samurai_Response_Http_Body){
            $this->_bodys[0] = $body;
        } else {
            $body = new Samurai_Response_Http_Body($body);
            $this->_bodys[0] = $body;
        }
        return $body;
    }


    /**
     * redirectの値をセット
     *
     * @access    public
     * @param     string  $url   URL
     */
    public function setRedirect($url)
    {
        $this->setStatus('303');
        if($this->Request->getHttpVersion() == 1.0) $this->setStatus('302');
        if($this->Device->isMobile()){
            if($this->Device->isImode()){
                $this->setStatus('302');
            }
            if($this->Session){
                if($this->Request->getParameter('guid')){
                    if(!preg_match('/(\?|&)guid=ON/i', $url)){
                        $url .= sprintf('%sguid=ON', strpos($url, '?') !== false ? '&' : '?');
                    }
                } else {
                    if(!preg_match('/(\?|&)'.preg_quote($this->Session->name()).'=/', $url)){
                        $url .= sprintf('%s%s=%s', strpos($url, '?') ? '&' : '?', $this->Session->name(), $this->Session->id());
                    }
                }
            }
        }
        $this->setHeader('location', $url);
    }





    /**
     * 出力を実行する
     *
     * @access     public
     */
    public function execute()
    {
        //ヘッダー補完
        $this->setHeader('date', date('r'));
        $this->setHeader('x-php-framework', 'Samurai/' . Samurai::VERSION . '; extends Maple3');
        //ヘッダーの送出
        $this->sendStatusCode($this->_status);
        $this->sendHeaders($this->_headers);
        if($this->isMultipulBody()){
            $this->sendBody4Mutipul($this->_bodys);
        } elseif($this->hasBody()){
            $this->sendBody($this->_bodys[0]);
        }
    }


    /**
     * ステータスコードの送出
     *
     * @access     public
     * @param      int     $status_code   ステータスコード
     * @return     boolean
     */
    public function sendStatusCode($status_code)
    {
        //既に送出済みの場合
        if(headers_sent()) return false;
        //コードの送出
        $_const = 'Samurai_Response_Http::STATUS_' . $status_code;
        if(defined($_const)){
            header(sprintf('HTTP/%s %s %s', $this->Request->getHttpVersion(), $status_code, constant($_const)));
        } else {
            header('Status: ' . $status_code);
        }
        return true;
    }


    /**
     * ヘッダーの送出
     *
     * @access     public
     * @param      array   $headers   ヘッダー
     */
    public function sendHeaders(array $headers = array())
    {
        foreach($headers as $_key => $_val){
            header(sprintf('%s: %s', $_key, $_val));
        }
    }


    /**
     * ボディの送出
     *
     * @access     public
     * @param      object  $Body   Samurai_Response_Http_Body
     */
    public function sendBody(Samurai_Response_Http_Body $Body)
    {
        $this->sendHeaders($Body->getHeaders());
        echo $Body->getBody();
    }





    /**
     * メッセージボディが複数設定されているかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function isMultipulBody()
    {
        return count($this->_bodys) > 1;
    }


    /**
     * メッセージボディを保持しているかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function hasBody()
    {
        return count($this->_bodys) > 0;
    }
}

