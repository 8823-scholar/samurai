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

use Samurai\Samurai\Samurai;
use Samurai\Samurai\Config;
use Samurai\Samurai\Application;
use Samurai\Samurai\Component\FileSystem;

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
     * app
     *
     * @access  public
     * @var     Samurai\Samurai\Application
     */
    public $app;


    /**
     * constructor
     *
     * @access  public
     * @param   Samurai\Samurai\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    /**
     * register to autoload
     *
     * @access  public
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }



    /**
     * autoload.
     *
     * @access  public
     * @param   string  $class
     */
    public function autoload($class)
    {
        // path.
        $path = $this->getPathByClass($class);

        // load
        if ($path) {
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
     * @param   boolean $with_app_namespace
     * @return  string
     */
    public function getPathByClass($class, $with_app_namespace = true)
    {
        $class_path = str_replace('\\', DS, $class);
        $class_path = str_replace('_', DS, $class_path) . '.php';

        foreach ($this->app->config('directory.apps') as $app) {
            $file = ($with_app_namespace ? $app['root'] : $app['dir']) . DS . $class_path;
            $file = new FileSystem\File($file);
            if ($file->isExists()) return $file;
        }
        return null;
    }



    /**
     * file finder over application dirs.
     *
     * @param   string  $glob
     * @param   boolean $not_exists
     * @return  Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator
     */
    public function find($glob, $not_exists = false)
    {
        $files = new FileSystem\Iterator\SimpleListIterator();

        // is absolute path
        if ($glob[0] === '/') {
            $matches = glob($glob);
            foreach ($matches as $path) {
                $file = new FileSystem\File($path);
                $files->add($file);
            }

            if (! $files->size() && $not_exists) {
                $files->add(new FileSystem\File($path));
            }

            return $files;
        }

        $first = null;
        foreach ($this->app->config('directory.apps') as $app) {
            if (! $first) $first = $app['dir'] . $glob;

            $matches = glob($app['dir'] . DS . $glob);
            foreach ($matches as $path) {
                $file = new FileSystem\File($path);
                $files->add($file);
            }
        }
        
        if (! $files->size() && $not_exists) {
            $files->add(new FileSystem\File($first));
        }

        return $files;
    }


    /**
     * find and get first element.
     *
     * @param   string  $blob
     * @param   boolean $not_exists
     * @return  Samurai\Samurai\Component\FileSystem\File
     */
    public function findFirst($glob, $not_exists = false)
    {
        $files = $this->find($glob, $not_exists);
        return $files->first();
    }

}

