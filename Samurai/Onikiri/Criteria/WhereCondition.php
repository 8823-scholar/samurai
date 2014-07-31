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

/**
 * Where section condition.
 *
 * @package     Samurai.Onikiri
 * @subpackage  Criteria
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class WhereCondition extends BaseCondition
{
    /**
     * chain by "AND".
     *
     * @const   string
     */
    const CHAIN_BY_AND = 'AND';
    
    /**
     * chain by "OR".
     *
     * @const   string
     */
    const CHAIN_BY_OR = 'OR';


    /**
     * add condition.
     *
     * 1. $cond->where->add('foo = ?', $foo);
     * 2. $cond->where->add('foo = ? AND bar = ?', [$foo, $bar]);
     * 3. $cond->where->add('foo = ? AND bar = ?', $foo, $bar);
     * 4. $cond->where->add('foo = :foo AND bar = :bar', [':foo' => $foo, ':bar' => $bar]);
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function andAdd()
    {
        $args = func_get_args();
        $value = array_shift($args);

        $params = [];
        while ($param = array_shift($args)) {
            if (is_array($param)) {
                $params = array_merge($params, $param);
            } else {
                $params[] = $param;
            }
        }

        $value = new WhereConditionValue($this, $value, $params);
        $this->add($value);

        return $this;
    }

    /**
     * add not condition.
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function andNot()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'andAdd'), $args);

        $value = array_pop($this->conditions);
        $value->not();
        $this->add($value);

        return $this;
    }

    /**
     * add or condition.
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function orAdd()
    {
        $args = func_get_args();
        $value = array_shift($args);
        
        $params = [];
        while ($param = array_shift($args)) {
            if (is_array($param)) {
                $params = array_merge($params, $param);
            } else {
                $params[] = $param;
            }
        }

        $value = new WhereConditionValue($this, $value, $params);
        $value->chain_by = WhereCondition::CHAIN_BY_OR;
        $this->add($value);

        return $this;
    }
    
    /**
     * or not condition.
     *
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function orNot()
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'orAdd'), $args);

        $value = array_pop($this->conditions);
        $value->not();
        $this->add($value);

        return $this;
    }
    
    
    /**
     * add in condition.
     *
     * @param   string  $key
     * @param   array   $values
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function andIn($key, array $values)
    {
        $value = sprintf('%s IN (%s)', $key, join(', ', array_fill(0, count($values), '?')));
        return $this->andAdd($value, $values);
    }

    /**
     * add not in condition.
     *
     * @param   string  $key
     * @param   array   $values
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function andNotIn($key, array $values)
    {
        $value = sprintf('%s NOT IN (%s)', $key, join(', ', array_fill(0, count($values), '?')));
        return $this->andAdd($value, $values);
    }
    
    /**
     * or in condition.
     *
     * @param   string  $key
     * @param   array   $values
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function orIn($key, array $values)
    {
        $value = sprintf('%s IN (%s)', $key, join(', ', array_fill(0, count($values), '?')));
        return $this->orAdd($value, $values);
    }
    
    /**
     * or not in condition.
     *
     * @param   string  $key
     * @param   array   $values
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function orNotIn($key, array $values)
    {
        $value = sprintf('%s NOT IN (%s)', $key, join(', ', array_fill(0, count($values), '?')));
        return $this->orAdd($value, $values);
    }
    
    
    /**
     * add between condition.
     *
     * @param   string  $key
     * @param   mixed   $min
     * @param   mixed   $max
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function andBetween($key, $min, $max)
    {
        $value = sprintf('%s BETWEEN ? AND ?', $key);
        return $this->andAdd($value, [$min, $max]);
    }

    /**
     * add not between condition.
     *
     * @param   string  $key
     * @param   mixed   $min
     * @param   mixed   $max
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function andNotBetween($key, $min, $max)
    {
        $value = sprintf('%s NOT BETWEEN ? AND ?', $key);
        return $this->andAdd($value, [$min, $max]);
    }
    
    /**
     * or between condition.
     *
     * @param   string  $key
     * @param   mixed   $min
     * @param   mixed   $max
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function orBetween($key, $min, $max)
    {
        $value = sprintf('%s BETWEEN ? AND ?', $key);
        return $this->orAdd($value, [$min, $max]);
    }
    
    /**
     * or not between condition.
     *
     * @param   string  $key
     * @param   mixed   $min
     * @param   mixed   $max
     * @return  Samurai\Onikiri\Criteria\WhereCondition
     */
    public function orNotBetween($key, $min, $max)
    {
        $value = sprintf('%s NOT BETWEEN ? AND ?', $key);
        return $this->orAdd($value, [$min, $max]);
    }


    /**
     * convert to SQL.
     *
     * @access  public
     * @return  string
     */
    public function toSQL()
    {
        $sql = [];

        $sql[] = 'WHERE';

        if (! $this->conditions) {
            $sql[] = '1';
        } else {
            foreach ($this->conditions as $index => $value) {
                if ( $index > 0 ) $sql[] = $value->chain_by;
                $sql[] = $value->toSQL();
            }
        }

        return join(' ', $sql);
    }
}

