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
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    SVN: $Id$
 */

require_once dirname(__FILE__) . '/ActiveGateway.class.php';
require_once dirname(__FILE__) . '/ActiveGatewayRecord.class.php';
require_once dirname(__FILE__) . '/ActiveGatewayRecords.class.php';
require_once dirname(__FILE__) . '/ActiveGatewayCondition.class.php';
require_once dirname(__FILE__) . '/ActiveGatewayUtils.class.php';
require_once dirname(__FILE__) . '/Driver/Driver.abstract.php';

/**
 * ActiveGatewayの取得、設定情報の管理などを行うクラス
 *
 * このクラスはsingletonで動作する。
 * 
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGatewayManager
{
    /**
     * DSN(スレーブ)情報を保持
     *
     * @access   private
     * @var      array
     */
    private $_dsn_slave = array();

    /**
     * DSN(マスター)情報を保持
     *
     * @access   private
     * @var      array
     */
    private $_dsn_master = array();

    /**
     * 設定ファイル保持
     *
     * @access   private
     * @var      array
     */
    private $_config_files = array();

    /**
     * ActiveGatewayインスタンス保持
     *
     * @access   private
     * @var      array
     */
    private $_active_gateways = array();

    /**
     * 自身のインスタンス
     *
     * @access   private
     * @var      object
     */
    private static $_instance;

    /**
     * 発行されたクエリーを保持
     *
     * @access   private
     * @var      array
     */
    private static $_querys = array();


    /**
     * コンストラクタ
     *
     * @access     private
     */
    private function __construct()
    {
        
    }


    /**
     * ActiveGatewayManagerインスタンスの返却
     *
     * @access     public
     * @return     object  ActiveGatewayManager
     */
    public function singleton()
    {
        if(self::$_instance === NULL){
            self::$_instance = new ActiveGatewayManager();
        }
        return self::$_instance;
    }


    /**
     * 設定ファイルを読み込む
     *
     * @access     public
     * @param      string  $config_file   設定ファイル
     */
    public function import($config_file)
    {
        $config = ActiveGatewayUtils::loadYaml($config_file);
        //情報のセット
        foreach($config as $alias => $_val){
            $this->setConfig($alias, $_val);
        }
    }


    /**
     * 設定をセットする
     *
     * @access     public
     * @param      string  $alias
     * @param      array   $config
     */
    public function setConfig($alias, array $config)
    {
        //スレーブのセット
        if(isset($config['dsn']) && $config['dsn']){
            $this->_dsn_slave[$alias] = $config['dsn'];
        }
        //マスターのセット
        if(isset($config['dsn_master']) && $config['dsn_master']){
            $this->_dsn_master[$alias] = $config['dsn_master'];
        } else {
            $this->_dsn_master[$alias] = $this->_dsn_slave[$alias];
        }
        //confファイルの決定
        if(isset($config['conf']) && $config['conf']){
            if(!isset($this->_config_files[$alias])){
                $this->_config_files[$alias] = $config['conf'];
            } else {
                $this->_config_files[$alias] = array_merge($this->_config_files[$alias], $config['conf']);
            }
        }
    }



    /**
     * ActiveGatewayの取得
     *
     * DSN文字列から、該当するドライバーを選択し、インスタンス化したのち返却する。
     *
     * @access     public
     * @param      string  $alias   対象DSNのエイリアス名
     * @return     object  ActiveGateway
     */
    public function getActiveGateway($alias)
    {
        //静的参照許可
        if(!is_object($this) || !$this instanceof ActiveGatewayManager){
            $Manager = ActiveGatewayManager::singleton();
            return $Manager->getActiveGateway($alias);
        }
        //既に作成済みの場合
        if($this->hasActiveGateway($alias)){
            return $this->_active_gateways[$alias];
        }
        //新規作成
        elseif($this->hasDsn($alias)){
            $ActiveGateway = $this->makeActiveGateway($this->_pick($this->_dsn_slave[$alias]),
                                            $this->_pick($this->_dsn_master[$alias]),
                                            isset($this->_config_files[$alias]) ? $this->_config_files[$alias] : "");
            $this->_active_gateways[$alias] = $ActiveGateway;
            return $ActiveGateway;
        //不正
        } else {
            trigger_error("[ActiveGatewayManager]:DSN is Not Found -> {$alias}", E_USER_ERROR);
        }
    }


    /**
     * ActiveGatewayを作成する
     *
     * @access     public
     * @param      string  $dsn_slave    DSN(スレーブ)
     * @param      string  $dsn_master   DSN(マスター)
     * @param      string  $conf_file    設定ファイルパス
     * @return     object  ActiveGatewayインスタンス
     */
    public function makeActiveGateway($dsn_slave, $dsn_master = NULL, $conf_file = '')
    {
        //ActiveGatewayの生成
        $ActiveGateway = new ActiveGateway();
        $ActiveGateway->setDsn($dsn_slave);
        $ActiveGateway->setDsnMaster($dsn_master !== NULL ? $dsn_master : $dsn_slave);
        $ActiveGateway->import($conf_file);
        //ドライバーの生成
        $driver_name = $this->_getDriverName($dsn_slave);
        $driver_file = $this->_getDriverFile($dsn_slave);
        if(file_exists($driver_file)){
            include_once($driver_file);
            $Driver = new $driver_name();
            if(is_object($Driver)){
                $ActiveGateway->setDriver($Driver);
            } else {
                trigger_error("[ActiveGatewayManager]:Driver generate failed... -> {$driver_file}", E_USER_ERROR);
            }
        } else {
            trigger_error("[ActiveGatewayManager]:Driver is Not Found -> {$driver_file}", E_USER_ERROR);
        }
        return $ActiveGateway;
    }


    /**
     * Driverのクラス名取得
     *
     * @access     private
     * @param      string  $dsn   DSN情報
     * @return     string
     */
    private function _getDriverName($dsn)
    {
        $dsn_info = parse_url($dsn);
        if(isset($dsn_info['scheme']) && $dsn_info['scheme']){
            return 'ActiveGateway_Driver_' . ucfirst($dsn_info['scheme']);
        } else {
            trigger_error("[ActiveGatewayManager]:DSN is invalid format. -> {$dsn}", E_USER_ERROR);
        }
    }


    /**
     * Driverのファイル名取得
     *
     * @access     private
     * @param      string  $dsn   DSN情報
     * @return     string
     */
    private function _getDriverFile($dsn)
    {
        $driver_name = $this->_getDriverName($dsn);
        return sprintf('%s/Driver/%s.class.php', dirname(__FILE__), preg_replace('/^ActiveGateway_Driver_/', '', $driver_name));
    }


    /**
     * ActiveGatewayを既に保持しているかどうか
     *
     * @access     public
     * @param      string  $alias   Alias名
     * @return     boolean
     */
    public function hasActiveGateway($alias)
    {
        return isset($this->_active_gateways[$alias]) && is_object($this->_active_gateways[$alias]);
    }


    /**
     * DSN情報を保持しているかどうか
     *
     * @access     public
     * @param      string  $alias   Alias名
     * @return     boolean
     */
    public function hasDsn($alias)
    {
        return isset($this->_dsn_slave[$alias]) && $this->_dsn_slave[$alias];
    }


    /**
     * 実行クエリーのプール
     *
     * @access     public
     * @param      string  $query   クエリー文字列
     * @param      int     $time    実行時間
     */
    public function poolQuery($query, $time = 0)
    {
        self::$_querys[] = array(
            'query' => $query,
            'time'  => $time,
        );
    }

    /**
     * プールされた実行クエリーの取得
     *
     * @access     public
     * @return     array
     */
    public function getPoolQuery()
    {
        return self::$_querys;
    }

    /**
     * プールされたクエリーを解放
     *
     * @access     public
     */
    public function clearPoolQuery()
    {
        self::$_querys = array();
    }


    /**
     * 配列からランダムにピック
     * 配列でない場合はそのまま返却
     *
     * @access     private
     * @param      mixed   $array
     * @return     mixed
     */
    private function _pick($array)
    {
        if(is_array($array)){
            return $array[array_rand($array)];
        } else {
            return $array;
        }
    }


    /**
     * 全ての接続を確立しなおす
     *
     * forkした際、子プロセスの終了時に接続が全て切られてしまうため、
     * 子プロセスの接続リソースは別途確保すべきだから
     *
     * @access     public
     */
    public function reconnectAll()
    {
        foreach($this->_active_gateways as $AG){
            $AG->connect(true);
        }
    }

    /**
     * すべての接続を切断する
     *
     * @access     public
     */
    public function disconnectAll()
    {
        foreach($this->_active_gateways as $AG){
            $AG->disconnect();
        }
    }
}

