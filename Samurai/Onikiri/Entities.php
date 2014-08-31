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

use Iterator;
use Samurai\Onikiri\Statement;
use Samurai\Onikiri\Connection;

/**
 * Entities class.
 *
 * @package     Onikiri
 * @subpackage  Entity
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Entities implements Iterator
{
    /**
     * entities cache.
     *
     * @var     array
     */
    private $_entities = array();

    /**
     * position.
     *
     * @var     int
     */
    private $_position = 0;


    /**
     * constructor.
     *
     */
    public function __construct()
    {
    }


    /**
     * add entity.
     *
     * @param   Samurai\Onikiri\Entity  $entity
     */
    public function add(Entity $entity)
    {
        $this->_entities[] = $entity;
    }


    /**
     * get size.
     *
     * @return  int
     */
    public function size()
    {
        return count($this->_entities);
    }


    /**
     * get cols
     *
     * @return array
     */
    public function col($column)
    {
        $values = [];
        foreach ($this->_entities as $entity) {
            if ($entity->hasAttribute($column)) $values[] = $entity->$column;
        }
        return $values;
    }


    /**
     * reverse sorted.
     *
     * @return  Samurai\Onikiri\Entities
     */
    public function reverse()
    {
        $entities = new Entities();

        foreach (array_reverse($this->_entities) as $entity) {
            $entities->add($entity);
        }

        return $entities;
    }


    /**
     * get by position.
     *
     * @param   int     $position
     * @return  Samurai\Onikiri\Entity
     */
    public function getByPosition($position)
    {
        return isset($this->_entities[$position]) ? $this->_entities[$position] : null;
    }


    /**
     * fetch.
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function fetch()
    {
        $entity = $this->current();
        if ($this->valid()) {
            $this->next();
        } else {
            $this->rewind();
        }
        return $entity;
    }

    /**
     * get first entity.
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function first()
    {
        return $this->getByPosition(0);
    }

    /**
     * get last entity
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function last()
    {
        return $this->getByPosition($this->size() - 1);
    }


    /**
     * filtering results
     *
     * @param   string|closure  $key
     * @param   mixed           $value
     * @return  Samurai\Onikiri\Entities
     */
    public function filter($key, $value = null)
    {
        if (! $key instanceof \Closure) {
            $closure = function($entity) use ($key, $value) {
                if ($entity->$key == $value) return $entity;
            };
        } else {
            $closure = $key;
        }
        
        $filtered = new Entities();
        foreach ($this->_entities as $entity) {
            if ($entity = $closure($entity)) {
                $filtered->add($entity);
            }
        }
        return $filtered;
    }


    /**
     * @implements
     */
    public function current()
    {
        return $this->getByPosition($this->_position);
    }

    /**
     * @implements
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * @implements
     */
    public function next()
    {
        $this->_position++;
    }

    /**
     * @implements
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * @implements
     */
    public function valid()
    {
        return isset($this->_entities[$this->_position]);
    }


    /**
     * bredge to each entity
     */
    public function __call($method, $args)
    {
        foreach ($this->_entities as $entity) {
            call_user_func_array([$entity, $method], $args);
        }
    }
}

