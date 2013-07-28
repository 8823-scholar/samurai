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
 * namespace management class.
 *
 * this class is referenced by static.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Namespacer
{
    /**
     * path relationed to namespace.
     *
     * @access  public
     * @var     array
     */
    public static $namespaces = array();


    /**
     * private constructor.
     *
     * @access  private
     */
    private function __construct()
    {
    }


    /**
     * register namespace.
     *
     * @access  public
     * @param   string  $namespace
     * @param   string  $path
     */
    public static function register($namespace, $path)
    {
        self::$namespaces[$path] = $namespace;
    }
    
    
    /**
     * pick the app dir from some path.
     *
     * @access  public
     * @param   string  $path
     * @return  string
     */
    public static function pickAppDir($path)
    {
        // when relational path.
        if ($path[0] !== '/') return $path;

        $root = null;
        foreach (self::$namespaces as $p => $ns) {
            if (strpos($path, $p) === 0) {
                if ($root === null || strlen($root) < strlen($p)) $root = $p;
            }
        }

        return $root ? $root : $path;
    }


    /**
     * pick the root dir from some path.
     *
     * @access  public
     * @param   string  $path
     * @return  string
     */
    public static function pickRootDir($path)
    {
        // when relational path.
        if ($path[0] !== '/') return $path;

        $root = null;
        foreach (self::$namespaces as $p => $ns) {
            if (strpos($path, $p) === 0) {
                $f = function($names, $count) {
                    for ($i = 0; $i < $count; $i++) {
                        array_pop($names);
                    }
                    return $names;
                };
                $d = join(DS, $f(explode(DS, $p), count(explode('\\', $ns))));
                if ($root === null || strlen($root) < strlen($d)) $root = $d;
            }
        }

        return $root ? $root : $path;
    }
}

