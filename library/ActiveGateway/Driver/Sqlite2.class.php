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
 * ActiveGatewayのSqlite2用ドライバー
 * 
 * @package    ActiveGateway
 * @subpackage Driver
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGateway_Driver_Sqlite2 extends ActiveGateway_Driver
{
    /**
     * @implements
     */
    protected function _connect($dsn_info)
    {
        $scheme = $dsn_info['scheme'];
        $path   = $dsn_info['path'];
        try {
            $connection = new PDO("$scheme:$path", '', '');
        } catch(PDOException $Exception){
            trigger_error(sprintf('[ActiveGateway_Driver_Sqlite2]:Connection failed... -> [%s]', $Exception->getMessage()), E_USER_ERROR);
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
            $field_list[] = $_key;
            $value_list[] = '?';
            $params[$i] = $_val;
            $i++;
        }
        return sprintf('INSERT INTO %s ( %s ) VALUES ( %s )', $table_name, join(', ', $field_list), join(', ', $value_list));
    }

    /**
     * @implements
     */
    public function modifyUpdateQuery($table_name, $sets, $wheres = array(), $orders = array())
    {
        $sql  = sprintf('UPDATE %s SET %s', $table_name, join(', ', $sets));
        $sql .= ($wheres) ? sprintf(' WHERE %s', join(' AND ', $wheres)) : '' ;
        return $sql;
    }

    /**
     * @implements
     */
    public function modifyDeleteQuery($table_name, $wheres = array(), $orders = array())
    {
        $sql  = sprintf('DELETE FROM %s', $table_name);
        $sql .= ($wheres) ? sprintf(' WHERE %s', join(' AND ', $wheres)) : '' ;
        return $sql;
    }

    /**
     * @implements
     */
    public function modifyUpdateLimitQuery($sql, $limit = NULL)
    {
        //this database can't use limit update!
        return $sql;
    }

    /**
     * @implements
     */
    public function modifyFoundRowsQuery($sql)
    {
        //this database can't use SELECT FOUND_ROWS()!!
        return $sql;
    }

    /**
     * @override
     */
    public function modifyAttributes($table_info, &$attributes = array())
    {
        foreach($table_info as $_key => $_val){
            if($_val['default'] === NULL && !$_val['null'] && !isset($attributes[$_key])){
                switch(strtoupper($_val['type'])){
                    case 'TINYINT':
                    case 'SMALLINT':
                    case 'MEDIUMINT':
                    case 'INT':
                    case 'INTENGER':
                    case 'BIGINT':
                    case 'FLOAT':
                    case 'DOUBLE':
                    case 'DECIMAL':
                        $attributes[$_key] = 0; break;
                    case 'CHAR':
                    case 'VARCHAR':
                    case 'TINYBLOB':
                    case 'TINYTEXT':
                    case 'BLOB':
                    case 'TEXT':
                    case 'MEDIUMBLOB':
                    case 'MEDIUMTEXT':
                    case 'LONGBLOB':
                    case 'LONGTEXT':
                        $attributes[$_key] = ''; break;
                    case 'DATE':
                        $attributes[$_key] = date('Y-m-d', 0); break;
                    case 'DATETIME':
                    case 'TIMESTAMP':
                        $attributes[$_key] = date('Y-m-d H:i:s', 0); break;
                    case 'TIME':
                        $attributes[$_key] = date('H:i:s', 0); break;
                    case 'YEAR':
                        $attributes[$_key] = date('Y', 0); break;
                }
            }
        }
    }


    /**
     * @implements
     */
    public function getTotalRows($sql, $params = array())
    {
        $stmt = $this->query($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return is_array($rows) ? count($rows) : 0 ;
    }


    /**
     * @implements
     */
    public function getTableInfo($table_name)
    {
        $result = array();
        
        if($table_name === NULL || $table_name == '' || !is_string($table_name)){
            return $result;
        }
        
        $sql = "PRAGMA table_info('$table_name')";
        $stmt = $this->_connection->query($sql);
        
        $table_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($table_info as $_key => $info){
            //カラム名の取得
            $name = $info['name'];
            
            //フィールド型の取得
            if(preg_match('/^(.+)\((.+)\)$/', $info['type'], $matches)){
                $type   = $matches[1];
                $length = $matches[2];
            } else {
                $type   = $info['type'];
                $length = 0;
            }
            
            //NULL値の判断
            $null = ($info['notnull']) ? false : true ;
            
            //キーの取得
            $primary_key = ($info['pk']) ? true : false ;
            
            //デフォルト値の取得
            $default = $info['dflt_value'];
            
            //その他フラグの取得
            $extras = array();
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

