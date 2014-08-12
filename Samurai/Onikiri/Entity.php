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

namespace Samurai\Onikiri;

use Samurai\Raikiri\DependencyInjectable;

/**
 * Entity class.
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Entity
{
    /**
     * table
     *
     * @var     EntityTable
     */
    public $table;

    /**
     * attributes
     *
     * @var     array
     */
    public $attributes = array();

    /**
     * original attributes.
     *
     * @var     array
     */
    protected $o_attributes = array();

    /**
     * exists in backend ?
     *
     * @var     boolean
     */
    public $exists = false;

    /**
     * @traits
     */
    use DependencyInjectable {
        DependencyInjectable::__get as __di_get;
    }

    
    /**
     * constructor.
     *
     * @param   EntityTable $table
     * @param   array       $attributes
     * @param   boolean     $exists
     */
    public function __construct(EntityTable $table, $attributes = [], $exists = false)
    {
        $this->setTable($table);
        $this->attributes = (array)$attributes;
        $this->o_attributes = (array)$attributes;
        $this->exists = $exists;
    }


    /**
     * initialization
     */
    public function initialize()
    {
    }


    /**
     * set table
     *
     * @param   Samurai\Onikiri\EntityTable $table
     */
    public function setTable(EntityTable $table)
    {
        $this->table = $table;
    }

    /**
     * get table
     *
     * @param   string  $name
     * @return  Samurai\Onikiri\EntityTable
     */
    public function getTable($name = null)
    {
        if (! $name) {
            return $this->table;
        } else {
            return $this->table->onikiri()->getTable($name);
        }
    }

    /**
     * set transaction
     *
     * @param   Samurai\Onikiri\Transaction $tx
     */
    public function setTx(Transaction $tx)
    {
        $this->getTable()->setTx($tx);
    }

    /**
     * get transaction
     *
     * @return  Samurai\Onikiri\Transaction
     */
    public function getTx()
    {
        return $this->getTable()->getTx();
    }


    /**
     * save entity.
     *
     * @param   array   $attributes
     */
    public function save($attributes = array())
    {
        $this->table->save($this, $attributes);
    }


    /**
     * destroy entity.
     */
    public function destroy()
    {
        $this->table->destroy($this);
    }


    /**
     * get attributes.
     *
     * @return  array
     */
    public function getAttributes($updated = false)
    {
        if (! $updated) return $this->attributes;

        $attributes = array();
        foreach ($this->attributes as $key => $value) {
            if (! array_key_exists($key, $this->o_attributes) || $value !== $this->o_attributes[$key]) {
                $attributes[$key] = $value;
            }
        }
        return $attributes;
    }

    /**
     * has attribute
     *
     * @param   string  $key
     * @return  boolean
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }


    /**
     * Get primary value.
     *
     * @return  mixed
     */
    public function getPrimaryValue()
    {
        return $this->attributes[$this->table->getPrimaryKey()];
    }

    /**
     * Set primary value.
     *
     * @param   mixed   $value
     */
    public function setPrimaryValue($value)
    {
        $this->attributes[$this->table->getPrimaryKey()] = $value;
    }


    /**
     * convert to Array
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->attributes;
    }


    /**
     * is new record ?
     *
     * @return  boolean
     */
    public function isExists()
    {
        return $this->exists;
    }


    /**
     * magick method for get.
     *
     * @param   string  $key
     * @return  mixed
     */
    public function __get($key)
    {
        // has getter ?
        $names = explode('_', $key);
        $getter = 'get' . join('', array_map('ucfirst', $names));
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        // has attributes ?
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return $this->__di_get($key);
    }

    /**
     * magick method for setter.
     *
     * @access  public
     * @param   string  $key
     * @param   mixed   $value
     */
    public function __set($key, $value)
    {
        // has setter ?
        $names = explode('_', $key);
        $setter = 'set' . join('', array_map('ucfirst', $names));
        if (method_exists($this, $setter)) {
            return $this->$getter();
        }

        // has attributes ?
        $this->attributes[$key] = $value;
    }


    /**
     * magick method for getter.
     *
     * @access  public
     * @param   string  $method
     * @param   array   $args
     */
    public function __call($method, array $args)
    {
        // when getter.
        if (preg_match('/^get([A-Z]\w+)$/', $method, $matches)) {
            $names = preg_split('/(?=[A-Z])/', $matches[1]);
            array_shift($names);
            $key = strtolower(join('_', $names));
            return $this->$key;
            
        // when setter.
        } elseif (preg_match('/^set([A-Z]\w+)$/', $method, $matches)) {
            $names = preg_split('/(?=[A-Z])/', $matches[1]);
            array_shift($names);
            $key = strtolower(join('_', $names));
            return $this->$key = array_shift($args);

        // when attribute key
        } elseif (array_key_exists($method, $this->attributes)) {
            $method = 'get' . join('', array_map('ucfirst', explode('_', $method)));
            return $this->$method();
        }

        $class = get_class($this);
        throw new \LogicException("No such method. -> {$class}::{$method}");
    }
}

