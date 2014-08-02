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
 * column schema
 *
 * @package     Samurai.Onikiri
 * @subpackage  Schema
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ColumnSchema extends Schema
{
    /**
     * table schema
     *
     * @var     Samurai\Onikiri\Schema\TableSchema
     */
    public $table;

    /**
     * name
     *
     * @var     string
     */
    public $name;

    /**
     * default value
     *
     * @var     mixed
     */
    public $default_value = null;

    /**
     * nullable
     *
     * @var     boolean
     */
    public $nullable = true;


    /**
     * constructor
     *
     * @param   Samurai\Onikiri\Schema\TableSchema  $table
     * @param   string                              $name
     * @param   array                               $describe
     */
    public function __construct(TableSchema $table, $name, array $describe = [])
    {
        $this->name($name);
        $this->setTable($table);
    }


    /**
     * set table
     *
     * @param   Samurai\Onikiri\Schema\TableSchema  $table
     */
    public function setTable(TableSchema $table)
    {
        $this->table = $table;
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
     * get name.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set default value.
     *
     * @param   mixed   $value
     */
    public function defaultValue($value)
    {
        $this->default_value = $value;
        return $this;
    }

    /**
     * get default value.
     *
     * @return  mixed
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }
}

