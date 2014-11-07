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
     */
    public function locatorAction()
    {
        $arg = $this->request->get('args');

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
        
        // show version.
        if ($this->request->get('option.v') || $this->request->get('version')) {
            return [self::FORWARD_ACTION, 'utility.version'];
        }

        // action execute.
        // exclude first argument, because this is command name.
        $args = $this->request->getAsArray('args');
        array_shift($args);
        $this->request->set('args', $args);
        return [self::FORWARD_ACTION, $this->completionActionArg($arg)];
    }



    /**
     * show version action.
     */
    public function versionAction()
    {
        $this->assign('version', Samurai::getVersion());
        $this->assign('state', Samurai::getState());
        return self::VIEW_TEMPLATE;
    }


    /**
     * show usage action.
     */
    public function usageAction()
    {
        $this->assign('version', Samurai::getVersion());
        $this->assign('state', Samurai::getState());
        $this->assign('script', './app');   // TODO: $this->request->getScript()
        return self::VIEW_TEMPLATE;
    }


    /**
     * start server action.
     */
    public function serverAction()
    {
        chdir($this->application->config('directory.document_root'));
        $host = $this->request->get('host', 'localhost');
        $port = $this->request->get('port', 8888);
        passthru(sprintf('php -S %s:%s index.php', $host, $port));
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

