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

namespace Samurai\Samurai\Component\FileSystem;

/**
 * File info class.
 *
 * @package     Samurai
 * @subpackage  Component.FileSystem
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class File extends \SplFileInfo
{
    /**
     * path
     *
     * @access  public
     * @var     string
     */
    public $path;

    /**
     * parent
     *
     * @access  public
     * @var     Samurai\Samurai\Component\FileSystem\Directory
     */
    public $parent;

    /**
     * app dir.
     *
     * @access  public
     * @var     string
     */
    public $app_dir;

    /**
     * app namespace.
     *
     * @access  public
     * @var     string
     */
    public $app_namespace;


    /**
     * constructor
     *
     * @access  public
     * @param   string  $file_name
     */
    public function __construct($file_name)
    {
        parent::__construct($file_name);
        $this->path = $this->getRealPath();
    }


    /**
     * set parent node.
     *
     * @access  public
     * @param   Samurai\Samurai\Component\FileSystem\Directory  $parent
     */
    public function setParent(Directory $parent)
    {
        $this->parent = $parent;
    }



    /**
     * app dir accessor.
     *
     * @access  public
     * @param   string  $dir
     * @return  string
     */
    public function appDir($dir = null)
    {
        if ($dir !== null) $this->app_dir = $dir;
        return $this->app_dir;
    }
    
    
    /**
     * app namespace accessor.
     *
     * @access  public
     * @param   string  $namespace
     * @return  string
     */
    public function appNameSpace($namespace = null)
    {
        if ($namespace !== null) $this->app_namespace = $namespace;
        return $this->app_namespace;
    }


    /**
     * get root dir.
     *
     * root dir is app-dir exclude namespace.
     *
     * @access  public
     * @return  string
     */
    public function rootDir()
    {
        $dir = $this->appDir();
        $pattern_namespace = preg_quote(DS . str_replace('\\', DS, $this->appNameSpace()), '/');
        $dir = preg_replace("/{$pattern_namespace}/", '', $dir);
        return $dir;
    }



    /**
     * get ruled class name from path.
     * (not real class name.)
     *
     * @access  public
     * @return  string
     */
    public function getClassName()
    {
        $dir = substr($this->getPath(), strlen($this->rootDir()) + 1);
        $namespace = str_replace(DS, '\\', $dir);
        $class_name = $this->getBasename(".{$this->getExtension()}");
        return "${namespace}\\{$class_name}";
    }
}

