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

use Samurai\Samurai\Exception\Exception;

/**
 * file system utility tools.
 *
 * @package     Samurai
 * @subpackage  Component.FileSystem
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Utility
{
    /**
     * @dependencies
     */
    public $finder;


    /**
     * mkdir (with parent)
     *
     * @access  public
     * @param   string  $dir
     */
    public function mkdirP($dir)
    {
        $p = [];
        $dirs = explode(DS, $dir);
        foreach ($dirs as $name) {
            $p[] = $name;
            $path = join(DS, $p);
            if ($path === '') {
                continue;
            } elseif (! file_exists($path)) {
                mkdir($path);
            } elseif(! is_dir($path)) {
                throw new Exception("Can not mkdir, this is not directory. -> {$path}");
            }
        }
    }


    /**
     * remove dir (with contain files)
     *
     * @access  public
     * @param   string  $dir
     */
    public function rmdirR($dir)
    {
        $finder = $this->finder->create();
        $files = $finder->notRecursive()->find($dir);
        foreach ($files as $file) {
            if (! $file->isExists()) continue;

            if ($file->isDir()) {
                $this->rmdirR("{$file}/*");
                if ($file->isWritable()) {
                    rmdir($file);
                } else {
                    throw new Exception("Can not delete this directory. -> {$file}");
                }
            } else {
                if ($file->isWritable()) {
                    unlink($file);
                } else {
                    throw new Exception("Can not delete this file. -> {$file}");
                }
            }
        }
    }


    /**
     * put contents
     *
     * @access  public
     * @param   string  $file
     * @param   string  $contents
     */
    public function putContents($file, $contents)
    {
        $dir = dirname($file);
        if (! file_exists($dir)) throw new Exception("Can not put contents, not exists dirctory. -> {$dir}");
        file_put_contents($file, $contents);
    }
}

