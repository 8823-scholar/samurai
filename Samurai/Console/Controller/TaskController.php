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

/**
 * Task controller.
 *
 * @package     Samurai
 * @subpackage  Console.Controller
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class TaskController extends ConsoleController
{
    /**
     * execute action.
     */
    public function executeAction()
    {
        $request = $this->request->getAll();
        $task = $this->pickTaskName($request);
        $options = $this->pickTaskOptions($request);

        if ($this->isUsage()) {
            $task = $this->taskProcessor->get($task);
            $this->response->send($task->getUsage());
        } else {
            $this->task($task, $options);
        }
    }


    /**
     * pick task name from array.
     *
     * @access  private
     * @param   array   $options
     * @return  string
     */
    private function pickTaskName(array $options)
    {
        return isset($options['args']) ? array_shift($options['args']) : null;
    }
    
    
    /**
     * pick task options from array.
     *
     * @access  private
     * @param   array   $options
     * @return  array
     */
    private function pickTaskOptions(array $options)
    {
        // exclude task name.
        if (isset($options['args'])) {
            array_shift($options['args']);
        }

        // array to string.
        foreach ($options as $key => $value) {
            if (is_array($value) && $key !== 'args') {
                $options[$key] = array_pop($value);
            }
        }

        return $options;
    }
}

