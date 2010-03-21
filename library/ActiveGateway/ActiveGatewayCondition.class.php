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
 * ActiveGatewayにおいて、検索条件を保持するクラス
 *
 * <code>
 *     $condition = ActiveGateway::getCondition();
 *     $condition->where->foo = 'bar';
 *     $condition->where->foo = $condition->isNotEqual('bar');
 *     $condition->where->bar = $condition->isGreaterThan(10, true);
 *     $condition->where->bar = $condition->isLessThan(10, false);
 * </code>
 * 
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGatewayCondition
{
    /**
     * SELECT
     *
     * @access   public
     * @var      mixed
     */
    public $select = NULL;

    /**
     * FROM
     *
     * @access   public
     * @var      mixed
     */
    public $from = NULL;

    /**
     * WHERE
     *
     * @access   public
     * @var      mixed
     */
    public $where = NULL;

    /**
     * WHERE(addtional)
     *
     * @access   public
     * @var      mixed
     */
    public $addtional_where = '';

    /**
     * ORDER
     *
     * @access   public
     * @var      mixed
     */
    public $order = NULL;

    /**
     * GROUP
     *
     * @access   public
     * @var      mixed
     */
    public $group = NULL;

    /**
     * LIMIT
     *
     * @access   public
     * @var      int
     */
    public $limit = NULL;

    /**
     * OFFSET
     *
     * @access   public
     * @var      int
     */
    public $offset = NULL;

    /**
     * トータルローズを取得するかどうか
     *
     * @access   public
     * @var      boolean
     */
    public $total_rows = true;

    /**
     * 「active」フィールドを考慮するかどうか
     *
     * @access   public
     * @var      boolean
     */
    public $regard_active = true;



    /**
     * リミットをセット
     *
     * @access     public
     * @param      int     $limit   リミット
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
    }


    /**
     * offset値の設定
     *
     * offsetというか、pageIDをセットする感じ。
     * 性質上、リミットをセットした後でないと意味を発揮しない。
     * is_pageid=falseの場合、裸の値がoffset値となる。
     *
     * @access     public
     * @param      int      $offset
     * @param      boolean  $is_pageid
     */
    public function setOffset($offset, $is_pageid = true)
    {
        $offset = (int)$offset;
        if($is_pageid){
            $offset = ($offset > 0) ? $offset - 1 : 0 ;
            $offset = (int)$this->limit * $offset;
        }
        if($offset < 0) $offset = 0;
        $this->offset = $offset;
    }



    /**
     * = 比較
     *
     * @access     public
     * @param      mixed    $value
     * @return     object   ActiveGatewayCondition_Value
     */
    public function isEqual($value)
    {
        $obj = new ActiveGatewayCondition_Value($value);
        if($value === NULL) $obj->override = 'IS NULL';
        return $obj;
    }

    /**
     * != 比較
     *
     * @access     public
     * @param      mixed    $value
     * @return     object   ActiveGatewayCondition_Value
     */
    public function isNotEqual($value)
    {
        $obj = new ActiveGatewayCondition_Value($value);
        $obj->operator = '!=';
        if($value === NULL) $obj->override = 'IS NOT NULL';
        return $obj;
    }

    /**
     * >= 比較
     *
     * @access     public
     * @param      mixed    $value
     * @param      boolean  $within    =がつくかどうか
     * @return     object   ActiveGatewayCondition_Value
     */
    public function isGreaterThan($value, $within = true)
    {
        $obj = new ActiveGatewayCondition_Value($value);
        $obj->operator = '>';
        if($within) $obj->operator = '>=';
        return $obj;
    }

    /**
     * <= 比較
     *
     * @access     public
     * @param      mixed    $value
     * @param      boolean  $within   =がつくかどうか
     */
    public function isLessThan($value, $within = true)
    {
        $obj = new ActiveGatewayCondition_Value($value);
        $obj->operator = '<';
        if($within) $obj->operator = '<=';
        return $obj;
    }

    /**
     * LIKE 比較
     *
     * @access     public
     * @param      mixed    $value
     * @return     object   ActiveGatewayCondition_Value
     */
    public function isLike($value)
    {
        $obj = new ActiveGatewayCondition_Value($value);
        $obj->operator = 'LIKE';
        return $obj;
    }

    /**
     * BIT演算 AND
     *
     * @access     public
     * @param      mixed    $value
     * @return     object   ActiveGatewayCondition_Value
     */
    public function isBitAnd($value)
    {
        $obj = new ActiveGatewayCondition_Value($value);
        $obj->operator = '&';
        return $obj;
    }


    /**
     * AND連結
     *
     * @access     public
     * @param      mixed     $value   ...
     * @return     object   ActiveGatewayCondition_Values
     */
    public function isAnd()
    {
        $obj = new ActiveGatewayCondition_Values();
        $obj->operator = 'AND';
        foreach(func_get_args() as $arg){
            $obj->append($arg);
        }
        return $obj;
    }

    /**
     * OR連結
     *
     * @access     public
     * @param      mixed     $value   ...
     * @return     object   ActiveGatewayCondition_Values
     */
    public function isOr()
    {
        $obj = new ActiveGatewayCondition_Values();
        $obj->operator = 'OR';
        foreach(func_get_args() as $arg){
            $obj->append($arg);
        }
        return $obj;
    }
}





/**
 * ActiveGatewayの条件の値を体現するクラス
 * 
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGatewayCondition_Value
{
    /**
     * 値
     *
     * @access   public
     * @var      mixed
     */
    public $value = NULL;

    /**
     * 比較演算子
     *
     * @access   public
     * @var      string
     */
    public $operator = '=';

    /**
     * 比較演算子とかではなく、値も含めて比較文を総上書きしたい際に使用
     * (IS NULLなど)
     *
     * @access   public
     * @var      string
     */
    public $override = '';


    public function __construct($value = NULL)
    {
        $this->value = $value;
    }
}





/**
 * ActiveGatewayの条件の値の集合を体現するクラス
 * 
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGatewayCondition_Values
{
    /**
     * 値
     *
     * @access   public
     * @var      array
     */
    public $values = array();

    /**
     * 連結演算子
     *
     * @access   public
     * @var      string
     */
    public $operator = 'AND';


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
    }


    /**
     * 値を追加
     *
     * @access     public
     * @param      mixed
     * @return     object   ActiveGatewayCondition_Value
     */
    public function append($value)
    {
        $this->values[] = $value;
    }
}
