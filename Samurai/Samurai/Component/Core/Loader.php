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

use App\Application;
use Samurai\Samurai\Samurai;
use Samurai\Samurai\Config;

/**
 * Class loader.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Loader
{
    /**
     * autoload.
     *
     * @access  public
     * @param   string  $class
     */
    public static function autoload($class)
    {
        // path.
        $path = self::getPathByClass($class);

        // load
        if ( $path ) {
            require_once $path;
            return true;
        }
        return false;
    }

    
    /**
     * get path by class.
     *
     * @access  public
     * @param   string  $class
     * @return  string
     */
    public function getPathByClass($class)
    {
        $class_path = str_replace('\\', DS, $class);
        $class_path = str_replace('_', DS, $class_path) . '.php';

        foreach ( Application::getPaths() as $path ) {
            $file = $path . DS . $class_path;
            if ( file_exists($file) ) return $file;
        }
        return null;
    }



    /**
     * get all file paths if exists.
     *
     * @access  public
     * @param   string  $path
     * @return  array
     */
    public static function getPaths($path, $dirs = array(), $namespaces = array())
    {
        // is absolute path.
        if ( $path[0] === '/' ) return array($path);

        $paths = array();
        $dirs = $dirs ? $dirs : Application::getPaths();
        $namespaces = $namespaces ? $namespaces : array(null);
        foreach ( $dirs as $dir ) {
            foreach ( $namespaces as $namespace ) {
                $_dir = $namespace ? $dir . DS . str_replace('\\', DS, $namespace) : $dir;
                $file = $_dir . DS . $path;
                if ( file_exists($file) ) {
                    $paths[] = $file;
                }
            }
        }
        return $paths;
    }


    /**
     * Get path.
     *
     * @access  public
     * @param   string  $path
     * @return  string
     */
    public static function getPath($path, $dirs = array(), $namespaces = array())
    {
        // is absolute path.
        if ( $path[0] === '/' ) return $path;

        // search path.
        $dirs = $dirs ? $dirs : Application::getPaths();
        $namespaces = $namespaces ? $namespaces : array(null);
        foreach ( $dirs as $dir ) {
            foreach ( $namespaces as $namespace ) {
                $_dir = $namespace ? $dir . DS . str_replace('\\', DS, $namespace) : $dir;
                $file = $_dir . DS . $path;
                if ( file_exists($file) ) return $file;
            }
        }
        return null;
    }
}

