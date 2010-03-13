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

/**
 * ActiveGateway各種ドライバーの抽象クラス
 *
 * 各種ドライバーは必ず継承すること
 * 
 * @package    ActiveGateway
 * @subpackage Driver
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class ActiveGateway_Driver
{
    /**
     * コネクション
     *
     * @access   protected
     * @var      resource
     */
    protected $connection;

    /**
     * コネクションマスタ
     *
     * @access   protected
     * @var      resource
     */
    protected $connection_master;

    /**
     * トランザクション内かどうか
     *
     * @access   protected
     * @var      boolean
     */
    protected $_in_transaction = false;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * 接続
     *
     * @access     public
     * @param      string  $dsn          DSN
     * @param      string  $dsn_master   マスターDSN
     */
    public function connect($dsn, $dsn_master='')
    {
        $this->connection = $this->_connect(parse_url($dsn));
        if(!$dsn_master){
            $this->connection_master = $this->connection;
        } elseif($dsn_master == $dsn){
            $this->connection_master = $this->connection;
        } else {
            $this->connection_master = $this->_connect(parse_url($dsn_master));
        }
    }


    /**
     * 各種ドライバー用コネクト
     *
     * @param      array    $dsn_info   分解されたDSN情報
     * @return     resource コネクション
     */
    abstract protected function _connect(array $dsn_info);


    /**
     * 切断
     *
     * @access     public
     */
    public function disconnect()
    {
        $this->connection = NULL;
        $this->connection_master = NULL;
    }


    /**
     * コネクションが確立されているのかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function hasConnection()
    {
        return is_object($this->connection) && is_object($this->connection_master);
    }



    /**
     * クエリー
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @return     object  PDOステートメント
     */
    public function query($sql, $params = array())
    {
        //ステートメントの生成
        if($this->_isUpdateQuery($sql)){
            $stmt = $this->connection_master->prepare($sql);
        } else {
            $stmt = $this->connection->prepare($sql);
        }
        //ブレースフォルダの割当て
        foreach($params as $_key => $_val){
            //データタイプのディフォルトは文字列
            $param_type = PDO::PARAM_STR;
            if(is_null($_val)){
                $param_type = PDO::PARAM_NULL;
            } elseif(is_int($_val)){
                $param_type = PDO::PARAM_INT;
            } elseif(is_bool($_val)){
                $param_type = PDO::PARAM_BOOL;
            } elseif(is_resource($_val)){
                $param_type = PDO::PARAM_LOB;
            } elseif(strlen($_val)>=5120){
                $param_type = PDO::PARAM_LOB;
            }
            $stmt->bindValue($_key, $_val, $param_type);
        }
        //実行
        $execute_start = microtime(true);
        $stmt->execute();
        $execute_end   = microtime(true);
        ActiveGatewayManager::singleton()->poolQuery($stmt->queryString, $execute_end-$execute_start);
        //エラーチェック
        $this->_checkError($stmt, $params);
        return $stmt;
    }


    /**
     * リミットクエリー
     *
     * SQL文中にリミットを記述してもかまわないが、リミットに関しては、各DBで文法が違うため、
     * その差異を吸収するメソッドとして、このメソッドは存在する。
     * ディフォルトでうまくうごかないDBは、それぞれのドライバーに専用のメソッドを記述してオーバーライドすること。
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @param      int     $offset   開始位置
     * @param      int     $limit    取得数
     * @return     object  PDOステートメント
     */
    public function limitQuery($sql, $params = array(), $offset = NULL, $limit = NULL)
    {
        if($this->_isUpdateQuery($sql)){
            $sql = $this->modifyUpdateLimitQuery($sql, $limit);
        } else {
            $sql = $this->modifyLimitQuery($sql, $offset, $limit);
        }
        return $this->query($sql, $params);
    }



    /**
     * トランザクション開始
     *
     * @access     public
     */
    public function tx()
    {
        if(!$this->_in_transaction){
            $this->_in_transaction = true;
            $this->connection_master->beginTransaction();
        }
    }


    /**
     * ロールバック
     *
     * @access     public
     */
    public function rollback()
    {
        if($this->_in_transaction){
            $this->_in_transaction = false;
            $this->connection_master->rollback();
        }
    }


    /**
     * コミット
     *
     * @access     public
     */
    public function commit()
    {
        if($this->_in_transaction){
            $this->_in_transaction = false;
            $this->connection_master->commit();
        }
    }


    /**
     * lastInsertID
     *
     * @access     public
     * @return     int
     */
    public function lastInsertId()
    {
        return $this->connection_master->lastInsertId();
    }





    /**
     * リミットクエリーの整形
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      int     $offset   開始位置
     * @param      int     $limit    作用制限
     * @return     string  SQL文
     */
    abstract public function modifyLimitQuery($sql, $offset = NULL, $limit = NULL);

    /**
     * インサートクエリーの生成
     *
     * @access     public
     * @param      string  $table_name   テーブル名
     * @param      array   $attributes   各種値
     * @param      array   &$params       ブレースフォルダ格納用
     * @return     string  SQL文
     */
    abstract public function modifyInsertQuery($table_name, $attributes, &$params = array());

    /**
     * 更新クエリーの生成
     *
     * @access     public
     * @param      string  $table_name   テーブル名
     * @param      array   $sets         更新値
     * @param      array   $wheres       条件値
     * @param      array   $orders       並び順
     * @return     string  SQL文
     */
    abstract public function modifyUpdateQuery($table_name, $sets, $wheres = array(), $orders = array());

    /**
     * 削除クエリーの生成
     *
     * @access     public
     * @param      string  $table_name   テーブル名
     * @param      array   $wheres       条件値
     * @param      array   $orders       並び順
     * @return     string  SQL文
     */
    abstract public function modifyDeleteQuery($table_name, $wheres = array(), $orders = array());

    /**
     * 更新制限クエリーの整形
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      int     $limit    作用制限
     * @return     string  SQL文
     */
    abstract public function modifyUpdateLimitQuery($sql, $limit = NULL);

    /**
     * 総レコード取得用クエリー整形
     *
     * @access     public
     * @param      string  $sql   SQL文
     * @return     string  SQL文
     */
    abstract public function modifyFoundRowsQuery($sql);

    /**
     * インサート時に内容を調節する
     *
     * @access     public
     */
    public function modifyAttributes($table_info, &$attributes = array())
    {
        
    }

    /**
     * カラム名をエスケープする
     *
     * @access     public
     * @return     string
     */
    public function escapeColumn($column_name)
    {
        return $column_name;
    }



    /**
     * 直前のクエリーの総レコード数の取得
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @return     int
     */
    abstract public function getTotalRows($sql, $params = array());



    /**
     * 更新文かどうかの判断
     *
     * @access     protected
     * @param      string  $sql   SQL文
     * @return     boolean
     */
    protected function _isUpdateQuery($sql)
    {
        $sql = trim($sql);
        return preg_match('/^(UPDATE|INSERT|BEGIN|ROLLBACK|COMMIT)/i', $sql);
    }


    /**
     * クエリー実行後のエラーチェック
     *
     * @access     private
     */
    protected function _checkError($stmt, $params)
    {
        @list($code, $driver_code, $message) = $stmt->errorInfo();
        if($code != '00000'){
            throw(new Exception("ActiveGateway(PDO) Error[{$code}][{$driver_code}]: {$message} -> " . $stmt->queryString));
        }
    }
}

