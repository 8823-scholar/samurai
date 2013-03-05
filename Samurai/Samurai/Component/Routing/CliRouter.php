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

namespace Samurai\Samurai\Component\Routing;

use Samurai\Samurai\Component\Core\YAML;

/**
 * Routing class for cli.
 *
 * command option dispatch to action.
 *
 * @package     Samurai
 * @subpackage  Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class CliRouter extends Router
{
    /**
     * constructor.
     *
     * @access  public
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * import routing config.
     *
     * @access  public
     * @param   string  $file
     */
    public function import($file)
    {
        // ignore argument file.
        // cli routing only default "task_execute"
        $this->setRoot('task.execute');
        $this->_default = $this->_root;
    }



    /**
     * routing.
     *
     * @access  public
     * @return  Rule\Rule
     */
    public function routing()
    {
        // has dispatch.
        if ( $action = $this->getDispatchAction() ) {
            return new Rule\MatchRule(array('action' => $action));
        }

        // default rule.
        $path = '/';
        if ( $this->_default && $this->_default->match($path) ) {
            return $this->_default;
        }
    }


    /**
     * Get dispatched action
     *
     * enable target action name by command-line argument.
     *
     * @access  public
     * @return  string
     */
    public function getDispatchAction()
    {
        $args = $this->Request->getAsArray('args');
        $arg = array_shift($args);
        if ( preg_match('/^(\w+\.\w+)$/', $arg, $matches) ) {
            $action = $matches[1];
            return $action;
        }
    }
}

