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
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Samurai\Exception\NotImplementsException;

/**
 * Base task.
 *
 * @package     Samurai
 * @subpackage  Component.Task
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Task
{
    /**
     * @traits
     */
    use Accessor;
    use DependencyInjectable;

    /**
     * args
     *
     * @var     array
     */
    public $args = [];

    /**
     * options
     *
     * @var     array
     */
    public $options = [];

    /**
     * do method.
     *
     * @var     string
     */
    public $do = null;

    /**
     * output bredge component.
     *
     * @access  public
     */
    public $output;
    
    
    /**
     * execute this task pre setted.
     *
     * @param   array   $options
     */
    public function execute(array $options = [])
    {
        if (! $this->do) throw new \Samurai\Samurai\Exception\LogicException('preset task something do.');

        $option = $this->getOption();
        $option->importFromArray($options);
        $option->validate();
        $this->{$this->do . 'Task'}($option);
    }


    /**
     * call other task
     *
     * @param   string  $name
     * @param   array   $options
     */
    public function task($name, array $options = [])
    {
        $this->taskProcessor->execute($name, $options);
    }


    /**
     * send message to client.
     *
     * @param   string  $message
     */
    public function sendMessage()
    {
        if (! $this->output) return;

        $args = func_get_args();
        call_user_func_array([$this->output, 'send'], $args);
    }


    /**
     * get option
     *
     * @return  Samurai\Samurai\Component\Task\Option
     */
    public function getOption($name = null)
    {
        $option = new Option();
        $reflection = $this->getReflection();
        if (! $name) $name = $this->do;
        $name = $name . 'Task';
        if (! $name || ! $reflection->hasMethod($name)) return $option;

        $method = $reflection->getMethod($name);
        $comment = $method->getDocComment();
        $parser = new OptionParser();
        $lines = [];
        foreach (preg_split('/\r\n|\n|\r/', $comment) as $line) {
            // /** or */ is skip.
            if (in_array(trim($line), ['/**', '*/', '**/'])) continue;

            $line = preg_replace('/^\s*?\*\s?/', '', $line);

            // options
            if($parser->isSupports($line)) {
                $option->addDefinition($parser->parse($line));
                continue;
            }

            // start char is "@" that is doc comment end signal.
            if (preg_match('/^@\w+/', $line)) continue;

            $lines[] = $line;
        }

        if ($options = $option->getDefinitions()) {
            $lines[] = $parser->formatter($option);
        }

        $option->setDescription(join(PHP_EOL, $lines));

        return $option;
    }


    /**
     * get reflection instance.
     *
     * @return  Reflection
     */
    public function getReflection()
    {
        $reflection = new \ReflectionClass(get_class($this));
        return $reflection;
    }


    /**
     * has task method ?
     *
     * @param   string  $do
     * @return  boolean
     */
    public function has($do)
    {
        return method_exists($this, $do . 'Task');
    }
}

