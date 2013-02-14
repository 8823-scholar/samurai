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
 * @package     Raikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Raikiri;

/**
 * DI Container common object class.
 *
 * @package     Raikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Object
{
    /**
     * dependencies
     *
     * @access  protected
     * @var     array
     */
    protected $_deps = array();


    /**
     * constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->defineDeps();
    }


    /**
     * definition dependencies
     *
     * @access  public
     */
    public function defineDeps()
    {
        // $this->addDep('Foo');
    }

    
    /**
     * add dependency
     *
     * @access  public
     * @param   string  $name
     */
    public function addDep($name)
    {
        if ( ! $this->hasDep($name) ) {
            $this->_deps[$name] = null;
        }
    }


    /**
     * has dependency ?
     *
     * @access  public
     * @param   string  $name
     * @return  boolean
     */
    public function hasDep($name)
    {
        return array_key_exists($name, $this->_deps);
    }




    /**
     * magick method: get
     *
     * bridge to dependencies.
     *
     * @implements
     */
    public function __get($name)
    {
        if ( $this->hasDep($name) ) {
            if ( $this->_deps[$name] === null ) {
                $container = ContainerFactory::get();
                $this->_deps[$name] = $container->getComponent($name);
            }
            return $this->_deps[$name];
        }
        return null;
    }
}

