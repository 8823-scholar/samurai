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

Samurai_Loader::loadByClass('Samurai_Request_Parameter');

/**
 * GET/POSTで受け取った値を操作＆格納する
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Request_Http extends Samurai_Request_Parameter
{
    /**
     * ヘッダー情報
     *
     * @access   private
     * @var      array
     */
    private $_headers = array();


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        //COOKIEを取得したくないために、敢えて$_REQUESTで取得しない
        $request = array_merge($_GET, $_POST);
        //自動エスケープ処理を無効化
        if(get_magic_quotes_gpc()){
            array_walk_recursive($request, create_function('&$item', '$item = stripslashes($item);'));
        }
        //インポート
        $this->import($request);
        foreach(apache_request_headers() as $_key => $_val){
            $this->setHeader($_key, $_val);
        }
    }





    /**
     * REQUEST_METHODの値を返却
     *
     * @access    public
     * @return    string
     */
    public function getMethod()
    {
        if($method = $this->getParameter('_method')){
            $method = strtoupper((string)$method);
            if(Samurai_Config::get('restful.strict_method', 1) && !in_array($method, array('GET','POST','PUT','DELETE'))){
                $method = 'GET';
            }
        }
        return $method ? $method : $_SERVER['REQUEST_METHOD'] ;
    }


    /**
     * ヘッダーをセットする
     *
     * @access     public
     * @param      string   $key
     * @param      string   $value
     */
    public function setHeader($key, $value)
    {
        $this->_headers[strtolower($key)] = $value;
    }

    /**
     * リクエストheaderの取得
     *
     * @access     public
     * @param      string   $key
     * @param      string   $default
     */
    public function getHeader($key, $default = NULL)
    {
        $key = strtolower($key);
        $value = isset($this->_headers[$key]) ? $this->_headers[$key] : $default;
        return $value;
    }

    /**
     * すべてのヘッダーを取得する
     *
     * @access     public
     * @return     array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * headerが存在するかチェック
     *
     * @access     public
     * @param      string   $key
     * @return     boolean
     */
    public function hasHeader($key)
    {
        $key = strtolower($key);
        return isset($this->_headers[$key]);
    }



    /**
     * HTTPS通信かどうかを判断する
     *
     * @access     public
     * @return     boolean
     */
    public function isHttps()
    {
        if($function = Samurai_Config::get('etc.is_https_function')){
            return $function();
        } else {
            return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
        }
    }


    /**
     * HTTPバージョンを取得する
     *
     * @access     public
     */
    public function getHttpVersion()
    {
        $version = str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']);
        return (float)$version;
    }





    /**
     * Actionの強制変化
     *
     * フォーム内に二つのsubmitボタンがある場合、submitボタンのname属性でActionを切り分ける
     * <code>
     *    <input type="submit" name="dispatch_OK" value="OK">
     *    //⇒ Actionは「OK」になる
     *    <input type="submit" name="dispatch_NG" value="NG">
     *    //⇒ Actionは「NG」になる
     * </code>
     *
     * @access    public
     */
    public function dispatchAction()
    {
        //パラメータの取得
        $parameters = $this->getParameters();
        if(!count($parameters)) return;
        //Actionの割当て
        foreach($parameters as $_key => $_val){
            if(preg_match('/^dispatch_/', $_key)){
                $action = preg_replace('/^dispatch_/', '', $_key);
                $this->setParameter(Samurai_Config::get('action.request_key'), $action);
                Samurai_Logger::info('Dispatched Action -> %s.', $action);
            }
        }
    }





    /**
     * @override
     */
    public function import(array $parameters)
    {
        //文字コードの差異を修正
        if(!ini_get('mbstring.encoding_translation')
            && Samurai_Config::get('encoding.input') != Samurai_Config::get('encoding.internal')){
            mb_convert_variables(Samurai_Config::get('encoding.internal'), Samurai_Config::get('encoding.input'), $parameters);
        }
        parent::import($parameters);
    }
}

