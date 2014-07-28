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

namespace Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Core\Accessor;
use Samurai\Samurai\Exception\NotFoundException;

/**
 * task processor.
 *
 * @package     Samurai
 * @subpackage  Component.Task
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Processor
{
    /**
     * task name separator
     *
     * @const   string
     */
    const SEPARATOR = ':';
    
    /**
     * @traits
     */
    use Accessor;

    /**
     * outputter.
     *
     * @var     Object
     */
    public $output;

    /**
     * @dependencies
     */
    public $loader;
    public $Container;


    /**
     * get task.
     *
     * format:
     *   namespace:some:do
     *
     * @access  public
     * @param   string  $name
     * @return  Task
     */
    public function get($name)
    {
        $names = explode(self::SEPARATOR, $name);
        $method = array_pop($names);
        $class_name = 'Task\\' . join('\\', array_map('ucfirst', $names)) . 'Task';
        $class_path = $this->loader->getPathByClass($class_name, false);
        if ($class_path === null || ! $class_path->isExists()) throw new NotFoundException("No such task. -> {$name}");

        require_once $class_path;
        $class_name = $class_path->getClassName();
        $task = new $class_name();
        if (! $task->has($method)) throw new NotImplementsException("No such task. -> {$name}");
        $task->setDo($method);
        $task->setOutput($this->output);
        $this->Container->injectDependency($task);

        return $task;
    }


    /**
     * call task.
     *
     * @access  public
     * @param   mixed   $name
     */
    public function execute($name, array $options = [])
    {
        if (is_string($name)) {
            $names = explode(self::SEPARATOR, $name);
            $method = array_pop($names);
            $task = $this->get($name);
        }

        $task->array2Options($options);
        $task->$method();
    }
}

