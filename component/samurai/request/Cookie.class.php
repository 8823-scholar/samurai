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
 * クッキー値を操作＆格納する
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Request_Cookie extends Samurai_Request_Parameter
{
    /**
     * 有効パス
     *
     * @access   private
     * @var      string
     */
    private $_path;

    /**
     * 有効ドメイン
     *
     * @access   private
     * @var      string
     */
    private $_domain;

    /**
     * sequreフラグ
     *
     * @access   private
     * @var      int
     */
    private $_sequre;


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        $cookie = $_COOKIE;
        //自動エスケープ処理を無効化
        if(get_magic_quotes_gpc()){
            array_walk_recursive($cookie, create_function('&$item', '$item = stripslashes($item);'));
        }
        //文字コードの差異を修正
        if(!ini_get('mbstring.encoding_translation')
            && Samurai_Config::get('encoding.input') != Samurai_Config::get('encoding.internal')){
            mb_convert_variables(Samurai_Config::get('encoding.internal'), Samurai_Config::get('encoding.input'), $cookie);
        }
        //インポート
        $this->import($cookie);
    }





    /**
     * Cookieの値を格納
     *
     * 注意すべき点は、expire(有効期限)の値です
     * 本来のsetcookie関数では、現在の時間も含めた有効期限を指定しなければなりませんが、
     * このメソッドでは、常に現在のUNIX時間に加算されます
     * その結果、削除ができなくなりますが、その場合、delParameterをご使用下さい
     *
     * @access     public
     * @param      string  $key          パラメータ名
     * @param      mixed   $value        パラメータの値
     * @param      int     $expire       有効期限
     * @param      string  $path         パス
     * @param      string  $domain       ドメイン
     * @param      int     $sequre       セキュア
     */
    public function setParameter($key, $value, $expire = NULL, $path = NULL, $domain = NULL, $sequre = NULL)
    {
        //配列が渡された場合の配慮
        if(is_array($value)){
            foreach($value as $_key => $_val){
                $new_key = "{$key}.{$_key}";
                $this->setParameter($new_key, $_val, $expire, $path, $domain, $sequre);
            }
            
        //通常の場合
        } else {
            //基本変数の登録
            parent::setParameter($key, $value);
            //cookie独自の登録
            if($expire !== NULL) $expire = date('U') + $expire;
            if($path === NULL)   $path   = $this->getPath();
            if($domain === NULL) $domain = $this->getDomain();
            if($sequre === NULL) $sequre = $this->getSequre();
            $keys = explode('.', $key);
            $base_key = array_shift($keys);
            $key_str  = '';
            foreach($keys as $key){
                $key_str .= "[{$key}]";
            }
            setcookie($base_key.$key_str, $value, $expire, $path, $domain, $sequre);
        }
    }


    /**
     * クッキーの削除
     *
     * @access     public
     * @param      string  $key      パラメータ名
     * @param      string  $path     パス
     * @param      string  $domain   ドメイン
     * @param      int     $sequre   セキュア
     */
    public function delParameter($key, $path = NULL, $domain = NULL, $sequre = NULL)
    {
        $value = $this->getParameter($key);
        //配列の場合
        if(is_array($value)){
            foreach($value as $_key => $_val){
                $this->delParameter("{$key}.{$_key}", $path, $domain, $sequre);
            }
            
        //通常の場合
        } else {
            $expire = time() - 86400;
            if($path === NULL) $path = $this->getPath();
            if($domain === NULL) $domain = $this->getDomain();
            if($sequre === NULL) $sequre = $this->getSequre();
            $keys = explode('.', $key);
            $base_key = array_shift($keys);
            $key_str  = '';
            foreach($keys as $key){
                $key_str .= "[{$key}]";
            }
            if($path === NULL){
                setcookie($base_key.$key_str, '', $expire);
            } elseif($domain === NULL){
                setcookie($base_key.$key_str, '', $expire, $path);
            } elseif($sequre === NULL){
                setcookie($base_key.$key_str, '', $expire, $path, $domain);
            } else {
                setcookie($base_key.$key_str, '', $expire, $path, $domain, $sequre);
            }
        }
    }





    /**
     * 有効パスの取得
     *
     * @access     public
     * @return     string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * 有効パスの設定
     * @access     public
     * @param      string  $path   有効パス
     */
    public function setPath($path)
    {
        $this->_path = $path;
    }


    /**
     * 有効ドメインの取得
     *
     * @access     public
     * @return     string
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * 有効ドメインの設定
     * @access     public
     * @param      string  $domain   有効ドメイン
     */
    public function setDomain($domain)
    {
        $this->_domain = $domain;
    }


    /**
     * HTTPS有効値の取得
     *
     * @access     public
     * @return     boolean
     */
    public function getSequre()
    {
        return $this->_sequre;
    }

    /**
     * HTTPS有効値の設定
     *
     * @access     public
     * @param      boolean $sequre   HTTPS有効値
     */
    public function setSequre($sequre)
    {
        $this->_sequre = $sequre;
    }
}

