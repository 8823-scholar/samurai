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

namespace Samurai\Samurai\Component\Console\Client;

use ReflectionObject;
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Onikiri\EntityTable;

/**
 * console base client
 *
 * @package     Samurai
 * @subpackage  Component.Console
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Client
{
    /**
     * @consts
     */
    const LOG_LEVEL_DEBUG = 1;
    const LOG_LEVEL_INFO = 2;
    const LOG_LEVEL_WARN = 3;
    const LOG_LEVEL_ERROR = 4;
    const LOG_LEVEL_FATAL = 5;

    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * construct
     */
    public function __construct()
    {
    }


    /**
     * logging to client (debug)
     *
     * @param   string  $message
     */
    public function log()
    {
        $args = func_get_args();
        $this->_log(self::LOG_LEVEL_DEBUG, $args);
    }
    
    /**
     * logging to client (info)
     *
     * @param   string  $message
     */
    public function info()
    {
        $args = func_get_args();
        $this->_log(self::LOG_LEVEL_INFO, $args);
    }
    
    /**
     * logging to client (warn)
     *
     * @param   string  $message
     */
    public function warn()
    {
        $args = func_get_args();
        $this->_log(self::LOG_LEVEL_WARN, $args);
    }
    
    /**
     * logging to client (error)
     *
     * @param   string  $message
     */
    public function error()
    {
        $args = func_get_args();
        $this->_log(self::LOG_LEVEL_ERROR, $args);
    }
    
    /**
     * logging to client (fatal)
     *
     * @param   string  $message
     */
    public function fatal()
    {
        $args = func_get_args();
        $this->_log(self::LOG_LEVEL_FATAL, $args);
    }


    /**
     * logging trigger
     *
     * @param   int     $level
     * @param   array   $args
     */
    protected function _log($level, $args)
    {
        foreach ($args as $message) {
            $this->send($level, $message);
        }
    }


    /**
     * level convert to string
     *
     * @param   int     $level
     * @return  string
     */
    public function levelToString($level)
    {
        $mapping = [
            self::LOG_LEVEL_DEBUG => 'debug',
            self::LOG_LEVEL_INFO => 'info',
            self::LOG_LEVEL_WARN => 'warn',
            self::LOG_LEVEL_ERROR => 'error',
            self::LOG_LEVEL_FATAL => 'fatal',
        ];
        return isset($mapping[$level]) ? $mapping[$level] : 'unknown';
    }


    /**
     * send message to display
     *
     * @param   int     $level
     * @param   string  $message
     */
    abstract public function send($level, $message);
    
    
    /**
     * var wrapping
     *
     * @param   mixed   $var
     * @return  mixed
     */
    public function wrapping($var, array $references = [])
    {
        switch (true) {
            case is_object($var):

                // is recruision ?
                if (in_array($var, $references, true)) return '** recursion **';
                $references[] = $var;

                $ref = new ReflectionObject($var);
                $values = [];
                foreach ($ref->getProperties() as $property) {

                    // simplize
                    $property->setAccessible(true);
                    $v = $property->getValue($var);
                    if (array_key_exists('Samurai\Raikiri\DependencyInjectable', $this->class_uses_deep($ref->getName()))
                        && $property->getName() === 'container') $v = '** raikiri **';
                    if ($var instanceof EntityTable && $property->getName() === 'onikiri') $v = '** onikiri **';

                    $values[] = sprintf('%s: %s', $property->getName(), $this->wrapping($v, $references));
                }
                $value = sprintf('%s {%s}', $ref->getName(), join(', ', $values));
                break;
            case is_array($var):
                $values = [];
                foreach ($var as $key => $value) {
                    $values[] = sprintf('%s: %s', $key, $this->wrapping($value, $references));
                }
                $value = sprintf('[%s]', join(', ', $values));
                break;
            default:
                $value = $var;
                break;
        }
        return $value;
    }


    /**
     * get use traits deep
     *
     * @param   string  $class
     * @return  array
     */
    public function class_uses_deep($class)
    {
        $traits = [];
        do {
            $traits = array_merge($traits, class_uses($class, false));
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait) {
            $traits = array_merge($traits, class_uses($trait, false));
        }
        return array_unique($traits);
    }
}

