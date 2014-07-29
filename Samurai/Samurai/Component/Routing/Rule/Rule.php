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

namespace Samurai\Samurai\Component\Routing\Rule;

/**
 * Rule abstract class.
 *
 * @package     Samurai
 * @subpackage  Component.Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Rule
{
    /**
     * name
     *
     * @var     string
     */
    public $name;

    /**
     * controller
     *
     * @var     string
     */
    public $controller;

    /**
     * action
     *
     * @var     string
     */
    public $action;

    /**
     * path
     *
     * @var     string
     */
    public $path;

    /**
     * params
     *
     * @var     array
     */
    public $params = [];


    /**
     * Set name.
     *
     * @param   string  $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * Set controller.
     *
     * @param   string  $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }


    /**
     * Get Controller
     *
     * @return  string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action
     *
     * format with controller: controller.action
     *
     * @param   string  $action
     */
    public function setAction($action)
    {
        $names = explode('.', $action);
        if (count($names) > 1) {
            $controller = array_shift($names);
            $action = array_shift($names);
            $this->setController($controller);
        } else {
            $action = array_shift($names);
        }
        $this->action = $action;
    }


    /**
     * Get action
     *
     * @return  string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set path.
     *
     * @param   string  $path
     */
    public function setPath($path)
    {
        $parts = explode(DS, $path);
        $filename = array_pop($parts);

        // with format.
        if ( preg_match('/^(.+?)\.(.+)$/', $filename, $matches) ) {
            $filename = $matches[1];
            $format = $matches[2];
            $parts[] = $filename;
            $this->path = join(DS, $parts);
            $this->setParam('format', $format);

        // no format
        } else {
            $this->path = $path;
        }
    }


    /**
     * Set params
     *
     * @param   string  $key
     * @param   string  $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * get params
     *
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }


    /**
     * matching to path.
     *
     * @param   string  $path
     * @return  boolean
     */
    abstract public function match($path);
}

