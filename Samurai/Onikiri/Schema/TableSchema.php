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
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri\Schema;

/**
 * table schema
 *
 * @package     Samurai.Onikiri
 * @subpackage  Schema
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class TableSchema extends Schema
{
    /**
     * name
     *
     * @var     string
     */
    public $name;

    /**
     * columns
     *
     * @var     array
     */
    public $columns = [];


    /**
     * constructor
     *
     * @param   string  $name
     * @param   array   $describe
     */
    public function __construct($name, array $describe)
    {
        $this->name($name);
        foreach ($describe as $column => $desc) {
            $column = $this->column($desc['name']);
            if (array_key_exists('default', $desc)) $column->defaultValue($desc['default']);
        }
    }


    /**
     * set name
     *
     * @param   string  $name
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * add column
     *
     * @param   string  $name
     */
    public function column($name)
    {
        $column = new ColumnSchema($this, $name);
        $this->columns[$name] = $column;
        return $column;
    }


    /**
     * get column names
     *
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }
}

