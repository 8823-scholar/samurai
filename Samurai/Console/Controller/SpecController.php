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
     * 4. run spec.
     *
     * @access  public
     */
    public function execute()
    {
        if ($this->isUsage()) return [self::FORWARD_ACTION, 'spec.usage'];

        $this->setup();
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

        // search spec configuration file.
        $config_file_name = $this->runner->getConfigurationFileName();
        $dirs = explode(DS, getcwd());
        $find = false;
        do {
            $workspace = join(DS, $dirs);
            $config_file_path = $workspace . DS . $config_file_name;
            if (file_exists($config_file_path) && is_file($config_file_path)) {
                $find = true;
                break;
            }
        } while (array_pop($dirs));
        if (!$find) $workspace = getcwd();

        $this->runner->setWorkspace($workspace);
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

