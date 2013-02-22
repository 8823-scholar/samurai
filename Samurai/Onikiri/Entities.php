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
     * Model.
     *
     * @access  public
     * @var     Model
     */
    public $model;

    /**
     * Statement.
     *
     * @access  public
     * @var     Statement
     */
    public $statement;

    /**
     * entities cache.
     *
     * @access  private
     * @var     array
     */
    private $_entities = array();

    /**
     * position.
     *
     * @access  private
     * @var     int
     */
    private $_position = 0;


    /**
     * constructor.
     *
     * @access  public
     * @param   Model       $model
     * @param   Statement   $statement
     */
    public function __construct(Model $model, Statement $statement)
    {
        $this->setModel($model);
        $this->setStatement($statement);
    }


    /**
     * Set model.
     *
     * @access  public
     * @param   Model   $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }


    /**
     * Set statement.
     *
     * @access  public
     * @param   Statement   $statement
     */
    public function setStatement(Statement $statement)
    {
        $this->statement = $statement;
    }



    /**
     * get by position.
     *
     * @access  public
     * @param   int     $position
     * @return  Entity
     */
    public function getByPosition($position)
    {
        // already has entity.
        if ( isset($this->_entities[$position]) ) return $this->_entities[$position];
        
        $row = $this->statement->fetch(Connection::FETCH_ASSOC, Connection::FETCH_ORI_ABS, $position);
        if ( ! $row ) return null;

        $entity = $this->model->build($row, true);
        $this->_entities[$position] = $entity;
        return $entity;
    }


    /**
     * get first entity.
     *
     * @access  public
     * @return  Entity
     */
    public function first()
    {
        return $this->getByPosition(0);
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
        $entity = $this->getByPosition($this->_position);
        return $entity ? true : false;
    }
}

