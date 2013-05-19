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

use Samurai\Onikiri\ModelFactory;

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
     * models
     *
     * @access  protected
     * @var     array
     */
    protected $_models = array();

    /**
     * container.
     *
     * @access  protected
     * @var     Samurai\Raikiri\Container
     */
    protected $_container;


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
     * set container.
     *
     * @access  public
     * @param   Container   $container
     */
    public function setContainer(Container $container)
    {
        $this->_container = $container;
    }


    /**
     * get container.
     *
     * @access  public
     * @return  Container
     */
    public function getContainer()
    {
        return $this->_container;
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
     * add model.
     *
     * @access  public
     * @param   string  $name
     */
    public function addModel($name)
    {
        if ( ! $this->hasModel($name) ) {
            $this->_models[$name] = null;
        }
    }


    /**
     * has model ?
     *
     * @access  public
     * @param   string  $name
     * @return  boolean
     */
    public function hasModel($name)
    {
        return array_key_exists($name, $this->_models);
    }




    /**
     * magick method: get
     *
     * bridge to dependencies.
     *
     * @implements
     */
    public function __get($key)
    {
        // has dependency ?
        if ( $this->hasDep($key) ) {
            if ( $this->_deps[$key] === null ) {
                $this->_deps[$key] = $this->_container->getComponent($key);
            }
            return $this->_deps[$key];
        }

        // has model ?
        if ( $this->hasModel($key) && class_exists('Samurai\Onikiri\ModelFactory') ) {
            if ( $this->_models[$key] === null ) {
                $factory = ModelFactory::singleton($key);
                $this->_models[$key] = $factory->get($key);
            }
            return $this->_models[$key];
        }

        return null;
    }
}

