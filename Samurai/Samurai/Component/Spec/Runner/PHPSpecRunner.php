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
use Samurai\Samurai\Component\Core\YAML;
use Samurai\Samurai\Component\Spec\PHPSpec\Input;
use Samurai\Samurai\Component\Spec\PHPSpec\DIContainerMaintainer;
use PhpSpec\Console\Application;

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
    public $Application;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // cd
        chdir($this->getWorkspace());

        $input = new Input([$this->Request->getScriptName(), 'run',
                                            $this->getWorkspace() . DS . 'spec', '--verbose', '--ansi']);

        $app = new Application(\Samurai\Samurai\Samurai::getVersion());

        // override
        $container = $app->getContainer();
        $container->set('samurai.container', $this->Application->getContainer());

        $container->setShared('runner.specification', function($c) {
            return new PHPSpecSpecificationRunner(
                $c->get('event_dispatcher'),
                $c->get('runner.example')
            );
        });

        $container->set('runner.maintainers.dicontainer', function($c) {
            $maintainer = new DIContainerMaintainer(
                $c->get('formatter.presenter'),
                $c->get('unwrapper')
            );
            $maintainer->Container = $c->get('samurai.container');
            return $maintainer;
        });

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
    public function searchSpecFiles(array $queries = [])
    {
        $specfiles = new SimpleListIterator();
        foreach ($this->targets as $target) {
            $finder = $this->Finder->create();
            $files = $finder->path($target)->fileOnly()->name("*Spec.php")->find();
            foreach ($files as $file) {
                if ($this->isMatch($file, $queries)) {
                    $specfiles->add($file);
                }
            }
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

        $namespaces = ['spec'];
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

