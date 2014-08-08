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
 * Base condition.
 *
 * @package     Samurai.Onikiri
 * @subpackage  Criteria
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class BaseCondition
{
    /**
     * parent.
     *
     * @var     Samurai\Onikiri\Criteria\Condition
     */
    public $parent;
    
    /**
     * conditions
     *
     * @var     array
     */
    public $conditions = [];

    /**
     * params
     *
     * @var     array
     */
    public $params = [];


    /**
     * constructor
     *
     * @param   Samurai\Onikiri\Criteria\Criteria   $parent
     */
    public function __construct(Criteria $parent)
    {
        $this->parent = $parent;
    }


    /**
     * make SQL.
     *
     * @return  string
     */
    abstract public function toSQL();
    
    
    /**
     * Set
     *
     * @param   string  $condition
     */
    public function set($condition, array $params = [])
    {
        $this->conditions = [];
        $this->add($condition);
        return $this;
    }


    /**
     * add.
     *
     * @access  public
     * @param   string  $condition
     */
    public function add($condition, array $params = [])
    {
        $this->conditions[] = $condition;
        return $this;
    }


    /**
     * bind
     *
     * @param   array   $params
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            $this->addParam($value, $key);
        }
        return $this;
    }

    /**
     * add params
     *
     * @param   mixed   $value
     * @param   mixed   $key
     */
    public function addParam($value, $key = null)
    {
        if ($key === null || is_int($key)) {
            $this->params[] = $value;
        } else {
            $this->params[$key] = $value;
        }
    }

    /**
     * get params
     *
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }


    /**
     * get parent criteria
     *
     * @return  Samurai\Onikiri\Criteria\Criteria
     */
    public function getCriteria()
    {
        if ($this->parent instanceof Criteria) {
            return $this->parent;
        } else {
            return $this->parent->getCriteria();
        }
    }


    /**
     * has conditions ?
     *
     * @access  public
     * @return  boolean
     */
    public function has()
    {
        return count($this->conditions) > 0;
    }


    /**
     * magick method for bridge to parent.
     *
     * @access  public
     */
    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->parent, $method), $args);
    }
}

