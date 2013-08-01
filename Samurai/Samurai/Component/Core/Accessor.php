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

namespace Samurai\Samurai\Component\Core;

/**
 * Addtional setter / getter
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
trait Accessor
{
    /**
     * bredge to property.
     *
     * support syntaxes:
     *
     *  1. simple bredge:
     *      $some->getName();       // Some::$name
     *      $some->getFoo_bar();    // Some::$foo_bar
     *
     *  2. camelCase convert to underbar:
     *      $some->getFooBar();     // Some::$foo_bar
     *
     * @access  public
     */
    public function __call($method, array $args = [])
    {

        // getter
        if (strpos($method, 'get') === 0) {
            $name = substr($method, 3);
            if (strlen($name) > 0) {
                // 1. simple bredge
                $name1 = lcfirst($name);
                if ($this->hasProperty($name1)) {
                    return $this->$name1;
                }
                // 2. camelCase to underbar
                $name2 = join('_', array_map('lcfirst', preg_split('/(?=[A-Z])/', $name1)));
                if ($this->hasProperty($name2)) {
                    return $this->$name2;
                }
            }
        }

        // setter
        if (strpos($method, 'set') === 0) {
            $name = substr($method, 3);
            if (strlen($name) > 0) {
                $value = array_shift($args);

                // 1. simple bredge
                $name1 = lcfirst($name);
                if ($this->hasProperty($name1)) {
                    return $this->$name1 = $value;
                }
                // 2. camelCase to underbar
                $name2 = join('_', array_map('lcfirst', preg_split('/(?=[A-Z])/', $name1)));
                if ($this->hasProperty($name2)) {
                    return $this->$name2 = $value;
                }
            }
        }

        // has parent __call method ?
        foreach (class_parents(__CLASS__) as $parent) {
            if (in_array('__call', get_class_methods($parent))) {
                return parent::__call($method, $args);
            }
        }

        throw new \LogicException("No such method. -> {$method}");
    }


    /**
     * has property ?
     *
     * @access  public
     * @param   string  $name
     * @return  boolean
     */
    public function hasProperty($name)
    {
        static $vars = null;
        if (! $vars) {
            $vars = get_object_vars($this);
        }

        return array_key_exists($name, $vars);
    }
}

