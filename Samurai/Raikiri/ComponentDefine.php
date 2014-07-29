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
 * Component definition.
 *
 * # most standard case.
 * Foo: App\Component\Foo
 *
 * # file path is irregular.
 * Bar:
 *     class: App\Component\Bar
 *     path: App/Component/Babababa.php
 *
 * # need constructor args.
 * Zoo:
 *     class: App\Component\Zoo
 *     args: [hoge, hoge]
 *
 * @package     Raikiri
 * @subpackage  Component
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ComponentDefine
{
    /**
     * class name
     *
     * @var     string
     */
    public $class;

    /**
     * file path
     *
     * @var     string
     */
    public $path;

    /**
     * constructor args
     *
     * @var     array
     */
    public $args = array();

    /**
     * init method name
     *
     * @var     string
     */
    public $init_method_name;

    /**
     * init method args
     *
     * @var     array
     */
    public $init_method_args = array();

    /**
     * instance type
     *
     * singleton: unique instance. (default)
     * prototype: generate for every get from the di container.
     *
     * @var     string
     */
    public $type = self::TYPE_SINGLETON;

    /**
     * instance
     *
     * @var     object
     */
    private $instance;

    /**
     * parent container.
     *
     * @var     Container
     */
    private $container;

    /**
     * const: types
     *
     * @const   string
     */
    const TYPE_SINGLETON = 'singleton';
    const TYPE_PROTOTYPE = 'prototype';
    const TYPE_CLOSURE = 'closure';


    /**
     * constructor
     *
     * @access  public
     * @param   array|Closure   $setting
     */
    public function __construct($setting)
    {
        if ($setting instanceof \Closure) {
            $this->class = $setting;
            $this->type = self::TYPE_CLOSURE;
            return;
        }

        foreach ($setting as $key => $value) {
            switch ($key) {
                case 'class':
                case 'path':
                case 'type':
                    $this->$key = (string)$value;
                    break;
                case 'args':
                    $this->$key = (array)$value;
                    break;
                case 'initMethod':
                    if (is_string($value)) {
                        $this->init_method_name = $value;
                        $this->init_method_args = array();
                    } else {
                        $this->init_method_name = $value['name'];
                        $this->init_method_args = isset($value['args']) ? (array)$value['args'] : array();
                    }
                    break;
            }
        }
    }


    /**
     * set container
     *
     * @param   Samurai\Raikiri\Container   $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * get container
     *
     * @return  Samurai\Raikiri\Container
     */
    public function getContainer()
    {
        return $this->container;
    }



    /**
     * get instance.
     *
     * @access  public
     * @return  object
     */
    public function getInstance()
    {
        if ($this->isSingleton() && $this->hasInstance()) {
            return $this->instance;
        }

        // require path
        if ($this->path) {
            require_once $this->path;
        }

        // initialize
        if ($this->isClosure()) {
            $closure = $this->class;
            $instance = $closure($this->container);
        } else {
            $class = $this->class;
            if ($class[0] !== '\\') $class = '\\' . $class;
            $script = sprintf('$instance = new %s(%s);', $class, $this->array2ArgsWithInjectDependency('$this->args', $this->args));
            eval($script);
        }

        if ($this->isSingleton()) {
            $this->instance = $instance;
        }
        if ($this->container && method_exists($instance, 'setContainer')) {
            $instance->setContainer($this->container);
        }
        
        // inject dependency
        if ($this->container) {
            $this->container->injectDependency($instance);
        }
        
        // init method
        if ($this->hasInitMethod()) {
            $this->callInitMethod($instance);
        }

        return $instance;
    }


    /** 
     * convert to arguments string.
     *
     * @access  private
     * @param   string  $parent
     * @param   array   $array
     * @return  string
     */
    private function array2ArgsWithInjectDependency($parent, array $array = array())
    {   
        $args = array();
        foreach ($array as $_key => $_val) {
            if (is_string($_val) && preg_match('/^\$([\w_]+)$/', $_val, $matches)) {
                $args[] = sprintf('$this->container->get("%s")', $matches[1]);
            } else {
                $args[] = is_numeric($_key) ? sprintf('%s[%s]', $parent, $_key) : sprintf("%s['%s']", $parent, $_key) ;
            }   
        }   
        return join(', ', $args);
    }



    /**
     * call init method
     *
     * @access  private
     * @param   object  $instance
     */
    private function callInitMethod($instance)
    {
        $args = [];
        foreach ($this->init_method_args as $arg) {
            if (is_string($arg) && preg_match('/^\$([\w_]+)$/', $arg, $matches)) {
                $arg = $this->container->get($matches[1]);
            }
            $args[] = $arg;
        }
        call_user_func_array(array($instance, $this->init_method_name), $args);
    }




    /**
     * has instance ?
     *
     * @return  boolean
     */
    public function hasInstance()
    {
        return $this->instance !== null;
    }


    /**
     * is singleton type ?
     *
     * @return  boolean
     */
    public function isSingleton()
    {
        return $this->type === self::TYPE_SINGLETON || $this->type === self::TYPE_CLOSURE;
    }

    /**
     * is prototype type ?
     *
     * @return  boolean
     */
    public function isPrototype()
    {
        return $this->type === self::TYPE_PROTOTYPE;
    }

    /**
     * is closuer ?
     *
     * @return  boolean
     */
    public function isClosure()
    {
        return $this->type === self::TYPE_CLOSURE;
    }


    /**
     * has init method ?
     *
     * @access  public
     * @return  boolean
     */
    public function hasInitMethod()
    {
        return $this->init_method_name !== null;
    }
}

