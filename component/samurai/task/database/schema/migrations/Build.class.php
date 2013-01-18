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
 * task: database: schema: migrations: build
 * 
 * @package     Samurai
 * @subpackage  Task.Database
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Task_Database_Schema_Migrations_Build extends Samurai_Task
{
    /**
     * schema.
     *
     * @access  private
     * @var     ActiveGateway_Schema
     */
    private $_schema;

    /**
     * migration files dir.
     *
     * @access  private
     * @var     string
     */
    private $_migrate_file_dir;


    /**
     * @dependencies
     */


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
     * set schema
     *
     * @access  public
     * @param   ActiveGateway_Schema    $schema
     */
    public function setSchema(ActiveGateway_Schema $schema)
    {
        $this->_schema = $schema;
    }




    /**
     * @implements
     */
    public function execute()
    {
        $schema = $this->_schema;
        $Manager = ActiveGateway::getManager();
        $AG = $Manager->getActiveGateway($schema->getAlias());

        // get migration file.
        $helper = $AG->getHelper();
        $files = $helper->getMigrationFiles($this->_migrate_file_dir, 0, $this->_schema->getVersion());
        foreach ( $files as $file ) {
            $AG->create(ActiveGateway_Schema::TABLE_SCHEMA_MIGRATIONS, array('version' => $file['version']));
        }
    }
    
    
    
    /**
     * @override
     */
    public function onStart()
    {
        parent::onStart();

        $version = $this->_schema->getVersion();
        $this->_migrate_file_dir = Samurai_Loader::getPath('db/migrate', true);
        $this->reporter->flushTaskMessage('-- database schema migrations build.', $this);
        $this->reporter->flushTaskMessage(sprintf('   upto version %d. [%s]', $version, $this->_migrate_file_dir), $this);
    }


    /**
     * @override
     */
    public function onFinish()
    {
        parent::onFinish();

        $this->reporter->flushTaskMessage(sprintf('   -> %0.5f sec.', $this->getPastSec()), $this);
    }
}

