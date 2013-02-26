<?php
/**
 * The MIT License
 *
 * Copyright (c) 2007-2013, Samurai Framework Project, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri\Condition;

/**
 * Onikiri Condition class.
 *
 * $cond->select('hoge');
 * $cond->where->add('name = ?', $name);
 * $cond->orderBy('id DESC');
 * $cond->groupBy('gender');
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Condition
{
    /**
     * select section.
     *
     * @access  public
     * @var     Samurai\Onikiri\Condition\SelectCondition
     */
    public $select;

    /**
     * where section.
     *
     * @access  public
     * @var     Samurai\Onikiri\Condition\WhereCondition
     */
    public $where;

    /**
     * from section.
     *
     * @access  public
     * @var     Samurai\Onikiri\Condition\FromCondition
     */
    public $from;

    /**
     * order section.
     *
     * @access  public
     * @var     Samurai\Onikiri\Condition\OrderCondition
     */
    public $order;

    /**
     * having section.
     *
     * @access  public
     * @var     Samurai\Onikiri\Condition\HavingCondition
     */
    public $having;

    /**
     * limit.
     *
     * @access  public
     * @var     int
     */
    public $limit = null;

    /**
     * offset.
     *
     * @access  public
     * @var     int
     */
    public $offset = null;

    /**
     * params
     *
     * @access  public
     * @var     array
     */
    public $params = array();

    /**
     * Model
     *
     * @access  public
     * @var     Samurai\Onikiri\Model
     */
    public $model;


    /**
     * constructor.
     *
     * @access  public
     * @param   array   $define
     */
    public function __construct(array $define = array())
    {
        // init.
        $this->select = new SelectCondition($this);
        $this->from = new FromCondition($this);
        $this->where = new WhereCondition($this);
        $this->group = new GroupCondition($this);
        $this->order = new OrderCondition($this);

        // initialize define.
        foreach ( $define as $key => $value ) {
        }
    }


    /**
     * set model.
     *
     * @access  public
     * @param   Samurai\Onikiri\Model   $model
     */
    public function setModel(\Samurai\Onikiri\Model $model)
    {
        $this->model = $model;
    }





    /**
     * select
     *
     * @access  public
     */
    public function select()
    {
        $args = func_get_args();
        while ( $arg = array_shift($args) ) {
            $this->select->add($arg);
        }
        return $this->select;
    }

    /**
     * from
     *
     * @access  public
     */
    public function from()
    {
        $args = func_get_args();
        while ( $arg = array_shift($args) ) {
            $this->from->add($arg);
        }
        return $this->from;
    }


    /**
     * where
     *
     * @access  public
     */
    public function where()
    {
        return call_user_func_array(array($this->where, 'add'), func_get_args());
    }


    /**
     * group
     *
     * @access  public
     */
    public function groupBy()
    {
        $args = func_get_args();
        while ( $arg = array_shift($args) ) {
            $this->group->add($arg);
        }
        return $this->group;
    }


    /**
     * order
     *
     * @access  public
     */
    public function orderBy()
    {
        $args = func_get_args();
        while ( $arg = array_shift($args) ) {
            $this->order->add($arg);
        }
        return $this->order;
    }

    /**
     * order by field.
     *
     * @access  public
     */
    public function orderByField()
    {
        call_user_func_array(array($this->order, 'addByField'), func_get_args());
        return $this->order;
    }





    /**
     * Set limit.
     *
     * @access  public
     * @param   int     $limit
     * @return  Condition
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }


    /**
     * Set offset.
     *
     * @access  public
     * @param   int     $offset
     * @return  Condition
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set page.
     *
     * @access  public
     * @param   int     $page
     */
    public function page($page)
    {
        $offset = $this->limit * ( $page - 1 );
        $this->offset($offset);
        return $this;
    }


    /**
     * get all params.
     *
     * @access  public
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * add param
     *
     * @access  public
     * @param   mixed   $value
     * @param   string  $key
     */
    public function addParam($value, $key = null)
    {
        if ( $key !== null && ! is_numeric($key) ) {
            $this->params[$key] = $value;
        } else {
            $this->params[] = $value;
        }
    }

    /**
     * append params
     *
     * @access  public
     * @param   array   $params
     */
    public function appendParams(array $params = array())
    {
        $this->params = array_merge($this->params, $params);
    }



    /**
     * import from array.
     *
     * @access  public
     * @param   array   $condition
     */
    public function import(array $condition = array())
    {
        foreach ( $condition as $key => $value ) {
            switch ( strtolower($key) ) {
                case 'where':
                    call_user_func_array(array($this->where, 'add'), $value);
                    break;
                case 'group':
                    $this->groupBy($value);
                    break;
                case 'order':
                    $this->orderBy($value);
                    break;
            }
        }
    }




    /**
     * bridge to model find.
     *
     * @access  public
     * @return  Samurai\Onikiri\Entity
     */
    public function find()
    {
        return $this->model->find($this);
    }

    /**
     * bridge to model findAll
     *
     * @access  public
     * @return  Samurai\Onikiri\Entities
     */
    public function findAll()
    {
        return $this->model->findAll($this);
    }




    /**
     * convert to SQL.
     *
     * @access  public
     * @return  string
     */
    public function toSQL()
    {
        $sql = array();
        $this->params = array();

        $sql[] = $this->select->toSQL();
        $sql[] = $this->from->toSQL();
        $sql[] = $this->where->toSQL();
        $this->appendParams($this->where->getParams());
        $sql[] = $this->group->toSQL();
        $this->appendParams($this->group->getParams());
        $sql[] = $this->order->toSQL();
        $this->appendParams($this->order->getParams());

        // TODO: Helper
        if ( $this->limit !== null ) {
            $sql[] = 'LIMIT ?';
            $this->params[] = $this->limit;
        }
        if ( $this->offset !== null ) {
            $sql[] = 'OFFSET ?';
            $this->params[] = $this->offset;
        }

        return join(' ', $sql);
    }


    /**
     * generate bind key.
     *
     * @access  public
     * @param   string  $key
     * @return  string
     */
    public function makeBindKey($key = null)
    {
        $bind = ( $key ? $key : '' );
        $bind = $bind . md5(uniqid());
        return $bind;
    }
}

