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

namespace Samurai\Samurai\Component\FileSystem\Iterator;

use Samurai\Samurai\Component\FileSystem\File;

/**
 * simple file list iterator.
 *
 * @package     Samurai
 * @subpackage  Component.FileSystem
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SimpleListIterator implements IteratorAggregate
{
    /**
     * listing files.
     *
     * @access  private
     */
    private $list = [];


    /**
     * add file
     *
     * @access  public
     * @param   Samurai\Samurai\Component\FileSystem\File   $file
     */
    public function add(File $file)
    {
        $this->list[] = $file;
    }


    /**
     * append other iterator.
     *
     * @access  public
     * @param   Samurai\Samurai\Component\FileSystem\Iterator\IteratorAggregate
     */
    public function append(IteratorAggregate $iterator)
    {
        foreach ($iterator as $file) {
            $this->add($file);
        }
    }



    /**
     * get first element.
     *
     * @access  public
     * @return  Samurai\Samurai\Component\FileSystem\File
     */
    public function first()
    {
        return $this->list ? $this->list[0] : null;
    }


    /**
     * get last element.
     *
     * @access  public
     * @return  Samurai\Samurai\Component\FileSystem\File
     */
    public function last()
    {
        return $this->list ? $this->list[count($this->list) -1] : null;
    }


    /**
     * get count of files.
     *
     * @access  public
     * @return  int
     */
    public function size()
    {
        return count($this->list);
    }


    /**
     * get reversed elements.
     *
     * @access  public
     * @return  Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator
     */
    public function reverse()
    {
        $iterator = new self();
        foreach (array_reverse($this->list) as $file) {
            $iterator->add($file);
        }
        return $iterator;
    }


    /**
     * get iterator.
     *
     * @access  public
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->list);
    }


    /**
     * convert to array
     *
     * @access  public
     * @return  array
     */
    public function toArray()
    {
        return $this->list;
    }
}

