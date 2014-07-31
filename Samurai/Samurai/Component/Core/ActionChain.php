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
use Samurai\Samurai\Exception\NotFoundException;
use Samurai\Raikiri\DependencyInjectable;

/**
 * Action chaining class.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ActionChain
{
    /**
     * stacked actions
     *
     * @access  public
     * @var     array
     */
    public $actions = array();

    /**
     * position of action
     *
     * @access  public
     * @var     int
     */
    public $position = 0;

    /**
     * @traits
     */
    use DependencyInjectable;


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
        $this->actions[] = array(
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
        if (! isset($this->actions[$this->position])) return null;

        $define = $this->actions[$this->position];
        if ($define['controller']) return $define;

        $controller = $this->getController($define['controller_name']);
        $define['controller'] = $controller;

        $this->actions[$this->position] = $define;

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
        $names = explode('_', $name);
        array_unshift($names, 'Controller');
        $base = join(DS, array_map('ucfirst', $names)) . 'Controller.php';

        $file = $this->loader->findFirst($base);
        if ($file) {
            $class = $file->getClassName();
            $controller = new $class();
            $controller->setContainer($this->Container);
            $this->Container->injectDependency($controller);
            $controller->setName($name);
            return $controller;
        }

        // not found.
        throw new NotFoundException();
    }


    /**
     * has controller ?
     *
     * @param   string  $name
     * @param   string  $action
     * @return  boolean
     */
    public function existsController($name, $action = null)
    {
        $names = explode('_', $name);
        array_unshift($names, 'Controller');
        $base = join(DS, array_map('ucfirst', $names)) . 'Controller.php';

        $file = $this->loader->findFirst($base);
        if ($file) {
            $class = $file->getClassName();
            if (! $action && class_exists($class)) return true;
            if (method_exists($class, $action . 'Action')) return true;
        }

        return false;
    }


    /**
     * Set current action result.
     *
     * @access  public
     * @param   mixed   $result
     */
    public function setCurrentResult($result)
    {
        $this->actions[$this->position]['result'] = $result;
    }




    /**
     * step to next sequense.
     *
     * @access  public
     */
    public function next()
    {
        $this->position++;
    }
}

