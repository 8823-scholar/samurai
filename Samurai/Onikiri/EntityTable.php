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

namespace Samurai\Onikiri;

/**
 * entity repository table.
 *
 * EntityTable is relational to database table.
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class EntityTable
{
    /**
     * table name
     *
     * @var     string
     */
    public $table_name;
    
    /**
     * entity class name.
     *
     * @var     string
     */
    public $entity_class;

    /**
     * primary key name.
     *
     * @var     string
     */
    public $primary_key = 'id';


    /**
     * get table name.
     *
     * User -> user
     * UserArticleTable -> user_article
     *
     * @return  string
     */
    public function getTableName()
    {
        if ($this->table_name) return $this->table_name;

        $tmp = explode('\\', get_class($this));
        $class = array_pop($tmp);
        $names = preg_split('/(?=[A-Z])/', $class);
        array_shift($names);
        array_pop($names);
        return strtolower(join('_', $names));
    }

    /**
     * set table name.
     *
     * @param   string  $name
     */
    public function setTableName($name)
    {
        $this->table_name = $name;
    }


    /**
     * get primary key.
     *
     * @return  string
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * set primary key.
     *
     * @param   string  $key
     */
    public function setPrimaryKey($key)
    {
        $this->primary_key = $key;
    }
    
    
    /**
     * get entity class.
     *
     * UserTable -> User
     * UserArticleTable -> UserArticle
     *
     * @return  string
     */
    public function getEntityClass()
    {
        if ($this->entity_class) return $this->entity_class;
        
        $class = get_class($this);
        $class = preg_replace('/Table$/', '', $class);
        return $class;
    }

    /**
     * set entity class.
     *
     * @param   string  $class
     */
    public function setEntityClass($class)
    {
        $this->entity_class = $class;
    }
}

