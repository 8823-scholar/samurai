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

namespace Samurai\Samurai\Component\Core;

use Samurai\Samurai\Component\Core\YAML;
use Samurai\Samurai\Controller\SamuraiController;
use Samurai\Samurai\Exception;
use Samurai\Raikiri;

/**
 * Filter chaining class.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class FilterChain extends Raikiri\Object
{
    /**
     * controller
     *
     * @access  private
     * @var     Samurai\Samurai\Controller\SamuraiController 
     */
    private $_controller;

    /**
     * action
     *
     * @access  private
     * @var     string
     */
    private $_action;

    /**
     * filters
     *
     * @access  private
     * @var     array
     */
    private $_filters = array();

    /**
     * filter names.
     *
     * @access  private
     * @var     array
     */
    private $_filter_names = array();

    /**
     * position of filter
     *
     * @access  private
     * @var     int
     */
    private $_position = 0;

    /**
     * has action filter ?
     *
     * @access  private
     * @var     boolean
     */
    private $_has_action_filter = false;

    /**
     * @dependencies
     */
    public $Request;
    public $ArrayUtil;
    public $Container;


    /**
     * Set action
     *
     * @access  public
     * @param   array
     */
    public function setAction(SamuraiController $controller, $action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
    }


    /**
     * clear all data.
     *
     * @access  public
     */
    public function clear()
    {
        $this->_controller = null;
        $this->_action = null;
        $this->_filters = array();
        $this->_filter_names = array();
        $this->_position = 0;
        $this->_has_action_filter = false;
    }


    /**
     * build filter chain.
     *
     * @access  public
     */
    public function build()
    {
        // load filter
        // 1. App/Controller/filter.yml
        // 2. App/Controller/foo.filter.yml
        $filters = $this->_controller->getFilters();
        for ( $i = 0; $i < count($filters); $i++ ) {
            $this->loadFilter($filters[$i], $i !== count($filters) - 1);
        }

        // action filter is already last.
        if ( ! $this->_has_action_filter ) {
            $this->addFilter('Action');
        } else {
            $action = null;
            $names = array();
            foreach ( $this->_filter_names as $name) {
                if ( strtolower($name) === 'action' ) {
                    $action = $name;
                } else {
                    $names[] = $name;
                }
            }
            $names[] = $action;
            $this->_filter_names = $names;
        }
    }


    /**
     * load filter.
     *
     * @access  public
     * @param   string  $file
     * @param   boolean $is_global
     */
    public function loadFilter($file, $is_global = false)
    {
        $defines = YAML::load($file);

        // when global, load "*"
        $filters = isset($defines['*']) && $defines['*'] ? $defines['*'] : array();

        // when local, load "*" and "controller.action"
        if ( ! $is_global ) {
            $key = $this->_controller->getFilterKey($this->_action);
            $filters = array_merge($filters, isset($defines[$key]) && $defines[$key] ? (array)$defines[$key] : array());
        }

        foreach ( $filters as $name => $attributes ) {
            $this->addFilter($name, $attributes);
        }
    }


    /**
     * Add filter.
     *
     * @access  public
     * @param   string  $name
     * @param   array   $attributes
     */
    public function addFilter($name, $attributes = array())
    {
        // name is "filtername:method:alias".
        $names = explode(':', $name);
        $filter = array_shift($names);
        $method = array_shift($names);
        $alias = array_shift($names);
        if ( ! $alias ) $alias = $filter;

        // method miss match, when return.
        if ( $method && strtoupper($method) !== $this->Request->getMethod() ) return;

        // register.
        if ( isset($this->_filters[$alias]) ) {
            $this->_filters[$alias]['attributes'] = $this->ArrayUtil->merge($this->_filters[$alias]['attributes'], $attributes);
        } else {
            $this->_filter_names[] = $alias;
            $this->_filters[$alias] = array('name' => $filter, 'attributes' => (array)$attributes);
        }

        // if action filter.
        if ( strtolower($filter) === 'action' ) {
            $this->_has_action_filter = true;
        }
    }



    /**
     * execute filter chain.
     *
     * @access  public
     */
    public function execute()
    {
        if ( $this->has() ) {
            $filter = $this->getCurrentFilter();
            $filter->execute();
        }
    }


    /**
     * step seaquese of filter.
     *
     * @access  public
     */
    public function next()
    {
        $this->_position++;
    }


    /**
     * Get current filter instance.
     *
     * @access  public
     * @return  Samurai\Samurai\Filter\Filter
     */
    public function getCurrentFilter()
    {
        $alias = $this->_filter_names[$this->_position];
        $define = $this->_filters[$alias];
        $filter = $this->getFilterByName($define['name']);
        return $filter;
    }


    /**
     * Get filter by name.
     *
     * @access  public
     * @param   string  $name
     * @return  Samurai\Samurai\Filter\Filter
     */
    public function getFilterByName($name)
    {
        // APP ?
        $filter = null;
        $name = ucfirst($name) . 'Filter';
        $class = '\\App\\Filter\\' . $name;
        if ( class_exists($class) ) {
            $filter = new $class();
        }

        // CORE ?
        $class = '\\Samurai\Samurai\\Filter\\' . $name;
        if ( ! $filter && class_exists($class) ) {
            $filter = new $class();
        }

        // not found.
        if ( ! $filter ) {
            throw new Exception\NotFoundException('No such filter. -> ' . $name);
        }
        
        $this->Container->injectDependency($filter);
        return $filter;
    }





    /**
     * has current filter ?
     *
     * @access  public
     * @return  boolean
     */
    public function has()
    {
        return isset($this->_filter_names[$this->_position]);
    }


    /**
     * has next filter ?
     *
     * @access  public
     * @return  boolean
     */
    public function hasNext()
    {
        return isset($this->_filter_names[$this->_position + 1]);
    }
}

