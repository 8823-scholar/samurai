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
 * Base condition.
 *
 * @package     Onikiri
 * @subpackage  Condition
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class BaseCondition
{
    /**
     * parent.
     *
     * @access  public
     * @var     Samurai\Onikiri\Condition\Condition
     */
    public $parent;
    
    /**
     * conditions
     *
     * @access  public
     * @var     array
     */
    public $conditions = array();

    /**
     * params
     *
     * @access  public
     * @var     array
     */
    public $params = array();




    /**
     * constructor
     *
     * @access  public
     * @param   Samurai\Onikiri\Condition\Condition $parent
     */
    public function __construct(Condition $parent)
    {
        $this->parent = $parent;
    }


    /**
     * make SQL.
     *
     * @access  public
     * @return  string
     */
    abstract public function toSQL();
    
    
    
    /**
     * Set
     *
     * @access  public
     * @param   string  $table
     */
    public function set($table)
    {
        $this->conditions = array();
        $this->add($table);
        return $this;
    }


    /**
     * add.
     *
     * @access  public
     * @param   string  $table
     */
    public function add($table)
    {
        $this->conditions[] = $table;
        return $this;
    }


    /**
     * get params
     *
     * @access  public
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
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

