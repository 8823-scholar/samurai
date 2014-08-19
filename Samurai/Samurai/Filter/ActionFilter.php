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

namespace Samurai\Samurai\Filter;

use Samurai\Samurai\Controller\SamuraiController;

/**
 * Action filter.
 *
 * @package     Samurai
 * @subpackage  Filter
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ActionFilter extends Filter
{
    /**
     * controller
     *
     * @var     Samurai\Samurai\Controller\SamuraiController
     */
    public $controller;

    /**
     * {@inheritdoc}
     */
    public function prefilter()
    {
        parent::prefilter();

        $actionDef = $this->actionChain->getCurrentAction();
        $errorList = $this->actionChain->getCurrentErrorList();

        // TODO: When has error, execute
        $this->controller = $actionDef['controller'];
        $action = $actionDef['action'];
        
        $this->controller->prefilter();
        
        $result = $this->_callAction($this->controller, $action, $errorList->getType());
        $this->actionChain->setCurrentResult($result);
    }


    /**
     * call action
     *
     * @param   Samurai\Samurai\Controller\SamuraiController    $controller
     * @param   string  $action
     * @param   string  $error
     * @return  mixed
     */
    private function _callAction(SamuraiController $controller, $action, $error)
    {
        $method = $action . 'Action';
        if ($error) {
            $method = $action . ucfirst($error) . 'Action';
        }
        if (method_exists($controller, $method)) {
            return $controller->$method();
        } else {
            return $error;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function postfilter()
    {
        $this->controller->postfilter();
    }
}

