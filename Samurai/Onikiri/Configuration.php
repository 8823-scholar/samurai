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

namespace Samurai\Onikiri;

/**
 * Onikiri configurations.
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Configuration
{
    /**
     * model contained directory.
     *
     * @var     array
     */
    public $model_dirs = [];

    /**
     * naming strategy.
     *
     * @var     Samurai\Onikiri\Mapping\NamingStrategy
     */
    public $naming_strategy;


    /**
     * add model directory
     *
     * @param   string  $dir
     * @param   string  $namespace
     */
    public function addModelDir($dir, $namespace)
    {
        $this->model_dirs[$dir] = $namespace;
    }


    /**
     * get model directories
     *
     * @return  array
     */
    public function getModelDirs()
    {
        $dirs = [];
        foreach ($this->model_dirs as $dir => $namespace) {
            $dirs[] = ['dir' => $dir, 'namespace' => $namespace];
        }
        return $dirs;
    }


    /**
     * set naming strategy instance
     *
     * @param   Samurai\Onikiri\Mapping\NamingStrategy  $namingStrategy
     */
    public function setNamingStrategy(Mapping\DefaultNamingStrategy $namingStrategy)
    {
        $this->naming_strategy = $namingStrategy;
    }

    /**
     * get naming strategy
     *
     * @return  Samurai\Onikiri\Mapping\NamingStrategy
     */
    public function getNamingStrategy()
    {
        return $this->naming_strategy;
    }
}

