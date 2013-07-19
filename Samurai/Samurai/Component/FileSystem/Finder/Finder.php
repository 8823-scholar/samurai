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

namespace Samurai\Samurai\Component\FileSystem\Finder;

use Samurai\Samurai\Component\FileSystem;

/**
 * File finder.
 *
 * example 1) simple pattern.
 * 
 *   $files = $finder->find('/path/to/dir');
 *
 * example 2) recursive pattern.
 *
 *   $files = $finder->path('/path/to/dir')->recursive()->find();
 *
 * example 3) matching pattern.
 *
 *   $files = $finder->path('/path/to/dir')->match('/\\.jpg$/');
 *
 * example 4) sort pattern.
 *
 *   $files = $finder->path('/path/to/dir')->sortByFileSize()->find();
 *
 * @package     Samurai
 * @subpackage  Component.FileSystem.Finder
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Finder
{
    /**
     * find target paths.
     *
     * @access  public
     * @var     string  $path
     */
    public $paths = array();

    /**
     * find recursive ?
     *
     * @access  public
     * @var     boolean
     */
    public $recursive = true;

    /**
     * only files ?
     *
     * @access  public
     * @var     boolean
     */
    public $file_only = false;

    /**
     * only directories ?
     *
     * @access  public
     * @var     boolean
     */
    public $directory_only = false;

    /**
     * name patterns
     *
     * @access  public
     * @var     array
     */
    public $names = array();


    /**
     * create new instance.
     *
     * @access  public
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Finder
     */
    public function create()
    {
        $finder = new Finder();
        return $finder;
    }


    /**
     * find trigger.
     *
     * @access  public
     * @param   string|array    $paths
     */
    public function find($paths = null)
    {
        if ($paths === null) $paths = $this->paths;

        // iterator
        $iterator = $this->getIterator();

        // search in target paths
        foreach ((array) $paths as $path) {
            $this->searchInPath($iterator, $path);
        }

        return $iterator;
    }


    /**
     * search files in target path.
     *
     * @access  private
     * @param   Samurai\Samurai\Component\FileSystem\Iterator\IteratorAggregate $iterator
     * @param   string  $path
     * @param   Samurai\Samurai\Component\FileSystem\Directory  $parent
     * @return  array
     */
    private function searchInPath(FileSystem\Iterator\IteratorAggregate $iterator, $path, FileSystem\Directory $parent = null)
    {
        // when exists.
        if (file_exists($path)) {
            if(is_dir($path)) {
                $dir = new FileSystem\Directory($path);
                if ($parent) $dir->setParent($parent);
                if ($this->validate($dir)) $iterator->add($dir);
                if ($this->recursive) {
                    $this->searchInPath($iterator, "{$dir}/*", $dir);
                }
            } else {
                $file = new FileSystem\File($path);
                if ($parent) $file->setParent($parent);
                if ($this->validate($file)) $iterator->add($file);
            }
        }
        // when not exists, then glob.
        else {
            foreach (glob($path) as $file) {
                $this->searchInPath($iterator, $file, $parent);
            }
        }
    }


    /**
     * validate file.
     *
     * @access  private
     * @param   Samurai\Samurai\Component\FileSystem\File   $file
     * @return  boolean
     */
    private function validate(FileSystem\File $file)
    {
        $filters = $this->buildFilters();
        foreach ($filters as $filter) {
            if (! $filter->validate($file)) return false;
        }

        return true;
    }


    /**
     * build filters for validate.
     *
     * @access  private
     * @return  array
     */
    private function buildFilters()
    {
        $filters = array();

        // file only ? or direcotry only ?
        if ($this->file_only) $filters[] = new Filter\FileOnlyFilter();
        if ($this->directory_only) $filters[] = new Filter\DirectoryOnlyFilter();

        // name match ?
        foreach ($this->names as $name) {
            $filters[] = new Filter\NameFilter($name);
        }

        return $filters;
    }



    /**
     * set find target path.
     *
     * @access  public
     * @param   string  $path
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Finder
     */
    public function path($path)
    {
        $this->paths[] = $path;
        return $this;
    }


    /**
     * set recursive flag is true.
     *
     * @access  public
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Finder
     */
    public function recursive()
    {
        $this->recursive = true;
        return $this;
    }

    /**
     * set recursive flag is false.
     *
     * @access  public
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Finder
     */
    public function notRecursive()
    {
        $this->recursive = false;
        return $this;
    }


    /**
     * set file only flag is true
     *
     * @access  public
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Finder
     */
    public function fileOnly($flag = true)
    {
        $this->file_only = $flag;
        if ($this->file_only) $this->directory_only = false;
        return $this;
    }

    /**
     * set directory only is true.
     *
     * @access  public
     * @param   boolean $flag
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Finder
     */
    public function directoryOnly($flag = true)
    {
        $this->directory_only = $flag;
        if ($this->directory_only) $this->file_only = false;
        return $this;
    }


    /**
     * add rule of name.
     *
     * @access  public
     * @param   string  $pattern
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Finder
     */
    public function name($pattern)
    {
        $this->names[] = $pattern;
        return $this;
    }





    /**
     * get iterator.
     *
     * @access  private
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Iterator\Iterator
     */
    private function getIterator()
    {
        $iterator = new FileSystem\Iterator\SimpleListIterator();
        return $iterator;
    }
}

