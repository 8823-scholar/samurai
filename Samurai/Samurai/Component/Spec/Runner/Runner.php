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
use Samurai\Samurai\Component\Core\Accessor;

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
     * workspace
     *
     * @access  public
     * @var     string
     */
    public $workspace = 'Temp/Spec';

    /**
     * target path.
     *
     * @access  public
     * @var     string
     */
    public $targets = [];

    /**
     * @dependencies
     */
    public $finder;

    /**
     * @traits
     */
    use Accessor;


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
     * run spec.
     *
     * @access  public
     */
    abstract public function run();


    /**
     * get configuration file.
     *
     * @access  public
     * @return  string
     */
    abstract public function getConfigurationFileName();
}

