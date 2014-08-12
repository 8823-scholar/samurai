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

namespace Samurai\Samurai\Component\DataSource;

use Samurai\Samurai\Exception\NotFoundException;

/**
 * YAML data source.
 *
 * @package     Samurai
 * @subpackage  Component.DataSource
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class YAML
{
    /**
     * load YAML file or string.
     *
     * @param   string  $source
     * @return  array
     */
    public function load($source)
    {
        if ($this->isFile($source)) {
            return $this->loadFile($source);
        } else {
            return $this->loadString($source);
        }
    }

    /**
     * load by file.
     *
     * @param   string  $file
     * @return  array
     */
    public function loadFile($file)
    {
        if ($this->enableSpyc()) {
            if (! file_exists($file)) return [];
            return $this->loadBySpyc($file);
        } else {
            throw new NotFoundException('Not found YAML parser.');
        }
    }

    /**
     * load by string
     *
     * @param   string  $string
     * @return  array
     */
    public function loadString($string)
    {
        if ($this->enableSpyc()) {
            return $this->loadBySpyc($string);
        } else {
            throw new NotFoundException('Not found YAML parser.');
        }
    }
    
    
    /**
     * dump YAML formatted.
     *
     * @param   array   $data
     * @return  string
     * @throw   Samurai\Exception\NotFoundException
     */
    public function dump($data)
    {
        if ($this->enableSpyc()) {
            return $this->dumpBySpyc($data);
        } else {
            throw new NotFoundException('Not found YAML parser.');
        }
    }


    /**
     * has Spyc YAML parser ?
     *
     * @return  boolean
     */
    public function enableSpyc()
    {
        return class_exists('Spyc');
    }



    /**
     * load YAML file by Spyc
     *
     * @param   string  $source
     * @return  array
     */
    public function loadBySpyc($source)
    {
        $data = \Spyc::YAMLLoad($source);
        return $data;
    }
    
    /**
     * dump YAML formatted by Spyc
     *
     * @param   array   $data
     * @return  string
     */
    public function dumpBySpyc($data)
    {
        $data = \Spyc::YAMLDump($data);
        return $data;
    }


    /**
     * is file ?
     *
     * @param   string  $source
     * @return  boolean
     */
    public function isFile($source)
    {
        return strpos($source, '---') === false && strpos($source, "\n") === false;
    }
}

