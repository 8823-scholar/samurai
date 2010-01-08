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
 * ActiveGatewayにおいて、レコードの集合を管理するクラス
 *
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGatewayRecords implements Iterator
{
    /**
     * レコードの集合
     *
     * @access   private
     * @var      array
     */
    private $_records = array();

    /**
     * レコードの参照index
     *
     * @access   private
     * @var      int
     */
    private $_index = 0;

    /**
     * 総レコード数
     *
     * @access   private
     * @var      int
     */
    private $_total_rows = 0;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * レコードを追加していく
     *
     * @access     public
     * @param      object  $record   ActiveGatewayRecord
     */
    public function addRecord($record)
    {
        $this->_records[] = $record;
    }


    /**
     * レコードを回していく
     *
     * @access     public
     * @return     object  ActiveGatewayRecord
     */
    public function fetch()
    {
        if($this->valid()){
            $Record = $this->current();
            $this->next();
            return $Record;
        } else {
            $this->rewind();
            return false;
        }
    }


    /**
     * レコードを回して、かつそれを削除する
     *
     * @access     public
     */
    public function fetchAndRemove()
    {
        $record = array_shift($this->_records);
        return $record;
    }


    /**
     * 最初のレコードを返却する
     *
     * @access     public
     * @return     object  ActiveGatewayRecord
     */
    public function getFirstRecord()
    {
        if(!isset($this->_records[0])){
            return null;
        } else {
            return $this->_records[0];
        }
    }

    /**
     * 最後のレコードを返却する
     *
     * @access     public
     * @return     object  ActiveGatewayRecord
     */
    public function getLastRecord()
    {
        if(!$this->getSize()){
            return null;
        } else {
            return $this->_records[$this->getSize()-1];
        }
    }


    /**
     * 無作為に1レコードを取り出す
     *
     * @access     public
     * @return     object   ActiveGatewayRecord
     */
    public function getRandom()
    {
        if(!$this->_records) return;
        $key = array_rand($this->_records);
        return $this->_records[$key];
    }





    /**
     * 総レコード数を返却する
     *
     * @access     public
     * @return     int
     */
    public function getTotalRows()
    {
        return $this->_total_rows;
    }

    /**
     * 総レコード数をセットする
     *
     * @access     public
     * @param      int     $total_rows   総レコード数
     */
    public function setTotalRows($total_rows)
    {
        if(!$total_rows) $total_rows = 0;
        $this->_total_rows = (int)$total_rows;
    }


    /**
     * 現在格納されているレコードのサイズを返却
     *
     * @access     public
     * @return     int
     */
    public function getSize()
    {
        return count($this->_records);
    }


    /**
     * 先頭のアイテムかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function isFirst()
    {
        return $this->_index === 0;
    }

    /**
     * 最後のアイテムかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function isLast()
    {
        return $this->_index === $this->getSize() - 1;
    }



    /**
     * 配列に変換する
     *
     * @access     public
     * @return     array
     */
    public function toArray()
    {
        $result = array();
        foreach($this as $record){
            $result[] = ActiveGatewayUtils::object2Array($record, true);
        }
        return $result;
    }





    /**
     * @implements
     */
    public function rewind()
    {
        $this->_index = 0;
        reset($this->_records);
    }

    /**
     * @implements
     */
    public function key()
    {
        return key($this->_records);
    }

    /**
     * @implements
     */
    public function current()
    {
        return current($this->_records);
    }

    /**
     * @implements
     */
    public function next()
    {
        $this->_index++;
        next($this->_records);
    }

    /**
     * @implements
     */
    public function valid()
    {
        $result = isset($this->_records[$this->_index]);
        if(!$result) $this->rewind();
        return $result;
    }
}

