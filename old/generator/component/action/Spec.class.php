<?php
/**
 * PHP version 5.
 *
 * Copyright (c) Samurai Framework Project, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     * Neither the name of the Samurai Framework Project nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * Execute specs for cli.
 *
 * For TDD(Test Driven Development) tool.
 * You can choice test runner "PHPSpec" or "PHPUnit" or others (default is "PHPSpec").
 *
 * @package     Samurai
 * @subpackage  Action.Spec
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Spec extends Generator_Action
{
    /**
     * runner
     *
     * @access  public
     * @var     Samurai_Spec_Runner_PHPSpec | Samurai_Spec_Runner_PHPUnit
     */
    public $runner;

    /**
     * workspace
     *
     * @access  private
     * @var     string
     */
    private $_workspace = 'temp/spec';

    /**
     * @dependencies
     */
    public $SpecHelper;
    public $Utility;


    /**
     * execute.
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        if ( $this->_isUsage() ) return 'usage';

        // init.
        $this->_setup();

        // search target specs and copy for runner.
        $this->_searchSpecsAndCopy();

        // spec init.
        $this->_initialization();

        // execute
        $this->runner->run();
    }


    /**
     * init.
     *
     * @access  private
     */
    private function _setup()
    {
        // set runner.
        $this->runner = $this->SpecHelper->getRunner($this->Request->get('runner', 'phpspec'));
        $this->runner->setTarget($this->Request->get('args.0', $this->_getSpecDir()));

        // target dir.
        $dir = sprintf('%s/%s', Samurai_Config::get('generator.directory.samurai'), $this->_workspace);
        $workspace = $dir;
        $this->runner->setWorkspace($workspace);
    }


    /**
     * get spec directory.
     *
     * @access  private
     * @return  string
     */
    private function _getSpecDir()
    {
        return Samurai_Config::get('generator.directory.samurai') . DS . Samurai_Config::get('directory.spec');
    }



    /**
     * search target spec files that copy for runner.
     *
     * @access  private
     */
    private function _searchSpecsAndCopy()
    {
        // clear.
        $this->_truncateWorkspace();

        $spec_files = $this->runner->searchSpecFiles();
        foreach ( $spec_files as $file ) {
            $this->_generateSpecFile($file->path);
        }
    }


    /**
     * clear files in workspace.
     * workspace is temp/spec/*
     *
     * @access  private
     */
    private function _truncateWorkspace()
    {
        $workspace = $this->runner->getWorkspace();
        if ( is_dir($workspace) ) {
            $this->Utility->rmdir($workspace);
        }
        $this->Utility->fillupDirectory($workspace, 0755);
    }


    /**
     * generate spec file for runner in workspace.
     *
     * @access  private
     * @param   string  $source
     */
    private function _generateSpecFile($source)
    {
        require_once $source;
        $src_class_name = $this->SpecHelper->getSourceClassName($source);
        $dst_class_name = $this->runner->validateClassName($src_class_name);
        $dst_class_file = $this->runner->validateClassFile($dst_class_name);

        // generate.
        $workspace = $this->runner->getWorkspace();
        $class_text = "<?php class {$dst_class_name} extends {$src_class_name} {}";
        file_put_contents($workspace . DS . $dst_class_file, $class_text);
    }



    /**
     * execute initialization.
     *
     * @access  private
     */
    private function _initialization()
    {
        $init_file = Samurai_Config::get('generator.directory.samurai')
                        . DS . Samurai_Config::get('generator.directory.spec') . DS . 'Initialization.php';
        if ( file_exists($init_file) ) {
            include_once($init_file);
        }
    }
}

