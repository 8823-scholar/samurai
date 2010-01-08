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
 * モデルクラス
 *
 * ActiveGatewayとより密に連携するための機能が盛り込まれている
 * 以前、Samurai_Logicとして用意していたものを、世間での呼称に合わせる
 * Samurai_Logicは今後使わずに、こちらを使用するようにしてください
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Model
{
    /**
     * ActiveGateway
     *
     * @access   public
     * @var      object
     */
    public $AG;

    /**
     * テーブル名
     *
     * @access   protected
     * @var      string
     */
    protected $_table;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * saveメソッドは共通
     *
     * @access     public
     * @param      object  $record   ActiveGatewayRecord
     */
    public function save(ActiveGatewayRecord $record)
    {
        return $this->AG->save($record);
    }


    /**
     * destroyメソッドも共通
     *
     * @access     public
     * @param      object  $record   ActiveGatewayRecord
     */
    public function destroy(ActiveGatewayRecord $record)
    {
        return $this->AG->destroy($record);
    }





    /**
     * ActiveGateway::findへのブリッジ
     *
     * @access     protected
     * @param      string  $table
     * @param      mixed   $condition
     */
    protected function _find($table, $condition)
    {
        $table = $this->_makeTableName($table);
        if(is_scalar($condition)){
            return $this->AG->find($table, $condition);
        }
        if(is_object($condition) || is_array($condition)){
            $this->_initAGCondition($condition);
            return $this->AG->findDetail($table, $condition);
        }
    }


    /**
     * ActiveGateway::findByへのブリッジ
     *
     * @access     protected
     * @param      string  $table
     * @param      string  $column
     * @param      mixed   $value
     */
    protected function _findBy($table, $column, $value)
    {
        $table = $this->_makeTableName($table);
        return $this->AG->findBy($table, $column, $value);
    }


    /**
     * ActiveGateway::findAllDetailへのブリッジ
     *
     * @access     protected
     * @param      string  $table
     * @param      object  $condition
     */
    protected function _findAll($table, $condition = NULL)
    {
        $table = $this->_makeTableName($table);
        $this->_initAGCondition($condition);
        return $this->AG->findAllDetail($table, $condition);
    }


    /**
     * ActiveGateway::findAllByへのブリッジ
     *
     * @access     protected
     * @param      string  $table
     * @param      string  $column
     * @param      mixed   $value
     * @param      object  $condition
     */
    protected function _findAllBy($table, $column, $value, $condition = NULL)
    {
        $table = $this->_makeTableName($table);
        $this->_initAGCondition($condition);
        return $this->AG->findAllBy($table, $column, $value, $condition);
    }


    /**
     * ActiveGateway::updateへのブリッジ
     *
     * @access     protected
     * @param      string  $table
     * @param      object  $condition
     * @param      array | object   $attributes
     */
    protected function _update($table, $condition, $attributes)
    {
        $table = $this->_makeTableName($table);
        if(is_scalar($condition)){
            return $this->AG->update($table, $condition, $attributes);
        }
        if(is_object($condition) || is_array($condition)){
            $this->_initAGCondition($condition);
            return $this->AG->updateDetail($table, $attributes, $condition);
        }
    }


    /**
     * ActiveGateway::updateAllへのブリッジ
     *
     * @access     protected
     * @param      string  $table
     * @param      object  $condition
     * @param      array | object   $attributes
     */
    protected function _updateAll($table, $condition, $attributes)
    {
        $table = $this->_makeTableName($table);
        $this->_initAGCondition($condition);
        return $this->AG->updateAllDetail($table, $attributes, $condition);
    }


    /**
     * テーブル名を作成する
     *
     * @access     private
     * @param      string  $table
     * @return     string
     */
    protected function _makeTableName($table)
    {
        $tables = preg_split('/(?=[A-Z])/', $table);
        array_shift($tables);
        array_unshift($tables, $this->_table);
        return strtolower(join('_', $tables));
    }


    /**
     * 未定義の関数がコールされた場合、キャッチ
     *
     * find, findBy, findAll, findAllBy, update, save系のメソッドがコールされると機能する
     * また、これらのメソッドは動的に名前を変化する
     *
     * @access     public
     * @param      string  $name
     * @param      array   $arguments
     * @return     mixed
     */
    public function __call($name, $arguments)
    {
        //find系
        if(preg_match('/^(findAllBy)(.*)?/i', $name, $matches)
            || preg_match('/^(findAll)(.*)?/i', $name, $matches)
            || preg_match('/^(findBy)(.*)?/i', $name, $matches)
            || preg_match('/^(find)(.*)?/i', $name, $matches)){
            array_unshift($arguments, isset($matches[2]) ? $matches[2] : '');
            return call_user_method_array('_' . $matches[1], $this, $arguments);
        }
        //update
        if(preg_match('/^(updateAll)(.*)?/i', $name, $matches)
            || preg_match('/^(update)(.*)?/i', $name, $matches)){
            array_unshift($arguments, isset($matches[2]) ? $matches[2] : '');
            return call_user_method_array('_' . $matches[1], $this, $arguments);
        }
        //delete
        
        //error
        trigger_error('undefined method.', E_USER_WARNING);
    }





    /**
     * AGConditionの初期化を行う
     *
     * @access     protected
     * @param      object  $AGdto   ActiveGatewayCondition
     */
    protected function _initAGCondition(&$AGdto)
    {
        if(is_array($AGdto)){
            $_AGdto = ActiveGateway::getCondition();
            foreach($AGdto as $_key => $_val){
                $_AGdto->$_key = $_val;
            }
            $AGdto = $_AGdto;
        } elseif(is_object($AGdto)){
            if(!$AGdto instanceof ActiveGatewayCondition){
                $_AGdto = ActiveGateway::getCondition();
                foreach($AGdto as $_key => $_val){
                    $_AGdto->$_key = $_val;
                }
                $AGdto = $_AGdto;
            }
        } else {
            $AGdto = ActiveGateway::getCondition();
        }
    }


    /**
     * ActiveGateway::getConditionへのブリッジ
     *
     * @access     public
     * @return     object  ActiveGatewayCondition
     */
    public function getCondition()
    {
        return ActiveGateway::getCondition();
    }
}

