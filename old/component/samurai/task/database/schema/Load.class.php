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
 * task: database: schema: load
 * 
 * @package     Samurai
 * @subpackage  Task.Database
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Task_Database_Schema_Load extends Samurai_Task
{
    /**
     * schemas
     *
     * @access  private
     * @var     array
     */
    private $_schemas = array();


    /**
     * @dependencies
     */
    public $FileScanner;


    /**
     * constructor.
     *
     * @access     public
     */
    public function __construct()
    {
        parent::__construct();
    }




    /**
     * prepare.
     *
     * @implements
     */
    public function prepare()
    {
        // get schema path
        $schema_files = $this->getSchemaFiles();
        if ( ! $schema_files->getSize() ) {
            $this->reporter->flushTaskMessage('Not found schema files.', $this);
            return false;
        }
        
        // load schema file.
        foreach ( $schema_files as $file ) {
            $this->_load($file->filename, $file->path);
        }
        $this->reporter->flushTaskMessage('finished preapred.', $this);
    }


    /**
     * load.
     *
     * @access  private
     * @param   string  $alias
     * @param   string  $schema_file
     */
    private function _load($alias, $schema_file)
    {
        $this->reporter->flushTaskMessage(sprintf('load schema [%s], and preparing...', $alias), $this);

        $class = 'Db_Schema_' . ucfirst($alias);
        require_once $schema_file;
        $schema = new $class($alias);
        $schema->define();
        $this->_schemas[] = $schema;

        // schema defines to task
        $before = $this;
        foreach ( $schema->getDefines() as $define ) {
            $task = $this->TaskManager->interrupt('database:schema:load:define', $this, $before);
            $task->setDefine($define);
            $before = $task;
        }
        
        // initialize schema_migrations
        $task = $this->TaskManager->interrupt('database:schema:migrations:initialize', $this, $before);
        $task->setSchema($schema);

        // build schema migration versions.
        $task = $this->TaskManager->interrupt('database:schema:migrations:build', $this, $task);
        $task->setSchema($schema);
    }




    /**
     * execute.
     *
     * @implements
     */
    public function execute()
    {
    }





    /**
     * get schema file path.
     *
     * @access  public
     * @return  string
     */
    public function getSchemaFiles()
    {
        $dir = Samurai_Loader::getPath('db/schema', true);
        $cond = $this->FileScanner->getCondition();
        $cond->setExtension('php');
        $files = $this->FileScanner->scan($dir, $cond);
        return $files;
    }
}

