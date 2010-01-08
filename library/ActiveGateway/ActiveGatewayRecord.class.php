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
 * ActiveGatewayで使用するレコードを表すクラス
 * 
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGatewayRecord
{
    /**
     * オリジナルの値を保持
     *
     * @access   private
     * @var      array
     */
    private $_original = array();

    /**
     * 新規レコードかどうか
     *
     * @access   private
     * @var      boolean
     */
    private $_new_record = true;

    /**
     * 基本エイリアス
     *
     * @access   private
     * @var      string
     */
    private $_base_alias = '';

    /**
     * テーブル名
     *
     * @access   private
     * @var      string
     */
    private $_table_name = '';

    /**
     * テーブル情報
     *
     * @access   private
     * @var      array
     */
    private $_table_info = array();

    /**
     * プライマリキー
     *
     * @access   private
     * @var      string
     */
    private $_primary_key = 'id';


    /**
     * コンストラクタ
     *
     * @access     public
     * @param      mixed   $row          PDOStatement->fetchで取得した値
     * @param      boolean $new_record   新規レコードかどうかの判断値
     * @param      string  $alias        仮想テーブル名
     */
    public function __construct($row = NULL, $new_record = true, $alias = NULL)
    {
        //$rowの展開
        if($row !== NULL){
            if(!is_array($row) && !is_object($row)){
                trigger_error('[ActiveGatewayRecord]:$row is Not Array');
            }
            foreach($row as $_key => $_val){
                if(!preg_match('/^_/', $_key)){
                    $this->$_key = $_val;
                    $this->_original[$_key] = $_val;
                }
            }
        }
        //その他情報のセット
        $this->_new_record = $new_record;
        $this->_base_alias = $alias;
        $this->_table_name = $alias;
    }



    /**
     * テーブル名の設定
     *
     * @access     public
     * @param      string  $table_name   テーブル名
     */
    public function setTableName($table_name)
    {
        $this->_table_name = (string)$table_name;
    }

    /**
     * テーブル名の取得
     *
     * @access     public
     * @return     string
     */
    public function getTableName()
    {
        return $this->_table_name;
    }


    /**
     * テーブル情報の設定
     *
     * @access     public
     * @param      array   $table_info   テーブル情報
     */
    public function setTableInfo($table_info)
    {
        $this->_table_info = $table_info;
        foreach($table_info as $key => $attributes){
            if(!array_key_exists($key, $this->_original)){
                $this->_original[$key] = $attributes['default'];
            }
        }
    }


    /**
     * プライマリキーの設定
     *
     * @access     public
     * @param      string  $primary_key   プライマリキー名
     */
    public function setPrimaryKey($primary_key)
    {
        $this->_primary_key = (string)$primary_key;
    }

    /**
     * プライマリキーの取得
     *
     * @access     public
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->_primary_key;
    }

    /**
     * プライマリキーの値の取得
     *
     * @access     public
     * @return     mixed   プライマリキーの値
     */
    public function getPrimaryValue()
    {
        $primary_key = $this->getPrimaryKey();
        return $this->$primary_key;
    }


    /**
     * Alias名の取得
     *
     * @access     public
     * @return     string
     */
    public function getAlias()
    {
        return $this->_base_alias;
    }


    /**
     * Attributesの取得
     *
     * @access     public
     * @param      boolean $updated   更新されたものに限定するかどうか
     * @return     array
     */
    public function getAttributes($updated = false)
    {
        $attributes = get_object_vars($this);
        foreach($attributes as $_key => $_val){
            if(preg_match('/^_/', $_key)) unset($attributes[$_key]);
            if($updated){
                if(!array_key_exists($_key, $this->_original) || $attributes[$_key] == $this->_original[$_key]){
                    unset($attributes[$_key]);
                }
            }
        }
        return $attributes;
    }


    /**
     * オリジナルの値取得
     *
     * @access     public
     * @param      string  $key   キー
     * @return     mixed   オリジナルの値
     */
    public function getOriginalValue($key)
    {
        switch($key){
            case 'primary_key':
                $value = $this->_original[$this->getPrimaryKey()];
                break;
            default:
                if(isset($this->_original[$key])){
                    $value = $this->_original[$key];
                } else {
                    $value = NULL;
                }
                break;
        }
        return $value;
    }



    /**
     * 新規レコードかどうかの判断
     *
     * @access     public
     * @return     boolean
     */
    public function isNewRecord()
    {
        return $this->_new_record;
    }


    /**
     * 指定のフィールを保持しているかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function hasField($field_name)
    {
        return isset($this->_table_info[$field_name]);
    }


    /**
     * ActiveGatewayによる、saveが可能であるかを返却
     *
     * 判断基準は、テーブル名が取得できるかどうかである。
     * 現在、findSqlもしくはfindAllSqlを直接使用すると、このsaveは使用できないようになっている。
     * テーブル名(Alias名)が取得しにくいからだ。
     *
     * @access     public
     * @return     boolean
     */
    public function enableSave()
    {
        if($this->_table_name === NULL) return false;
        return true;
    }

    /**
     * enableSaveのシノニム
     *
     * @access     public
     * @return     boolean
     */
    public function isSavable()
    {
        return $this->enableSave();
    }


    /**
     * 論理削除が可能かどうか
     * 判断基準は、規約通りに「active」フィールドがあるかどうかだ。
     *
     * @access     public
     * @return     boolean
     */
    public function enableDeleteByLogical()
    {
        return isset($this->_table_info['active']);
    }


    /**
     * 配列に変換する
     *
     * @access     public
     * @return     array
     */
    public function toArray()
    {
        return ActiveGatewayUtils::object2Array($this->getAttributes(), true);
    }
}

