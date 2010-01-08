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
 * ActiveGateway本体
 * 
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGateway
{
    /**
     * DSN(スレーブ)
     *
     * @access   private
     * @var      string
     */
    private $_dsn_slave = '';

    /**
     * DSN(マスター)
     *
     * @access   private
     * @var      string
     */
    private $_dsn_master = '';

    /**
     * 設定情報
     *
     * @access   private
     * @var      array
     */
    private $_config = array();

    /**
     * 設定ファイル
     *
     * @access   private
     * @var      string
     */
    private $_config_file = '';

    /**
     * フェッチモード
     *
     * @access   private
     * @var      int
     */
    private $_fetch_mode = self::FETCH_OBJ;

    /**
     * テーブル情報
     *
     * @access   private
     * @var      array
     */
    private $_table_info = array();

    /**
     * Driverインスタンス
     *
     * @access   private
     * @var      object
     */
    private $Driver;

    /**
     * FETCH_MODE定数(LAZY)
     *
     * @const    int
     */
    const FETCH_LAZY = PDO::FETCH_LAZY;

    /**
     * FETCH_MODE定数(ASSOC)
     *
     * @const    int
     */
    const FETCH_ASSOC = PDO::FETCH_ASSOC;

    /**
     * FETCH_MODE定数(OBJ)
     *
     * @const    int
     */
    const FETCH_OBJ = PDO::FETCH_OBJ;

    /**
     * FETCH_MODE定数(BOTH)
     *
     * @const    int
     */
    const FETCH_BOTH = PDO::FETCH_BOTH;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * コネクト
     *
     * @access     public
     * @param      boolean $force
     * @return     boolean 接続の可否
     */
    public function connect($force = false)
    {
        //接続済み
        if(!$force && $this->hasConnection()){
            return true;
        }
        //DSNなし
        if(!$this->hasDsn()){
            trigger_error('[ActiveGateway]:DSN is Not Found.', E_USER_ERROR);
        }
        //新規接続
        $this->Driver->connect($this->_dsn_slave, $this->_dsn_master);
        return true;
    }





    /**
     * findシリーズ、ID検索
     *
     * @access     public
     * @param      string  $alias   テーブル名
     * @param      int     $id      ID
     * @return     object  ActiveGatewayRecord
     */
    public function find($alias, $id)
    {
        $Record = $this->_buildRecord($alias);
        $primary_key = $Record->getPrimaryKey();
        
        $condition = ActiveGateway::getCondition();
        $condition->where->$primary_key = $id;
        return $this->findDetail($alias, $condition);
    }


    /**
     * findシリーズ、指定検索
     *
     * @access     public
     * @param      string  $alias    テーブル名
     * @param      string  $column   カラム名
     * @param      mixed   $value    検索条件(配列も可)
     * @return     object  ActiveGatewayRecord
     */
    public function findBy($alias, $column, $value)
    {
        $condition = ActiveGateway::getCondition();
        $condition->where->$column = $value;
        return $this->findDetail($alias, $condition);
    }


    /**
     * findシリーズ、詳細検索
     *
     * @access     public
     * @param      string  $alias       テーブル名
     * @param      array   $condition   ActiveGatewayCondition
     * @return     object  ActiveGatewayRecord
     */
    public function findDetail($alias, ActiveGatewayCondition $condition)
    {
        $condition->total_rows = false;
        $condition->setLimit(1);
        $ActiveGatewayRecords = $this->findAllDetail($alias, $condition);
        return $ActiveGatewayRecords->getFirstRecord();
    }


    /**
     * findシリーズ、SQL検索
     *
     * @access     public
     * @param      string  $alias    テーブル名
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @return     object  ActiveGatewayRecord
     */
    public function findSql($alias, $sql, $params = array())
    {
        $ActiveGatewayRecords = $this->findAllSql($alias, $sql, $params, 1, NULL, false);
        return $ActiveGatewayRecords->getFirstRecord();
    }


    /**
     * findAllシリーズ
     * 内実、findAllDetailのシノニム。
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      object  $condition    ActiveGatewayCondition
     * @return     object  ActiveGatewayRecords
     */
    public function findAll($alias, ActiveGatewayCondition $condition)
    {
        return $this->findAllDetail($alias, $condition);
    }


    /**
     * findAllシリーズ、指定検索
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      string  $column       カラム名
     * @param      mixed   $value        検索条件(配列可)
     * @param      object  $condition    ActiveGatewayCondition
     * @return     object  ActiveGatewayRecords
     */
    public function findAllBy($alias, $column, $value, $condition = NULL)
    {
        if($condition === NULL) $condition = ActiveGateway::getCondition();
        $condition->where->$column = $value;
        return $this->findAllDetail($alias, $condition);
    }


    /**
     * findAllシリーズ、詳細検索
     *
     * @access     public
     * @param      string  $alias       テーブル名
     * @param      object  $condition   ActiveGatewayCondition
     * @return     object  ActiveGatewayRecords
     */
    public function findAllDetail($alias, ActiveGatewayCondition $condition)
    {
        //初期化
        $Record = $this->_buildRecord($alias);
        if($condition->select === NULL) $condition->select = '*';
        if($condition->from === NULL)   $condition->from   = $this->Driver->escapeColumn($Record->getTableName());
        //自動付加
        $table_info = $this->getTableInfo($alias, $Record->getTableName());
        if(isset($table_info['active']) && $condition->regard_active && !isset($condition->where->active)){
            $condition->where->active = '1';
        }
        
        //セレクト節の生成
        $select = (array)$condition->select;
        //フロム節の調節
        $from = (array)$condition->from;
        //条件節の生成
        $params = array();
        $wheres = $this->_chain_where($condition->where, $params);
        if($condition->addtional_where) $wheres[] = $condition->addtional_where;
        //グループ節の生成
        $groups = (array)$condition->group;
        //オーダー節の生成
        $orders = $this->_chain_order($condition->order);
        
        //SQL文の生成
        $sql  = sprintf('SELECT %s FROM %s', join(', ', $select), join(', ', $from));
        $sql .= ($wheres) ? sprintf(' WHERE %s', join(' AND ', $wheres)) : '' ;
        $sql .= ($groups) ? sprintf(' GROUP BY %s', join(', ', $groups)) : '' ;
        $sql .= ($orders) ? sprintf(' ORDER BY %s', join(', ', $orders)) : '' ;
        //SQLから検索
        $result = $this->findAllSql($alias, $sql, $params, $condition->limit, $condition->offset, $condition->total_rows);
        return $result;
    }


    /**
     * findAllシリーズ、SQL検索
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      string  $sql          SQL文
     * @param      array   $params       ブレースフォルダ
     * @param      int     $limit        取得数
     * @param      int     $offset       開始位置
     * @param      boolean $total_rows   総レコード数を取得するかどうか
     * @return     object  ActiveGatewayRecords
     */
    public function findAllSql($alias, $sql, $params = array(), $limit = NULL, $offset = NULL, $total_rows = false)
    {
        $Records = new ActiveGatewayRecords();
        if($total_rows) $sql = $this->Driver->modifyFoundRowsQuery($sql);
        $res = $this->executeQuery($sql, $params, $limit, $offset);
        if($total_rows) $_total_rows = $this->Driver->getTotalRows($sql, $params);
        
        while($row = $res->fetch($this->_fetch_mode)){
            $Record = $this->_buildRecord($alias, $row, false);
            $Records->addRecord($Record);
        }
        
        $total_rows = ($total_rows) ? $_total_rows : $Records->getSize() ;
        $Records->setTotalRows($total_rows);
        return $Records;
    }





    /**
     * レコードの挿入を行う
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      array   $attributes   各種値
     * @return     object  ActiveGatewayRecord
     */
    public function insert($alias, $attributes = array())
    {
        $Record = $this->_buildRecord($alias, $attributes, true);
        $table_name = $Record->getTableName();
        //各種情報の付加
        $table_info = $this->getTableInfo($alias, $table_name);
        if(isset($table_info['created_at']) && !isset($attributes['created_at'])){
            $attributes['created_at'] = time();
        }
        if(isset($table_info['updated_at']) && !isset($attributes['updated_at'])){
            $attributes['updated_at'] = time();
        }
        if(isset($table_info['active']) && !isset($attributes['active'])){
            $attributes['active'] = '1';
        }
        //ディフォルト値調節
        $this->Driver->modifyAttributes($table_info, $attributes);
        //インサート
        $sql = $this->Driver->modifyInsertQuery($table_name, $attributes, $params);
        $stmt = $this->executeUpdate($sql, $params);
        $attributes[$Record->getPrimaryKey()] = $this->Driver->lastInsertId();
        $record = $this->_buildRecord($alias, $attributes, false);
        return $record;
    }





    /**
     * updateシリーズ、レコードインスタンスの一つの情報を更新する
     *
     * @access     public
     * @param      object  $record   ActiveGatewayRecord
     * @param      string  $column   カラム名
     * @param      mixed   $value    値
     * @return     boolean
     */
    public function updateAttribute($record, $column, $value)
    {
        $record->$column = $value;
        return $this->save($record);
    }


    /**
     * updateシリーズ、レコードインスタンスの複数の情報を更新する
     *
     * @access     public
     * @param      object  $Record       ActiveGatewayRecord
     * @param      array   $attributes   設定値
     * @return     boolean
     */
    public function updateAttributes($record, $attributes = array())
    {
        foreach((array)$attributes as $_key => $_val){
            if(!preg_match('/^_/', $_key)){
                $record->$_key = $_val;
            }
        }
        return $this->save($record);
    }


    /**
     * updateシリーズ、ID更新
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      int     $id           ID
     * @param      array   $attributes   設定値
     * @return     boolean
     */
    public function update($alias, $id, $attributes = array())
    {
        $record = $this->_buildRecord($alias);
        $primary_key = $record->getPrimaryKey();
        
        $condition = ActiveGateway::getCondition();
        $condition->where->$primary_key = $id;
        return $this->updateDetail($alias, $attributes, $condition);
    }


    /**
     * updateシリーズ、指定更新
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      string  $column       カラム名
     * @param      string  $value        条件
     * @param      array   $attributes   設定値
     * @return     boolean
     */
    public function updateBy($alias, $column, $value, $attributes = array())
    {
        $condition = ActiveGateway::getCondition();
        $condition->where->$column = $value;
        return $this->updateDetail($alias, $attributes, $condition);
    }


    /**
     * updateシリーズ、詳細更新
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      array   $attributes   設定値
     * @param      object  $condition    ActiveGatewayCondition
     * @return     boolean 
     */
    public function updateDetail($alias, $attributes, ActiveGatewayCondition $condition)
    {
        $condition->setLimit(1);
        return $this->updateAllDetail($alias, $attributes, $condition);
    }


    /**
     * updateシリーズ、SQL更新
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @return     boolean
     */
    public function updateSql($sql, $params)
    {
        return $this->updateAllSql($sql, $params, 1);
    }


    /**
     * updateAllシリーズ、ID更新
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      int     $id           ID
     * @param      array   $attributes   設定値
     * @return     boolean
     */
    public function updateAll($alias, $id, $attributes = array())
    {
        $record = $this->_buildRecord($alias);
        $primary_key = $record->getPrimaryKey();
        
        $condition = ActiveGateway::getCondition();
        $condition->where->$primary_key = $id;
        return $this->updateAllDetail($alias, $attributes, $condition);
    }


    /**
     * updateAllシリーズ、指定更新
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      string  $column       カラム名
     * @param      string  $value        条件
     * @param      array   $attributes   設定値
     * @return     boolean
     */
    public function updateAllBy($alias, $column, $value, $attributes = array())
    {
        $condition = ActiveGateway::getCondition();
        $condition->where->$column = $value;
        return $this->updateAllDetail($alias, $attributes, $condition);
    }


    /**
     * updateAllシリーズ、詳細更新
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      object  $condition    ActiveGatewayCondition
     * @param      string  $attributes   設定値
     * @return     boolean
     */
    public function updateAllDetail($alias, ActiveGatewayCondition $condition, $attributes = array())
    {
        //初期化
        $params = array();
        $record = $this->_buildRecord($alias);
        //自動付加
        $attributes = (array)$attributes;
        if($record->hasField('updated_at')){
            $attributes['updated_at'] = time();
        }
        //設定節の生成
        $sets = array();
        foreach($attributes as $_key => $_val){
            $place_holder = ":set_{$_key}";
            $params[$place_holder] = $_val;
            $sets[] = sprintf('`%s` = %s', $_key, $place_holder);
        }
        if(!$sets){
            trigger_error('[ActiveGateway]:No attributes update is very danger!!', E_USER_ERROR);
        }
        //条件節の生成
        $wheres = $this->_chain_where($condition->where, $params, 'where_');
        if(!$wheres){
            trigger_error('[ActiveGateway]:No where update is very danger!!', E_USER_ERROR);
        }
        //オーダー節の生成
        $orders = $this->_chain_order($condition->order);
        //SQLから更新
        $sql = $this->Driver->modifyUpdateQuery($record->getTableName(), $sets, $wheres, $orders);
        return $this->updateAllSql($sql, $params, $condition->limit);
    }


    /**
     * updateAllシリーズ、SQL更新
     *
     * @access     public
     * @param      string   $sql      SQL文
     * @param      array    $params   ブレースフォルダ
     * @param      int      $limit    更新数
     * @return     resource PDOステートメント
     */
    public function updateAllSql($sql, $params = array(), $limit = NULL)
    {
        $stmt = $this->executeUpdate($sql, $params, $limit);
        return $stmt;
    }





    /**
     * プライマリキーにおいて削除を実行する
     *
     * @access     public
     * @param      string  $alias   テーブル名
     * @param      int     $id      ID
     * @return     boolean
     */
    public function delete($alias, $id)
    {
        $record = $this->_buildRecord($alias);
        $primary_key = $record->getPrimaryKey();
        
        $condition = ActiveGateway::getCondition();
        $condition->where->$primary_key = $id;
        return $this->deleteDetail($alias, $condition);
    }


    /**
     * 詳細削除
     *
     * @access     public
     * @param      string  $alias       テーブル名
     * @param      object  $condition   ActiveGatewayCondition
     */
    public function deleteDetail($alias, ActiveGatewayCondition $condition)
    {
        $condition->setLimit(1);
        return $this->deleteAllDetail($alias, $condition);
    }


    /**
     * 詳細全削除
     *
     * @access     public
     * @param      string   $alias       テーブル名
     * @param      object   $condition   ActiveGatewayCondition
     */
    public function deleteAllDetail($alias, ActiveGatewayCondition $condition)
    {
        $record = $this->_buildRecord($alias);
        //論理消去できるのであれば論理消去(こちらが望ましい)
        if($condition->regard_active && $record->enableDeleteByLogical()){
            $attributes['active'] = '0';
            if($record->hasField('deleted_at')){
                $attributes['deleted_at'] = time();
            }
            return $this->updateAllDetail($alias, $attributes, $condition);
        }
        //物理消去
        else {
            //条件節の生成
            $params = array();
            $wheres = $this->_chain_where($condition->where, $params);
            if(!$wheres){
                trigger_error('[ActiveGateway]:No where delete is very danger!!', E_USER_ERROR);
            }
            //オーダー節の生成
            $orders = $this->_chain_order($condition->order);
            //SQLから更新
            $sql = $this->Driver->modifyDeleteQuery($Record->getTableName(), $wheres, $orders);
            return $this->updateAllSql($sql, $params, $condition->limit);
        }
    }





    /**
     * 新しいレコードインスタンスの生成
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      array   $attributes   初期パラメータ
     * @return     object  ActiveGatewayRecord
     */
    public function build($alias, $attributes = array())
    {
        $record = $this->_buildRecord($alias, $attributes, true);
        return $record;
    }


    /**
     * レコードインスタンスの生成
     *
     * @access     private
     * @param      string  $alias        テーブル名
     * @param      mixed   $row          PDOStatement->fetchの取得結果
     * @param      boolean $new_record   新規レコードかどうかの判断値
     * @return     object  ActiveGatewayRecord
     */
    private function _buildRecord($alias, $row = NULL, $new_record = true)
    {
        $record = new ActiveGatewayRecord($row, $new_record, $alias);
        //設定情報の取得
        $config = array();
        if($alias !== NULL && isset($this->_config[$alias])){
            $config = $this->_config[$alias];
        }
        //テーブル名の書き換え
        if(isset($config['table_name'])){
            $record->setTableName($config['table_name']);
        }
        //プライマリキーの書き換え
        if(isset($config['primary_key'])){
            $record->setPrimaryKey($config['primary_key']);
        }
        //テーブル情報の取得
        $table_info = $this->getTableInfo($alias, $record->getTableName());
        $record->setTableInfo($table_info);
        return $record;
    }


    /**
     * テーブル情報の取得
     *
     * ドライバーの取得メソッドを使用し、取得する。
     * ドライバーの取得メソッドは、PEAR_DBのgetTableInfo()と同等であるべきである。
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      string  $table_name   対象となるテーブルの実名
     * @return     array   テーブル情報配列
     */
    public function getTableInfo($alias, $table_name)
    {
        //既に取得済みの場合
        if(isset($this->_table_info[$alias])){
            return $this->_table_info[$alias];
        }
        //情報の取得
        $this->connect();
        $attributes = $this->Driver->getTableInfo($table_name);
        //情報の代入
        $this->_table_info[$alias] = $attributes;
        return $this->_table_info[$alias];
    }





    /**
     * インスタンスを使ってデータを更新する
     *
     * @access     public
     * @param      object  $record   ActiveGatewayRecord
     * @return     boolean
     */
    public function save(ActiveGatewayRecord &$record)
    {
        //チェック
        if(!$record->isSavable()){
            trigger_error('[ActiveGateway]:This record can not save.', E_USER_ERROR);
        }
        //新規レコードの場合
        if($record->isNewRecord()){
            $record = $this->insert($record->getAlias(), $record->getAttributes());
            return true;
        //既存レコードの場合
        } else {
            if($record->getAttributes(true)){
                $this->update($record->getAlias(), $record->getOriginalValue('primary_key'), $record->getAttributes(true));
            }
            return true;
        }
    }


    /**
     * 上記のbuildとsaveの一連の流れを一つのメソッドで完結させてしまう場合はコレ
     *
     * @access     public
     * @param      string  $alias        テーブル名
     * @param      array   $attributes   初期パラメータ
     * @return     object  ActiveGatewayRecord
     */
    public function create($alias, $attributes = array())
    {
        if(is_object($attributes) && $attributes instanceof ActiveGatewayRecord){
            $attributes->{$attributes->getPrimaryKey()} = NULL;
            $attributes = $attributes->toArray();
        }
        $record = $this->build($alias, $attributes);
        $this->save($record);
        return $record;
    }


    /**
     * インスタンスを使用し、データを削除する
     *
     * @access     public
     * @param      object  $record
     * @return     boolean
     */
    public function destroy(ActiveGatewayRecord &$record)
    {
        //新規レコードの場合
        if($record->isNewRecord()){
            $record = NULL;
            return true;
        //既存レコードの場合
        } else {
            return $this->delete($record->getAlias(), $record->getOriginalValue($record->getPrimaryKey()));
        }
    }


    /**
     * 検索(SELECT)SQL文の実行
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースホルダ
     * @param      int     $limit    取得数
     * @param      int     $offset   開始位置
     * @return     object  PDOステートメント
     */
    public function executeQuery($sql, $params = array(), $limit = NULL, $offset = NULL)
    {
        $this->connect();
        if($limit !== NULL || $offset !== NULL){
            $stmt = $this->Driver->limitQuery($sql, $params, $offset, $limit);
        } else {
            $stmt = $this->Driver->query($sql, $params);
        }
        
        return $stmt;
    }


    /**
     * 更新(INSERT|UPDATE|DELETE)SQL文の実行
     *
     * @access     public
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @param      int     $limit    作用制限
     * @return     object  PDOステートメント
     */
    public function executeUpdate($sql, $params = array(), $limit = NULL)
    {
        $this->connect();
        if($limit !== NULL){
            $stmt = $this->Driver->limitQuery($sql, $params, NULL, $limit);
        } else {
            $stmt = $this->Driver->query($sql, $params);
        }
        return $stmt;
    }





    /**
     * トランザクション開始
     *
     * @access     public
     * @param      string  $name   トランザクション名
     */
    public function tx($name = NULL)
    {
        $this->connect();
        $this->Driver->tx($name);
    }

    /**
     * ロールバック処理
     *
     * @access     public
     * @param      string  $name   トランザクション名
     */
    public function rollback($name = NULL)
    {
        $this->connect();
        $this->Driver->rollback($name);
    }

    /**
     * コミット処理
     *
     * @access     public
     * @param      string  $name   トランザクション名
     */
    public function commit($name = NULL)
    {
        $this->connect();
        $this->Driver->commit($name);
    }





    /**
     * PEAR_DB::getAll()と同等
     *
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @param      int     $limit    取得数
     * @param      int     $offset   開始位置
     * @return     array   すべての取得結果
     */
    public function getAll($sql, $params = array(), $limit = NULL, $offset = NULL)
    {
        $stmt = $this->executeQuery($sql, $params, $limit, $offset);
        return $stmt->fetchAll($this->_fetch_mode);
    }


    /**
     * PEAR_DB::getRow()と同等
     *
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @return     array   1レコードの結果
     */
    public function getRow($sql, $params = array())
    {
        $stmt = $this->executeQuery($sql, $params, 1);
        $row  = $stmt->fetchAll($this->_fetch_mode);
        return $row;
    }


    /**
     * PEAR_DB::getCol()と同等
     *
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @param      mixed   $column   カラム名指定
     * @return     array   取得結果
     */
    public function getCol($sql, $params = array(), $column = NULL)
    {
        $stmt = $this->executeQuery($sql, $params);
        $rows = $stmt->fetchAll(ActiveGateway::FETCH_BOTH);
        $result = array();
        foreach($rows as $row){
            if($column !== NULL){
                if(isset($row[$column])){
                    $result[] = $row[$column];
                } else {
                    $result[] = $row[0];
                }
            } else {
                $result[] = $row[0];
            }
        }
        return $result;
    }


    /**
     * PEAR_DB::getOne()と同等
     *
     * @param      string  $sql      SQL文
     * @param      array   $params   ブレースフォルダ
     * @param      mixed   $column   カラム名指定
     * @return     mixed   取得結果
     */
    public function getOne($sql, $params = array(), $column = NULL)
    {
        $stmt = $this->executeQuery($sql, $params, 1);
        $row = $stmt->fetch(ActiveGateway::FETCH_BOTH);
        if($column !== NULL){
            if(isset($row[$column])){
                return $row[$column];
            }
        }
        return $row[0];
    }





    /**
     * DSNの設定
     *
     * @access     public
     * @param      string  $dsn   DSN情報文字列
     */
    public function setDsn($dsn)
    {
        $this->_dsn_slave = $dsn;
        if(!$this->_dsn_master) $this->setDsnMaster($dsn);
    }

    /**
     * DSNの設定(マスター)
     *
     * @access     public
     * @param      string  $dsn   DSN情報文字列
     */
    public function setDsnMaster($dsn)
    {
        $this->_dsn_master = $dsn;
    }


    /**
     * 設定情報の取り込み
     *
     * @access     public
     * @param      string  $config_file   設定ファイル
     */
    public function import($config_file)
    {
        if($config_file){
            $this->_config = ActiveGatewayUtils::loadYaml($config_file);
            $this->_config_file = $config_file;
        }
    }


    /**
     * ドライバーの格納
     *
     * @access     public
     * @param      object  $Driver   ActiveGateway_Driver
     */
    public function setDriver($Driver)
    {
        $this->Driver = $Driver;
    }


    /**
     * ActiveGatewayの検索用DTOを返却する
     *
     * @access     public
     * @return     object   ActiveGatewayCondition
     */
    public function getCondition()
    {
        $condition = new ActiveGatewayCondition();
        return $condition;
    }


    /**
     * コネクションを保持しているかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function hasConnection()
    {
        return $this->Driver->hasConnection();
    }


    /**
     * DSNを保持しているかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function hasDsn()
    {
        return $this->_dsn_slave && $this->_dsn_master;
    }


    /**
     * WHERE条件を連結する
     *
     * @access     private
     * @param      mixed   $where    WHERE
     * @param      array   $params   ブレースフォルダ
     * @return     array
     */
    private function _chain_where($where, &$params, $prefix = '', $original_key = NULL)
    {
        $return = array();
        if($where){
            foreach($where as $_key => $_val){
                $column_key = $original_key === NULL ? $_key : $original_key;
                switch((string)$_key){
                    case 'range':
                        $place_holder1 = ":range_{$column_key}_1";
                        $place_holder2 = ":range_{$column_key}_2";
                        $return[] = sprintf('%s >= %s AND %s <= %s', $column_key, $place_holder1, $column_key, $place_holder2);
                        $params[$place_holder1] = array_shift($_val);
                        $params[$place_holder2] = array_shift($_val);
                        break;
                    default:
                        if(is_array($_val)){
                            $sub_wheres = $this->_chain_where($_val, $params, $prefix.$_key.'_', $column_key);
                            $return[] = sprintf('( %s )', join(' OR ', $sub_wheres));
                        } else {
                            $condition = ActiveGateway::getCondition();
                            if(!is_object($_val) || ! $_val instanceof ActiveGatewayCondition_Value){
                                $_val = $condition->isEqual($_val);
                            }
                            $place_holder = ':'.$prefix.$_key;
                            if($_val->override){
                                $return[] = sprintf('%s %s', $column_key, $_val->override);
                            } else {
                                $params[$place_holder] = $_val->value;
                                $return[] = sprintf('%s %s %s', $this->Driver->escapeColumn($column_key), $_val->operator, $place_holder);
                            }
                        }
                        break;
                }
            }
        }
        return $return;
    }


    /**
     * ORDER条件を連結する
     *
     * @access     private
     * @param      mixed   ORDER
     * @return     array
     */
    private function _chain_order($order)
    {
        $return = array();
        if($order){
            if(is_string($order)){
                $return[] = preg_match('/\(\)/', $order) ? $order : $this->Driver->escapeColumn($order);
            } else {
                foreach($order as $_key => $_val){
                    $return[] = sprintf('%s %s', $this->Driver->escapeColumn($_key), $_val);
                }
            }
        }
        return $return;
    }
}

