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

namespace Samurai\Samurai\Component\Request;

/**
 * Cli Request class.
 *
 * @package     Samurai
 * @subpackage  Component.Request
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class CliRequest extends Request
{
    /**
     * init.
     *
     * @access  public
     */
    public function init()
    {
        $this->set('args', array());
        if ( isset($_SERVER['argv']) ) {
            // first argument is script name.
            $args = $_SERVER['argv'];
            $script = array_shift($args);

            // parse options.
            foreach ( $args as $arg ) {
                // is long option
                // --foo
                //   => foo=true
                // --foo=bar
                //   => foo=bar
                if ( preg_match('/^--(..+)/', $arg, $matches) ) {
                    $option = explode('=', $matches[1]);
                    $key = array_shift($option);
                    $value = $option ? join('=', $option) : true;
                    $this->add($key, $value);
                }

                // is short option
                // -abc
                //   => option.a=true, option.b=true, option.c=true
                // -abc=foo
                //   => option.a=true, option.b=true, option.c=foo
                elseif ( preg_match('/^-(.+)/', $arg, $matches) ) {
                    $option = $matches[1];
                    for ( $i = 0; $i < strlen($option); $i++ ) {
                        $j = $i + 1;
                        $key = $option[$i];
                        if ( isset($option[$j]) && $option[$j] === '=' ) {
                            $value = substr($option, $j + 1);
                            $this->set('option.' . $key, $value);
                            break;
                        } else {
                            $value = true;
                            $this->set('option.' . $key, $value);
                        }
                    }
                }
                // is else to args.
                else {
                    $this->add('args', $arg);
                }
            }
        }
    }


    /**
     * add param.
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     */
    public function add($key, $value)
    {
        if ( ! isset($this->_params[$key]) ) {
            $this->_params[$key] = array();
        }
        $this->_params[$key][] = $value;
    }


    /**
     * Get param.
     *
     * when value is array, then return first value.
     * if you want to get as array, use "getAsArray", please.
     *
     * @override
     */
    public function get($key, $default = null)
    {
        $value = parent::get($key, $default);
        if ( is_array($value) ) {
            $value = array_shift($value);
        }
        return $value;
    }


    /**
     * Get param as array.
     *
     * @access  public
     * @param   string  $key
     * @param   array   $default
     * @return  array
     */
    public function getAsArray($key, array $default = array())
    {
        $value = parent::get($key, $default);
        return (array)$value;
    }



    /**
     * Get method
     *
     * @access  public
     * @return  string
     */
    public function getMethod()
    {
        return php_sapi_name();
    }
}

