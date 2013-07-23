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

use Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator;
use PhpSpec\Console\Application;
use Samurai\Samurai\Component\Core\YAML;

/**
 * spec runner for PHPSpec.
 *
 * @package     Samurai
 * @subpackage  Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class PHPSpecRunner extends Runner
{
    /**
     * @dependencies
     */
    public $Request;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // cd
        chdir($this->getWorkspace());

        $input = new PHPSpecRunnerInput([$this->Request->getScriptName(), 'run', $this->getWorkspace() . DS . 'spec', '--no-interaction']);

        $app = new Application(\Samurai\Samurai\Samurai::getVersion());
        $app->run($input);
    }


    /**
     * {@inheritdoc}
     */
    public function generateConfigurationFile()
    {
        $file = $this->getWorkspace() . DS . 'phpspec.yml';

        $suites = ['namespace' => '', 'spec_prefix' => 'spec', 'src_path' => 'src', 'spec_path' => '.'];
        $config = ['suites' => ['main' => $suites]];
        file_put_contents($file, YAML::dump($config));
    }


    /**
     * {@inheritdoc}
     */
    public function searchSpecFiles()
    {
        $specfiles = new SimpleListIterator();
        foreach ($this->targets as $target) {
            $finder = $this->Finder->create();
            $files = $finder->path($target)->fileOnly()->name("*Spec.php")->find();
            $specfiles->append($files);
        }

        return $specfiles;
    }


    /**
     * Ex. Foo\\Bar
     *
     * {@inheritdoc}
     */
    public function validateNameSpace($app_namespace, $src_class_name)
    {
        $class = substr($src_class_name, strlen($app_namespace) + 6);

        $namespaces = ['spec', $app_namespace];
        $namespaces = array_merge($namespaces, explode('\\', $class));
        array_pop($namespaces);
        return join('\\', $namespaces);
    }

    /**
     * Ex. ZooSpec
     *
     * {@inheritdoc}
     */
    public function validateClassName($app_namespace, $src_class_name)
    {
        $class = substr($src_class_name, strlen($app_namespace) + 6);

        $names = explode('\\', $class);
        return array_pop($names);
    }


    /**
     * Ex. FooBarZooSpec.php
     *
     * {@inheritdoc}
     */
    public function validateClassFile($namespace, $class_name)
    {
        return $this->getWorkspace() . DS . str_replace('\\', DS, $namespace) . DS . $class_name . '.php';
    }
}

