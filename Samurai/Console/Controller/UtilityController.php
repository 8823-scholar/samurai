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
 * @subpackage  Console
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Console\Controller;

use Samurai\Samurai\Samurai;

/**
 * Utility controller.
 *
 * @package     Samurai
 * @subpackage  Console.Controller
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class UtilityController extends ConsoleController
{
    /**
     * action locator action.
     *
     * @access  public
     */
    public function locator()
    {
        $arg = $this->Request->get('args');

        // show version.
        if ($this->Request->get('option.v') || $this->Request->get('version')) {
            return [self::FORWARD_ACTION, 'utility.version'];
        }

        // show usage.
        if ($arg === null) {
            return [self::FORWARD_ACTION, 'utility.usage'];
        }

        // start server.
        if ($arg === 's') {
            return [self::FORWARD_ACTION, 'utility.server'];
        }

        // task execute.
        if ($this->isTask($arg)) {
            return [self::FORWARD_ACTION, 'task.execute'];
        }

        // action execute.
        // exclude first argument, because this is command name.
        $args = $this->Request->getAsArray('args');
        array_shift($args);
        $this->Request->set('args', $args);
        return [self::FORWARD_ACTION, $this->completionActionArg($arg)];
    }



    /**
     * show version action.
     *
     * @access  public
     */
    public function version()
    {
        $this->assign('version', Samurai::getVersion());
        $this->assign('state', Samurai::getState());
        return self::VIEW_TEMPLATE;
    }


    /**
     * show usage action.
     *
     * @access  public
     */
    public function usage()
    {
        $this->assign('version', Samurai::getVersion());
        $this->assign('state', Samurai::getState());
        $this->assign('script', './app');   // TODO: $this->Request->getScript()
        return self::VIEW_TEMPLATE;
    }


    /**
     * start server action.
     */
    public function server()
    {
        chdir($this->application->config('directory.document_root'));
        passthru(sprintf('php -S localhost:%s index.php', $this->Request->get('port', 8888)));
    }



    /**
     * completion action arg string.
     *
     * @access  public
     * @param   string  $arg
     * @return  string
     */
    private function completionActionArg($arg)
    {
        return strpos($arg, '.') !== false ? $arg : "{$arg}.execute";
    }




    /**
     * arg is task ?
     * task format is "namespace:somedo"
     *
     * @access  public
     * @param   string  $arg
     * @return  boolean
     */
    public function isTask($arg)
    {
        return preg_match('/:/', $arg);
    }
}

