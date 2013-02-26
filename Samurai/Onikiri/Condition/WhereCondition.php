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
 * Where section class of condition.
 *
 * @package     Onikiri
 * @subpackage  Condition
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
     * 1. $cond->where->add(['foo' => $foo]);
     *    => WHERE foo = :foo
     * 2. $cond->where->add(['foo' => ['foo1', 'foo2']]);
     *    => WHERE foo IN (:foo1, :foo2)
     * 3. $cond->where->add('foo = ?', $foo);
     * 4. $cond->where->add('foo = ? AND bar = ?', $foo, $bar);
     * 5. $cond->where->add('foo = ? AND bar = ?', [$foo, $bar]);
     * 6. $cond->where->add('foo = :foo AND bar = :bar', ['foo' => $foo, 'bar' => $bar]);
     *
     * @access  public
     */
    public function add()
    {
        $args = func_get_args();
        $arg = array_shift($args);

        if ( is_array($arg) ) {
            $values = new WhereValues($this);
            foreach ( $arg as $key => $value ) {
                $values->add($key, $value);
            }
            $this->conditions[] = $values;

        } else {
            $value = new WhereRawValue($this, $arg, $args);
            $this->conditions[] = $value;
        }
        return $this;
    }

    /**
     * add or condition.
     *
     * @access  public
     */
    public function orAdd()
    {
        $args = func_get_args();
        $arg = array_shift($args);

        if ( is_array($arg) ) {
            $values = new WhereValues($this);
            $values->chain_by = WhereCondition::CHAIN_BY_OR;
            foreach ( $arg as $key => $value ) {
                $values->add($key, $value);
                $value = new WhereValue($this, $key, $value);
                $this->conditions[] = $value;
            }
            $this->conditions[] = $values;

        } else {
            $value = new WhereRawValue($this, $arg, $args);
            $value->chain_by = WhereCondition::CHAIN_BY_OR;
            $this->conditions[] = $value;
        }
        return $this;
    }


    /**
     * add like condition.
     *
     * @access  public
     */
    public function addLike($key, $value)
    {
        $value = new WhereLikeValue($this, $key, $value);
        $this->conditions[] = $value;
        return $this;
    }

    /**
     * add not like condition.
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     * @return  WhereCondition
     */
    public function notLike($key, $value)
    {
        $value = new WhereLikeValue($this, $key, $value);
        $value->not();
        $this->conditions[] = $value;
        return $this;
    }

    /**
     * add like condition chain by "OR".
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     * @return  WhereCondition
     */
    public function orLike($key, $value)
    {
        $value = new WhereLikeValue($this, $key, $value);
        $value->chain_by = WhereCondition::CHAIN_BY_OR;
        $this->conditions[] = $value;
        return $this;
    }

    /**
     * add not like condition chain by "OR".
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     * @return  WhereCondition
     */
    public function orNotLike($key, $value)
    {
        $value = new WhereLikeValue($this, $key, $value);
        $value->not();
        $value->chain_by = WhereCondition::CHAIN_BY_OR;
        $this->conditions[] = $value;
        return $this;
    }
    
    
    /**
     * add in condition.
     *
     * @access  public
     */
    public function addIn()
    {
        $args = func_get_args();
        $key = array_shift($args);
        if ( ! is_string($key) ) throw new \Exception('Invalid arguments.');

        $values = array();
        while ( $value = array_shift($args) ) {
            if ( is_array($value) ) {
                $values = array_merge($values, $value);
            } else {
                $values[] = $value;
            }
        }

        $value = new WhereValue($this, $key, $values);
        $this->conditions[] = $value;

        return $this;
    }

    /**
     * add not in condition.
     *
     * @access  public
     */
    public function notIn()
    {
        $args = func_get_args();
        $key = array_shift($args);
        if ( ! is_string($key) ) throw new \Exception('Invalid arguments.');

        $values = array();
        while ( $value = array_shift($args) ) {
            if ( is_array($value) ) {
                $values = array_merge($values, $value);
            } else {
                $values[] = $value;
            }
        }

        $value = new WhereValue($this, $key, $values);
        $value->not();
        $this->conditions[] = $value;

        return $this;
    }
    
    /**
     * add in condition chain by "OR".
     *
     * @access  public
     */
    public function orIn()
    {
        $args = func_get_args();
        $key = array_shift($args);
        if ( ! is_string($key) ) throw new \Exception('Invalid arguments.');

        $values = array();
        while ( $value = array_shift($args) ) {
            if ( is_array($value) ) {
                $values = array_merge($values, $value);
            } else {
                $values[] = $value;
            }
        }

        $value = new WhereValue($this, $key, $values);
        $value->chain_by = WhereCondition::CHAIN_BY_OR;
        $this->conditions[] = $value;

        return $this;
    }

    /**
     * add not in condition chain by "OR".
     *
     * @access  public
     */
    public function orNotIn()
    {
        $args = func_get_args();
        $key = array_shift($args);
        if ( ! is_string($key) ) throw new \Exception('Invalid arguments.');

        $values = array();
        while ( $value = array_shift($args) ) {
            if ( is_array($value) ) {
                $values = array_merge($values, $value);
            } else {
                $values[] = $value;
            }
        }

        $value = new WhereValue($this, $key, $values);
        $value->not();
        $value->chain_by = WhereCondition::CHAIN_BY_OR;
        $this->conditions[] = $value;

        return $this;
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

        $sql[] = 'WHERE';

        if ( ! $this->conditions ) {
            $sql[] = '1';
        } else {
            foreach ( $this->conditions as $index => $value ) {
                if ( $index > 0 ) $sql[] = $value->chain_by;
                $sql[] = $value->toSQL();
            }
        }

        return join(' ', $sql);
    }
}

