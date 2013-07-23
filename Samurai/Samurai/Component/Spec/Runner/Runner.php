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

namespace Samurai\Samurai\Component\Spec\Runner;

use Samurai\Samurai\Component\FileSystem\File;

/**
 * base runner
 *
 * @package     Samurai
 * @subpackage  Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Runner
{
    /**
     * target path.
     *
     * @access  public
     * @var     string
     */
    public $targets = array();

    /**
     * workspace
     *
     * @access  public
     * @var     string
     */
    public $workspace = 'Temp/Spec';

    /**
     * @dependencies
     */
    public $Finder;


    /**
     * set target path.
     *
     * @access  public
     * @param   string  $path
     */
    public function addTarget($path)
    {
        $this->targets[] = $path;
    }


    /**
     * set workspace path.
     *
     * @access  public
     * @param   string  $dir
     */
    public function setWorkspace($dir)
    {
        $this->workspace = $dir;
    }

    /**
     * get workspace path.
     *
     * @access  public
     * @return  string
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }




    /**
     * run spec.
     *
     * @access  public
     */
    abstract public function run();


    /**
     * generate configuration file.
     *
     * @access  public
     */
    abstract public function generateConfigurationFile();


    /**
     * search spec files in targets
     *
     * @access  public
     * @param   array   $queries
     * @return  Samurai\Samurai\Component\FileSystem\Iterator\IteratorAggregate
     */
    abstract public function searchSpecFiles(array $queries = []);


    /**
     * validate namespace.
     *
     * @access  public
     * @param   string  $app_namespace
     * @param   string  $src_class_name
     * @return  string
     */
    abstract public function validateNameSpace($app_namespace, $src_class_name);


    /**
     * validate class name.
     *
     * @access  public
     * @param   string  $app_namespace
     * @param   string  $src_class_name
     * @return  string
     */
    abstract public function validateClassName($app_namespace, $src_class_name);


    /**
     * validate class file from class name.
     *
     * @access  public
     * @param   string  $namespace
     * @param   string  $class_name
     * @return  string
     */
    abstract public function validateClassFile($namespace, $class_name);


    /**
     * is match by queries.
     *
     * @access  public
     * @param   Samurai\Samurai\Component\FileSystem\File   $file
     * @param   array   $queries
     */
    public function isMatch(File $file, array $queries = [])
    {
        foreach ($queries as $query) {
            // when namespace
            if (! strpos($query, DS)) {
                $q_ns = join('\\', array_map('ucfirst', explode(':', $query)));
                return $file->appNamespace() === $q_ns;
            }
            // when file path
            elseif (is_dir($query) || is_file($query)) {
                // is absolute path
                if ($query[0] === '/') {
                    return strpos($file->getRealPath(), $query) === 0;
                }
                // is relational path.
                else {
                    return strpos($file->getRealPath(), getcwd() . DS . $query) === 0;
                }
            }
        }
    }
}

