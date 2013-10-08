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
 * @package     Raikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Raikiri;

/**
 * Container factory class.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ContainerFactory
{
    /**
     * containers
     *
     * @access  private
     * @var     array
     */
    private static $containers = array();


    /**
     * Create container.
     *
     * @access  public
     * @return  Container
     */
    public static function create($name = null)
    {
        $name = $name ? $name : self::generateName();
        $container = new Container($name);
        $container->register('Container', $container);
        self::$containers[$name] = $container;
        return $container;
    }


    /**
     * Get container.
     *
     * @access  public
     * @param   string  $name
     * @return  Container
     */
    public static function get($name = null)
    {
        if ($name === null) {
            $name = array_shift(array_keys(self::$containers));
        }
        return self::$containers[$name] ? self::$containers[$name] : null;
    }


    /**
     * Generate container name.
     *
     * @access  public
     * @return  string
     */
    public static function generateName()
    {
        return uniqid();
    }
}

