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
 * Session管理を行うクラス
 *
 * DBセッションを利用する場合は、ActiveGateway必須
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Request_Session extends Samurai_Request_Parameter
{
    /**
     * SESSIONタイプ(file|db)
     *
     * @access   private
     * @var      string
     */
    private $_type  = 'file';

    /**
     * ActiveGateway識別子
     *
     * @access   private
     * @var      string
     */
    private $_dsn   = 'session';

    /**
     * ActiveGatewayセッションテーブル名エイリアス
     *
     * @access   private
     * @var      string
     */
    private $_alias = 'session';

    /**
     * ActiveGatewayインスタンス
     *
     * @access   private
     * @var      object
     */
    private $AG;


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * @override
     */
    public function setParameter($key, $value)
    {
        parent::setParameter($key, $value);
        $keys = explode('.', $key);
        $key_str = '';
        foreach($keys as $key){
            $key_str .= (is_numeric($key) || !$key) ? "[{$key}]" : "['{$key}']" ;
        }
        $script = "\$_SESSION{$key_str} = \$value;";
        eval($script);
    }


    /**
     * @override
     */
    public function delParameter($key)
    {
        parent::delParameter($key);
        $keys = explode('.', $key);
        $key_str = '';
        foreach($keys as $key){
            if($key == '') return false;
            $key_str .= (is_numeric($key)) ? "[{$key}]" : "['{$key}']" ;
        }
        $script = "unset(\$_SESSION{$key_str});";
        eval($script);
    }


    /**
     * 値の取得と削除を同時に行う
     *
     * @access     public
     * @param      string   $key
     */
    public function getAndDel($key)
    {
        $value = $this->getParameter($key);
        $this->delParameter($key);
        return $value;
    }

    /**
     * getAndDelのシノニム
     *
     * @access     public
     * @param      string   $key
     */
    public function getAndRemove($key)
    {
        return $this->getAndDel($key);
    }





    /**
     * セッション処理を開始する
     *
     * @access    public
     */
    public function start()
    {
        if(!isset($_SESSION)){
            $this->_setHandler();
            session_start();
            $this->import($_SESSION);
        }
    }


    /**
     * セッション処理を終了
     * name_spaceが指定された場合は、そのname_spaceの範囲だけクリア
     *
     * @access     public
     * @param      string  $name_space   名前空間
     */
    public function close($name_space = '')
    {
        if($name_space){
            $this->delParameter($name_space);
        } else {
            $_SESSION = array();
            $this->_parameters = array();
            session_destroy();
        }
    }


    /**
     * セッション名を設定/返却
     *
     * @access     public
     * @param      string  $name
     * @return     string
     */
    public function name($name = NULL)
    {
        return $name === NULL ? session_name() : session_name($name) ;
    }


    /**
     * セッションIDを設定/返却
     *
     * @access     public
     * @param      string   $id
     * @return     string
     */
    public function id($id = NULL)
    {
        return $id === NULL ? session_id() : session_id(preg_replace('/[^a-z0-9]/i', '', $id)) ;
    }


    /**
     * save_pathをセット
     *
     * @access    public
     * @param     string  $save_path   save_path
     */
    public function savePath($save_path = NULL)
    {
        return $save_path === NULL ? session_save_path() : session_save_path($save_path) ;
    }


    /**
     * cache_limiterをセット
     *
     * @access    public
     * @param     string  $cache_limiter   cache_limiter
     */
    public function cacheLimiter($cache_limiter = NULL)
    {
        return $cache_limiter === NULL ? session_cache_limiter() : session_cache_limiter($cache_limiter) ;
    }


    /**
     * cache_expireをセット
     *
     * @access    public
     * @param     int     $cache_expire   cache_expire
     */
    public function cacheExpire($cache_expire = NULL)
    {
        return $cache_expire === NULL ? session_cache_expire() : session_cache_expire($cache_expire) ;
    }


    /**
     * use_cookieをセット
     *
     * @access    public
     * @param     boolean $use_cookies   クッキーを使うかどうか
     */
    public function setUseCookies($use_cookies)
    {
        ini_set('session.use_cookies', $use_cookies ? 1 : 0 );
    }


    /**
     * cookie_lifetimeをセット
     *
     * @access    public
     * @param     int     $cookie_lifetime   cookie_lifetime
     */
    public function setCookieLifetime($cookie_lifetime)
    {
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_lifetime, $cookie_params['path'], $cookie_params['domain'], $cookie_params['secure']);
    }


    /**
     * cookie_pathをセット
     *
     * @access    public
     * @param     string  $cookie_path   cookie_path
     */
    public function setCookiePath($cookie_path)
    {
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'], $cookie_path, $cookie_params['domain'], $cookie_params['secure']);
    }


    /**
     * cookie_domainをセット
     *
     * @access    public
     * @param     string  $cookie_domain   cookie_domain
     */
    public function setCookieDomain($cookie_domain)
    {
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'], $cookie_params['path'], $cookie_domain, $cookie_params['secure']);
    }


    /**
     * cookie_secureをセット
     *
     * @access    public
     * @param     boolean $cookie_secure   cookie_secure
     */
    public function setCookieSecure($cookie_secure)
    {
        if(preg_match('/^true$/i', $cookie_secure) || preg_match('/^secure$/i', $cookie_secure) ||
            preg_match('/^on$/i', $cookie_secure) || $cookie_secure === 1 || $cookie_secure === '1'){
            $cookie_secure = 1;
        } else {
            $cookie_secure = 0;
        }
        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'], $cookie_params['path'], $cookie_params['domain'], $cookie_secure);
    }


    /**
     * gc_*をセットする
     *
     * @access     public
     * @param      string   $key
     * @param      int      $value
     */
    public function setGCConfig($key, $value)
    {
        ini_set('session.' . $key, $value);
    }





    /**
     * セッションタイプを設定する
     *
     * @access     public
     * @param      string   $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * セッションタイプを取得する
     *
     * @access     public
     * @return     string
     */
    public function getType()
    {
        return $this->_type;
    }


    /**
     * DSNをセットする
     *
     * @access     public
     * @param      string   $dsn
     */
    public function setDsn($dsn)
    {
        $this->_dsn = $dsn;
    }

    /**
     * DSNを取得する
     *
     * @access     public
     * @return     string
     */
    public function getDsn()
    {
        return $this->_dsn;
    }





    /**
     * session_handlerを設定に従って設定する
     *
     * @access     private
     */
    private function _setHandler()
    {
        if($this->_type == 'db'){
            session_set_save_handler(array($this, 'handler_db_open'), array($this, 'handler_db_close'),
                                        array($this, 'handler_db_read'), array($this, 'handler_db_write'),
                                        array($this, 'handler_db_destroy'), array($this, 'handler_db_gc'));
        }
    }


    /**
     * DB用ハンダラー
     * セッションをオープンする(DBに接続する)
     *
     * @access     public
     * @param      string   $save_path
     * @param      string   $session_name
     * @return     boolean
     */
    public function handler_db_open($save_path, $session_name)
    {
        $ActiveGatewayManager = ActiveGatewayManager::singleton();
        $this->AG = $ActiveGatewayManager->getActiveGateway($this->_dsn);
        return true;
    }


    /**
     * DB用ハンダラー
     * セッションをクローズする(DBを切断する)
     *
     * @access     public
     * @return     boolean
     */
    public function handler_db_close()
    {
        //PDOに切断処理はない
        return true;
    }


    /**
     * DB用ハンダラー
     * データを読み込む
     *
     * @access     public
     * @param      string   $id
     * @return     array
     */
    public function handler_db_read($id)
    {
        $session = $this->AG->findBy($this->_alias, 'session_id', $id);
        $data = $session ? $session->session_data : serialize(array());
        return $data;
    }


    /**
     * DB用ハンダラー
     * データを書き込む
     *
     * @access     public
     * @param      string   $id
     * @param      array    $data
     * @return     boolean
     */
    public function handler_db_write($id, $data)
    {
        //検索
        $session = $this->AG->findBy($this->_alias, 'session_id', $id);
        //作成
        if(!$session){
            $dto = new stdClass();
            $dto->session_id   = $id;
            $dto->session_data = $data;
            $this->AG->create($this->_alias, $dto);
        } else {
            $session->session_data = $data;
            $this->AG->save($session);
        }
        return true;
    }


    /**
     * DB用ハンダラー
     * デストロイ処理
     *
     * @access     public
     * @param      string   $id
     * @return     boolean
     */
    public function handler_db_destroy($id)
    {
        $this->AG->executeQuery("DELETE FROM `{$this->_alias}` WHERE `id` = :id", array(':id' => $id));
        return true;
    }


    /**
     * DB用ハンダラー
     * ガベージコレクション処理
     *
     * @access     public
     * @param      int      $maxlifetime
     * @return     boolean
     */
    public function handler_db_gc($maxlifetime)
    {
        $maxlifetime = $maxlifetime * 60;
        $this->AG->executeQuery("DELETE FROM `{$this->_alias}` WHERE `updated_at` < :updated_at",
                                                            array(':updated_at' => time() - $maxlifetime));
        return true;
    }
}

