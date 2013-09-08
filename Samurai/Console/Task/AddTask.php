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

namespace Samurai\Console\Task;

use Samurai\Samurai\Component\Task\Task;
use Samurai\Samurai\Component\Core\Skeleton;

/**
 * Add task.
 *
 * this task add class, spec, view, and others.
 *
 * @package     Samurai.Console
 * @subpackage  Task.Add
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class AddTask extends Task
{
    /**
     * @dependencies
     */
    public $FileUtil;
    public $Application;
    public $Loader;


    /**
     * add a spec.
     *
     * [usage]
     *   $ ./app add:spec Foo\Bar\Zoo
     *
     * @access  public
     */
    public function spec()
    {
        $current = $this->getCurrentAppDir();
        $spec_dir = $this->Loader->find($current . DS . $this->Application->config('directory.spec'))->first();

        foreach ($this->args as $arg) {
            $skeleton = $this->getSkeleton('Spec');
            $base_dir = clone $spec_dir;
            $class_name = basename($arg);
            $namespace = str_replace(DS, '\\', dirname($arg));

            $skeleton->assign('namespace', $spec_dir->getClassName() . '\\' . $namespace);
            $skeleton->assign('class', $class_name);
            $this->FileUtil->mkdirP($spec_dir->getRealPath() . DS . dirname($arg));
            $this->FileUtil->putContents($spec_dir->getRealPath() . DS . dirname($arg) . DS . $class_name . 'Spec.php', $skeleton->render());
        }
    }




    /**
     * get skeleton.
     *
     * @access  public
     * @param   string  $name
     * @return  Samurai\Samurai\Component\Core\Skeleton
     */
    public function getSkeleton($name)
    {
        $file = $this->Loader->find($this->Application->config('directory.skeleton') . DS . $name . 'Skeleton.php.twig')->first();
        $skeleton = new Skeleton($file);
        return $skeleton;
    }



    /**
     * get current dir in application.
     *
     * @access  public
     * @return  string
     */
    public function getCurrentAppDir()
    {
        // has targeted.
        if ($dir = $this->getOption('app-dir')) {
            return $dir[0] === '/' ? $dir : getcwd() . DS . $dir;
        }
        // or current dir.
        else {
            $current = getcwd();
            $default = null;
            foreach ($this->Application->config('directory.apps') as $app) {
                if (strpos($current, $app['dir']) === 0) return $app['dir'];
                if (! $default) $default = $app['dir'];
            }
            return $default;
        }
    }
}

