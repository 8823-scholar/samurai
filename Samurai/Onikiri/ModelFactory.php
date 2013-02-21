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
 * Model factory class.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ModelFactory
{
    /**
     * instance.
     *
     * @access  private
     * @var     ModelFactory
     */
    private static $_instance;

    /**
     * models
     *
     * @access  private
     * @var     array
     */
    private $_models = array();


    /**
     * constructor.
     *
     * @access  private
     */
    private function __construct()
    {
    }


    /**
     * get instance.
     *
     * @access  public
     * @return  ModelFactory
     */
    public static function singleton()
    {
        if ( self::$_instance === null ) {
            self::$_instance = new ModelFactory();
        }
        return self::$_instance;
    }



    /**
     * get model.
     *
     * @access  public
     * @param   string  $name
     * @return  Model
     */
    public function get($name)
    {
        if ( ! isset($this->_models[$name]) ) {
            $this->_models[$name] = $this->createModel($name);
        }
        return $this->_models[$name];
    }


    /**
     * create and return model instance.
     *
     * @access  public
     * @param   string  $name
     * @return  Model
     */
    public function createModel($name)
    {
        $names = preg_split('/(?=[A-Z])/', $name);
        array_shift($names);

        $class = '\\App\\Model\\' . join('\\', $names) . 'Model';
        $model = new $class();
        return $model;
    }
}

