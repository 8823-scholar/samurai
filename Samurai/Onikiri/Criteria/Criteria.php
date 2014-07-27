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

namespace Samurai\Onikiri\Criteria;

use Samurai\Onikiri\EntityTable;

/**
 * Onikiri criteria class.
 *
 * $criteria->columns('hoge', 'foo');
 * $criteria->where->add('name = ?', $name);
 * $criteria->orderBy('id DESC');
 * $criteria->groupBy('gender');
 *
 * @package     Samurai.Onikiri
 * @subpackage  Criteria
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Criteria
{
    /**
     * params
     *
     * @var     array
     */
    public $params = array();

    /**
     * EntityTable instance
     *
     * @var     Samurai\Onikiri\EntityTable
     */
    public $table;

    /**
     * where condition
     *
     * @var     Samurai\Onikiri\Criteria\WhereCondition
     */
    public $where;

    /**
     * limit condition.
     *
     * @var     int
     */
    public $limit;

    /**
     * offset condition.
     *
     * @var     int
     */
    public $offset;


    /**
     * constructor.
     *
     * @access  public
     * @param   Samurai\Onikiri\EntityTable
     */
    public function __construct(EntityTable $table)
    {
        $this->setTable($table);

        $this->where = new WhereCondition($this);
    }


    /**
     * set table.
     *
     * @param   Samurai\Onikiri\EntityTable $table
     */
    public function setTable(\Samurai\Onikiri\EntityTable $table)
    {
        $this->table = $table;
    }

    /**
     * get table.
     *
     * @return  Samurai\Onikiri\EntityTable
     */
    public function getTable()
    {
        return $this->table;
    }
    
    
    /**
     * where
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function where()
    {
        return call_user_func_array(array($this->where, 'andAdd'), func_get_args());
    }

    /**
     * where in
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function whereIn()
    {
        return call_user_func_array(array($this->where, 'andIn'), func_get_args());
    }

    /**
     * where not in
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function whereNotIn()
    {
        return call_user_func_array(array($this->where, 'andNotIn'), func_get_args());
    }
    
    /**
     * where between
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function whereBetween()
    {
        return call_user_func_array(array($this->where, 'andBetween'), func_get_args());
    }

    /**
     * where not between
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function whereNotBetween()
    {
        return call_user_func_array(array($this->where, 'andNotBetween'), func_get_args());
    }


    /**
     * bind params
     *
     * @param   array   $params
     * @return  Samurai\Onikiri\Criteria
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $this->params[$key] = $value;
            } else {
                $this->params[] = $value;
            }
        }
        return $this;
    }

    /**
     * add param
     *
     * @param   mixed   $value
     * @param   string  $key
     */
    public function addParam($value, $key = null)
    {
        if ($key !== null && ! is_numeric($key)) {
            $this->params[$key] = $value;
        } else {
            $this->params[] = $value;
        }
    }

    /**
     * get all params.
     *
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    
    /**
     * Set limit.
     *
     * @param   int     $limit
     * @return  Samurai\Onikiri\Criteria\Criteria
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set offset.
     *
     * @param   int     $offset
     * @return  Samurai\Onikiri\Criteria\Criteria
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set page.
     *
     * @param   int     $page
     */
    public function page($page)
    {
        if (! $this->limit) return;
        $offset = $this->limit * ($page - 1);
        $this->offset($offset);
        return $this;
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
     * bridge to table find.
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function find()
    {
        return $this->table->find($this);
    }

    /**
     * bridge to table findAll
     *
     * @return  Samurai\Onikiri\Entities
     */
    public function findAll()
    {
        return $this->table->findAll($this);
    }

    /**
     * bridge to table update
     *
     * @param   array   $attributes
     */
    public function update($attributes = [])
    {
        return $this->table->update($attributes, $this);
    }

    /**
     * bridge to table delete
     */
    public function delete()
    {
        return $this->table->delete($this);
    }


    /**
     * convert to SQL.
     *
     * @return  string
     */
    public function toSQL()
    {
        $sql = [];
        $this->params = [];

        $sql[] = 'SELECT *';
        $sql[] = 'FROM ' . $this->table->getTableName();
        $sql[] = $this->where->toSQL();
        /*
        $sql[] = $this->group->toSQL();
        $this->appendParams($this->group->getParams());
        $sql[] = $this->order->toSQL();
        $this->appendParams($this->order->getParams());
         */

        if ($this->limit !== null) {
            $sql[] = 'LIMIT ?';
            $this->params[] = $this->limit;
        }
        if ($this->offset !== null) {
            $sql[] = 'OFFSET ?';
            $this->params[] = $this->offset;
        }

        return join(' ', $sql);
    }


    /**
     * convert to update SQL.
     *
     * @access  public
     * @param   array   $attributes
     * @return  string
     */
    public function toUpdateSQL($attributes = array())
    {
        $sql = [];
        $this->params = [];
        
        $sql[] = sprintf('UPDATE %s SET', $this->table->getTableName());
        $setts = [];
        foreach ($attributes as $key => $value) {
            $setts[] = sprintf('%s = ?', $key);
            $this->addParam($value);
        }
        $sql[] = join(', ', $setts);
        $sql[] = $this->where->toSQL();

        return join(' ', $sql);
    }
    
    /**
     * convert to insert SQL.
     *
     * @param   array   $attributes
     * @return  string
     */
    public function toInsertSQL($attributes = array())
    {
        $sql = [];
        $this->params = [];
        
        $sql[] = sprintf('INSERT INTO %s', $this->table->getTableName());
        $keys = [];
        $values = [];
        foreach ($attributes as $key => $value) {
            $keys[] = $key;
            $values[] = '?';
            $this->addParam($value);
        }
        $sql[] = '(' . join(', ', $keys) . ')';
        $sql[] = 'VALUES (' . join(', ', $values) . ')';
        return join(' ', $sql);
    }
    
    /**
     * convert to delete SQL.
     *
     * @return  string
     */
    public function toDeleteSQL()
    {
        $sql = [];
        $this->params = [];
        
        $sql[] = sprintf('DELETE FROM %s', $this->table->getTableName());
        $sql[] = $this->where->toSQL();

        return join(' ', $sql);
    }
}

