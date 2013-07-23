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

namespace Samurai\Console\Controller;

use Samurai\Samurai\Component\FileSystem\File;

/**
 * spec controller.
 *
 * @package     Samurai.Console
 * @subpackage  Controller.Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SpecController extends ConsoleController
{
    /**
     * spec runner.
     *
     * @access  public
     * @var     Samurai\Samurai\Component\Spec\Runner
     */
    public $runner;

    /**
     * @dependencies
     */
    public $SpecHelper;
    public $Loader;
    public $Application;
    public $FileUtil;


    /**
     * execute spec files.
     *
     * spec step is...
     *
     * 1. setup
     * 2. copy to workspace.
     * 3. initialize sandbox for spec.
     * 4. run spec.
     *
     * @access  public
     */
    public function execute()
    {
        if ($this->isUsage()) return [self::FORWARD_ACTION, 'spec.usage'];

        $this->setup();
        $this->copy2Workspace();
        $this->initialize();
        $this->run();
    }
    
    
    /**
     * show usage action.
     *
     * @access  public
     */
    public function usage()
    {
        $this->assign('script', './app');   // TODO: $this->Request->getScript()
        return self::VIEW_TEMPLATE;
    }





    /**
     * set up for to run spec.
     *
     * @access  private
     */
    private function setup()
    {
        $this->runner = $this->SpecHelper->getRunner();

        // set target spec.
        foreach ($this->getTargetPaths() as $path) {
            $this->runner->addTarget($path);
        }

        // set workspace.
        $workspace = $this->Loader->find('Temp')->last() . DS . 'spec';
        $this->runner->setWorkspace($workspace);
    }


    /**
     * get target path.
     *
     * @access  private
     * @return  array
     */
    private function getTargetPaths()
    {
        $paths = [];
        foreach ($this->Loader->find($this->Application->config('directory.spec')) as $dir) {
            $paths[] = $dir->getRealPath();
        }
        return $paths;
    }


    /**
     * copy to workspace from target files.
     *
     * @access  private
     */
    private function copy2Workspace()
    {
        $this->truncateWorkspace();

        // search spec files.
        $queries = $this->Request->getAsArray('args', array('app', 'app:console'));
        $files = $this->runner->searchSpecFiles($queries);
        foreach ($files as $file) {
            $this->generateSpecFile($file);
        }
    }

    /**
     * truncate workspace directory.
     *
     * @access  private
     */
    private function truncateWorkspace()
    {
        $workspace = $this->runner->getWorkspace();

        // trush sweep
        if (is_dir($workspace)) {
            $this->FileUtil->rmdirR($workspace);
        }

        // make workspace
        $this->FileUtil->mkdirP($workspace);
    }

    /**
     * generate spec file.
     *
     * @access  private
     * @param   Samurai\Samurai\Component\FileSystem\File   $file
     */
    private function generateSpecFile(File $file)
    {
        require_once $file->getRealPath();
        $src_class_name = $file->getClassName();
        $dst_name_space = $this->runner->validateNameSpace($file->appNameSpace(), $src_class_name);
        $dst_class_name = $this->runner->validateClassName($file->appNameSpace(), $src_class_name);
        $dst_class_file = $this->runner->validateClassFile($dst_name_space, $dst_class_name);

        $workspace = $this->runner->getWorkspace();
        $body = [];
        $body[] = '<?php';
        $body[] = "namespace {$dst_name_space};";
        $body[] = "class {$dst_class_name} extends \\{$src_class_name} {}";
        $body[] = '';
        $this->FileUtil->mkdirP(dirname($dst_class_file));
        file_put_contents($dst_class_file, join(PHP_EOL, $body));
    }


    /**
     * initialize runner
     *
     * @access  private
     */
    private function initialize()
    {
        // generate runner configuration
        $this->runner->generateConfigurationFile();
    }


    /**
     * run spec.
     *
     * @access  private
     */
    private function run()
    {
        $this->runner->run();
    }
}

