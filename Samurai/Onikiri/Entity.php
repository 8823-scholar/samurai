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

/**
 * Entity class.
 *
 * @package     Onikiri
 * @subpackage  Entity
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Entity
{
    /**
     * Model.
     *
     * @access  public
     * @var     Model
     */
    public $model;

    /**
     * attributes
     *
     * @access  public
     * @var     array
     */
    public $attributes = array();

    /**
     * exists in backend ?
     *
     * @access  public
     * @var     boolean
     */
    public $exists = false;

    
    /**
     * constructor.
     *
     * @access  public
     * @param   Model   $model
     * @param   array   $attributes
     * @param   boolean $exists
     */
    public function __construct(Model $model, array $attributes = array(), $exists = false)
    {
        $this->setModel($model);
        $this->attributes = $attributes;
        $this->exists = $exists;
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
     * magick method for get.
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public function __get($key)
    {
        // has attributes ?
        if ( array_key_exists($key, $this->attributes) ) {
            return $this->attributes[$key];
        }
        return null;
    }
}

