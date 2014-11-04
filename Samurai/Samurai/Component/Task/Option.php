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

use Samurai\Samurai\Component\Request\Request;

/**
 * task options.
 *
 * @package     Samurai
 * @subpackage  Component.Task
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Option
{
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
     * construct
     *
     * @param   array   $options
     */
    public function __construct(array $options = [])
    {
        $this->importFromArray($options);
    }


    /**
     * import from array
     *
     * @param   array   $options
     */
    public function importFromArray(array $options)
    {
        foreach ($options as $key => $value) {
            switch (true) {
                case $key === 'args':
                    $this->args = $value;
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
     * import from request
     *
     * @param   Samurai\Samurai\Component\Request\Request   $request
     */
    public function importFromRequest(Request $request)
    {
        $args = $request->getAll();
        $this->importFromArray($args);
    }


    /**
     * get args
     *
     * @return  array
     */
    public function getArgs()
    {
        return $this->args;
    }
    
    
    /**
     * get a option.
     *
     * @param   string  $key
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }
}

