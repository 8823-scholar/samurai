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

use App\Application;
use Samurai\Samurai\Samurai;
use Samurai\Exception\Controller\NotFoundException;
use Samurai\Raikiri;

/**
 * Action chaining class.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ActionChain extends Raikiri\Object
{
    /**
     * stacked actions
     *
     * @access  private
     * @var     array
     */
    private $_actions = array();

    /**
     * position of action
     *
     * @access  private
     * @var     int
     */
    private $_position = 0;

    /**
     * @dependencies
     */
    public $Application;
    public $Container;


    /**
     * Add action
     *
     * @access  public
     * @param   string  $controller
     * @param   string  $action
     */
    public function addAction($controller, $action = null)
    {
        if ( $action === null ) {
            list($controller, $action) = explode('.', $controller);
        }
        $this->_actions[] = array(
            'controller' => null,
            'controller_name' => $controller,
            'action' => $action,
            'result' => null,
        );
    }


    /**
     * Get current action instance.
     *
     * @access  public
     * @return  array   controller:Controller, action:string
     */
    public function getCurrentAction()
    {
        if ( ! isset($this->_actions[$this->_position]) ) return null;

        $define = $this->_actions[$this->_position];
        if ( $define['controller'] ) return $define;

        $controller = $this->getController($define['controller_name']);
        $define['controller'] = $controller;

        $this->_actions[$this->_position] = $define;

        return $define;
    }


    /**
     * cteate and return controller.
     *
     * @access  public
     * @return  Controller
     */
    public function getController($name)
    {
        $base = join('\\', array_map('ucfirst', explode('_', $name))) . 'Controller';

        // search in app spaces.
        foreach ($this->Application->getAppSpaces() as $path => $space) {
            $class = $space . '\\Controller\\' . $base;
            $path = $this->Loader->getPath();
        }
        foreach ( $this->Application->getControllerSpaces() as $space ) {
            $controller = null;
            if ( class_exists($class) ) {
                $controller = new $class();
            }
            if ( $controller ) {
                $this->Container->injectDependency($controller);
                $controller->setName($name);
                return $controller;
            }
        }

        // not found.
        throw new NotFoundException();
    }


    /**
     * Set current action result.
     *
     * @access  public
     * @param   mixed   $result
     */
    public function setCurrentResult($result)
    {
        $this->_actions[$this->_position]['result'] = $result;
    }




    /**
     * step to next sequense.
     *
     * @access  public
     */
    public function next()
    {
        $this->_position++;
    }
}

