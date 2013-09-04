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
use Samurai\Samurai\Component\Request\Request;
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
     * @dependencies
     */
    public $TaskProcessor;



    /**
     * call other task
     *
     * @access  public
     * @param   string  $name
     * @param   array   $options
     */
    public function task($name, array $options = [])
    {
        $this->TaskProcessor->execute($name, $options);
    }


    /**
     * array to options and args.
     *
     * @access  public
     * @param   array   $array
     */
    public function array2Options(array $array)
    {
        foreach ($array as $key => $value) {
            switch (true) {
                case $key === 'args':
                    $this->args = array_merge($this->args, $value);
                    break;
                case is_integer($key):
                    $this->args[] = $value;
                    break;
                default:
                    $this->options[$key] = $value;
                    break;
            }
        }
    }

    /**
     * Request component to options
     *
     * @access  public
     * @param   Samurai\Samurai\Component\Request\Request   $request
     * @return  Samurai\Samurai\Component\Task\Task
     */
    public function request2Options(Request $request)
    {
        $args = $request->getAll();
        $this->array2Options($args);
    }

    /**
     * get a option.
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public function getOption($key, $default = null)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }


    /**
     * has task method ?
     *
     * @access  public
     * @param   string  $do
     * @return  boolean
     */
    public function has($do)
    {
        return method_exists($this, $do);
    }
}

