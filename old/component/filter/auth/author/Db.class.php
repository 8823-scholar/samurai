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
 * DBによる認証をかけるAuthor
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Auth_Author_Db extends Filter_Auth_Author
{
    /**
     * 認証名(複数の認証情報を共存させるため)
     *
     * @access   public
     * @var      string
     */
    public $name = 'DB';

    /**
     * 認証を有効にするかどうかの値
     *
     * @access   public
     * @var      boolean
     */
    public $enable = true;

    /**
     * 認証の種類(session|cookie|request)
     *
     * @access   public
     * @var      string
     */
    public $authtype = 'session';

    /**
     * Sessionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Session;

    /**
     * Cookieコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Cookie;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;

    /**
     * Hashコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Hash;



    /**
     * @implements
     */
    public function authorize(array $params)
    {
        //初期化
        foreach($params as $_key => $_val){
            switch($_key){
                case 'name':
                case 'authtype':
                    $this->$_key = (string)$_val;
                    break;
                case 'enable':
                    $this->$_key = (bool)$_val;
                    break;
            }
        }
        //もし認証を無視する場合
        if(!$this->enable) return true;
        //認証
        if($this->authtype == 'session'){
            return $this->_authorizeBySession($params);
        }
        //クッキー認証
        elseif($this->authtype == 'cookie'){
            return $this->_authorizeByCookie($params);
        }
        //リクエスト認証
        else {
            return $this->_authorizeByRequest($params);
        }
    }


    /**
     * Sessionによる認証
     *
     * @access     private
     * @param      array   $params   認証条件
     * @return     mixed   認証結果
     */
    protected function _authorizeBySession($params)
    {
        $info = $this->_getInfoFromSession($this->name);
        //情報がない場合認証失敗
        if(!$info){
            $this->_clearInfoFromCookie($this->name);
            $this->_clearInfoFromSession($this->name);
            Samurai_Logger::debug('Failed authorize... -> no information');
            return Samurai_Config::get('error.login');
        //認証通過！
        } else {
            Samurai_Logger::debug('Success authorize!');
            return true;
        }
    }


    /**
     * Cookieによる認証
     *
     * @access     private
     * @param      string  $params   認証条件
     * @return     mixed   認証結果
     */
    protected function _authorizeByCookie($params)
    {
        $info = $this->_getInfoFromCookie($this->name);
        //情報が欠けている場合
        if(!$info['user_id'] || !$info['hash']){
            $this->_clearInfoFromCookie($this->name);
            $this->_clearInfoFromSession($this->name);
            Samurai_Logger::debug('Failed authorize... -> no information');
            return Samurai_Config::get('error.login');
        //情報に誤りがある場合
        } elseif($info['hash'] != $this->_getSequreKey($info['user_id'])){
            $this->_clearInfoFromCookie($this->name);
            $this->_clearInfoFromSession($this->name);
            Samurai_Logger::debug('Failed authorize... -> worong parameter');
            return Samurai_Config::get('error.login');
        //認証通過！
        } else {
            Samurai_Logger::debug('Success authorize!');
            return true;
        }
    }


    /**
     * Requestによる認証
     *
     * @access     private
     * @param      array   認証条件
     */
    protected function _authorizeByRequest($params)
    {
        //初期化
        $use     = 'session';
        $dsn     = 'user';
        $table   = 'user';
        $field   = array('user' => 'user', 'pass' => 'pass', 'id' => 'id');
        $hash    = 'md5';
        $expire  = 60 * 60 * 24 * 1;
        $hold    = false;
        $request = array('user' => 'user', 'pass' => 'pass', 'hold' => 'hold');
        $cookie_path = '/';
        $error_message = 'ID、もしくはパスワードに誤りがあります。';
        foreach($params as $_key => $_val){
            switch($_key){
                case 'name':
                case 'use':
                case 'dsn':
                case 'table':
                case 'hash':
                case 'error_message':
                    $$_key = trim((string)$_val);
                    break;
                case 'expire':
                    $$_key = (int)$_val;
                    break;
                case 'hold':
                    $$_key = (bool)$_val;
                    break;
                case 'field':
                case 'request':
                    $$_key = array_merge($$_key, (array)$_val);
                    break;
            }
        }
        //ブラウザを閉じたあとも認証を保持するかどうかの判断値。
        $hold = $this->Request->getParameter($request['hold']) ? true : false ;
        if(!$hold) $expire = NULL;
        //DB検索
        $AG = $this->_getActiveGateway($dsn);
        $AGdto = ActiveGateway::getCondition();
        $AGdto->where->{$field['user']} = $this->Request->getParameter($request['user']);
        $AGdto->where->{$field['pass']} = $this->_toHash($this->Request->getParameter($request['pass']), $hash);
        $User = $AG->findDetail($table, $AGdto);
        //取得できない場合
        if(!$User){
            $this->_clearInfoFromSession($this->name);
            $this->_clearInfoFromCookie($this->name);
            return array(Samurai_Config::get('error.auth'), $error_message);
        //認証通過！
        } else {
            switch(strtolower($use)){
                case 'session':
                    $this->_setInfoToSession($this->name, $User->{$field['id']}, $expire, $cookie_path);
                    break;
                case 'cookie':
                    $this->_setInfoToCookie($this->name, $User->{$field['id']}, $expire, $cookie_path);
                    break;
            }
            return true;
        }
    }





    /**
     * ActiveGateway取得
     *
     * @access     protected
     * @param      string   $dsn   DSNエイリアス
     * @return     object   ActiveGateway
     */
    protected function _getActiveGateway($dsn)
    {
        $ActiveGatewayManager = ActiveGatewayManager::singleton();
        $ActiveGateway = $ActiveGatewayManager->getActiveGateway($dsn);
        return $ActiveGateway;
    }


    /**
     * セッションから情報を取得
     *
     * @access     protected
     * @param      string  $namespace   認証名前空間
     * @return     array
     */
    protected function _getInfoFromSession($namespace)
    {
        if(!$this->Device->isMobile() && !$this->Cookie->getParameter('SAMURAI_FILTER_AUTH.' . $namespace.'.hold')){
            return NULL;
        } else {
            return $this->Session->getParameter('SAMURAI_FILTER_AUTH.' . $namespace);
        }
    }


    /**
     * セッションに情報を埋め込む
     *
     * @access     protected
     * @param      string  $namespace    認証名前空間
     * @param      int     $user_id      ユーザーID
     * @param      int     $expire       認証の有効期限(NULLの場合、ブラウザを閉じるまで)
     */
    protected function _setInfoToSession($namespace, $user_id, $expire, $cookie_path)
    {
        //クッキーに持続情報の埋め込み
        $this->Cookie->setParameter('SAMURAI_FILTER_AUTH.' . $namespace . '.hold', 1, $expire, $cookie_path);
        //セッションに認証情報の埋め込み
        $this->Session->setParameter('SAMURAI_FILTER_AUTH.' . $namespace . '.user_id', $user_id);
        $this->Session->setParameter('SAMURAI_FILTER_AUTH.' . $namespace . '.expire', $expire);
    }


    /**
     * セッションの値を削除する
     *
     * @access     protected
     * @param      string  $namespace   認証名前空間
     */
    protected function _clearInfoFromSession($namespace)
    {
        $this->Session->delParameter('SAMURAI_FILTER_AUTH.' . $namespace);
    }





    /**
     * クッキーから値の取得
     *
     * @access     protected
     * @param      string  $namespace   認証名前空間
     * @return     array
     */
    protected function _getInfoFromCookie($namespace)
    {
        if(!$this->Cookie->getParameter('SAMURAI_FILTER_AUTH.' . $namespace . '.hold')){
            return NULL;
        } else {
            return $this->Cookie->getParameter('SAMURAI_FILTER_AUTH.' . $namespace);
        }
    }


    /**
     * クッキーに情報を埋め込む
     *
     * @access     protected
     * @param      string  $namespace    認証名前空間
     * @param      int     $user_id      ユーザーID
     * @param      int     $expire       クッキーの有効期限(NULLの場合、ブラウザを閉じるまで)
     */
    protected function _setInfoToCookie($namespace, $user_id, $expire, $cookie_path)
    {
        $info = array('user_id' => $user_id, 'hash' => $this->_getSequreKey($user_id));
        $this->Cookie->setParameter('SAMURAI_FILTER_AUTH.' . $namespace, $info, $expire, $cookie_path);
    }


    /**
     * クッキーの情報を消去する
     *
     * @access     protected
     * @param      string  $namespace   認証名前空間
     */
    protected function _clearInfoFromCookie($namespace)
    {
        $this->Cookie->delParameter('SAMURAI_FILTER_AUTH.' . $namespace);
    }





    /**
     * 暗号化文字列を取得する
     *
     * @access     protected
     * @param      string  $string   対象文字列
     * @param      string  $hash     暗号タイプ
     * @return     string
     */
    protected function _toHash($string, $hash='md5')
    {
        if(!$string) return '';
        switch(strtolower($hash)){
            case 'md5':
                return $this->Hash->md5($string); break;
            case 'blowfish':
                return $this->Hash->bfEncrypt($string); break;
        }
        return $string;
    }


    /**
     * ユーザー一意のセキュアキー生成
     *
     * @access     protected
     * @param      int     $id      ユーザー一意のID
     * @param      mixed   $extra   付加情報
     * @return     string
     */
    protected function _getSequreKey($id, $extra='extra')
    {
        //TODO: Deviceコンポーネントを利用した一意のIDの生成
        //return $this->_getHash(sprintf("%s_%s_%s", $id, $extra, $this->Device->getSerial()), "md5");
        return $this->_toHash('TODO');
    }
}

