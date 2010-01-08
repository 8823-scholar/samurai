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
 * ActiveGatewayのMysql用ドライバー
 * 
 * @package    ActiveGateway
 * @subpackage Driver
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGateway_Driver_Mysql extends ActiveGateway_Driver
{
    /**
     * @override
     */
    protected function _connect(array $dsn_info)
    {
        $scheme  = $dsn_info['scheme'];
        $user    = $dsn_info['user'];
        $pass    = $dsn_info['pass'];
        $host    = $dsn_info['host'];
        $db_name = substr($dsn_info['path'],1);
        try {
            $connection = new PDO("mysql:dbname={$db_name};host={$host}", $user, $pass);
        } catch(PDOException $Exception){
            trigger_error(sprintf('[ActiveGateway_Driver_Mysql]:Connection failed... -> [%s]', $Exception->getMessage()), E_USER_ERROR);
        }
        return $connection;
    }



    /**
     * @implements
     */
    public function modifyLimitQuery($sql, $offset = NULL, $limit = NULL)
    {
        if($offset !== NULL && $limit !== NULL){
            $sql = sprintf('%s LIMIT %d, %d', $sql, $offset, $limit);
        } elseif($limit !== NULL){
            $sql = sprintf('%s LIMIT %d', $sql, $limit);
        }
        return $sql;
    }

    /**
     * @implements
     */
    public function modifyInsertQuery($table_name, $attributes, &$params = array())
    {
        $i = 1;
        $field_list = array();
        $value_list = array();
        
        foreach($attributes as $_key => $_val){
            $field_list[] = "`{$_key}`";
            $value_list[] = '?';
            $params[$i] = $_val;
            $i++;
        }
        
        return sprintf('INSERT INTO `%s` ( %s ) VALUES ( %s )', $table_name, join(', ', $field_list), join(', ', $value_list));
    }

    /**
     * @implements
     */
    public function modifyUpdateQuery($table_name, $sets, $wheres = array(), $orders = array())
    {
        $sql  = sprintf('UPDATE `%s` SET %s', $table_name, join(', ', $sets));
        $sql .= ($wheres) ? sprintf(' WHERE %s', join(' AND ', $wheres)) : '' ;
        return $sql;
    }

    /**
     * @implements
     */
    public function modifyDeleteQuery($table_name, $wheres = array(), $orders = array())
    {
        $sql  = sprintf('DELETE FROM `%s`', $table_name);
        $sql .= ($wheres) ? sprintf(' WHERE %s', join(' AND ', $wheres)) : '' ;
        return $sql;
    }

    /**
     * @implements
     */
    public function modifyUpdateLimitQuery($sql, $limit = NULL)
    {
        if(is_numeric($limit)){
            $sql = sprintf('%s LIMIT %d', $sql, $limit);
        }
        return $sql;
    }

    /**
     * @implements
     */
    public function modifyFoundRowsQuery($sql)
    {
        if(!preg_match('/^SELECT\s*SQL_CALC_FOUND_ROWS/i', $sql)){
            $sql = preg_replace('/SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $sql, 1);
        }
        return $sql;
    }

    /**
     * @override
     */
    public function escapeColumn($column_name)
    {
        return '`' . $column_name . '`';
    }

    /**
     * @implements
     */
    public function getTotalRows($sql, $params=array())
    {
        $stmt = $this->query('SELECT FOUND_ROWS()');
        $row  = $stmt->fetch(PDO::FETCH_NUM);
        return $row[0];
    }


    /**
     * @implements
     */
    public function getTableInfo($table_name)
    {
        //初期化
        $result = array();
        if($table_name === NULL || $table_name == '' || !is_string($table_name)){
            return $result;
        }
        
        //取得
        $sql = "DESCRIBE `{$table_name}`";
        $stmt = $this->query($sql);
        $table_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        //整形
        foreach($table_info as $_key => $info){
            //カラム名の取得
            $name = $info['Field'];
            
            //フィールド型の取得
            if(preg_match('/^(.+)\((.+)\)$/', $info['Type'], $matches)){
                $type   = $matches[1];
                $length = $matches[2];
            } else {
                $type   = $info['Type'];
                $length = NULL;
            }
            
            //NULL値の判断
            $null = ($info['Null']=='YES') ? true : false ;
            
            //キーの取得
            $primary_key = preg_match('/PRI/', $info['Key']) ? true : false ;
            
            //デフォルト値の取得
            $default = $info['Default'];
            
            //その他フラグの取得
            $extras = array();
            if(preg_match('/auto_increment/', $info['Extra'])){
                $extras[] = 'auto_increment';
            }
            $extras = join(' ', $extras);
            
            //値の生成
            $result[$name] = array(
                'table'       => $table_name,
                'name'        => $name,
                'type'        => $type,
                'length'      => $length,
                'null'        => $null,
                'primary_key' => $primary_key,
                'default'     => $default,
                'extras'      => $extras,
            );
        }
        
        return $result;
    }
}

