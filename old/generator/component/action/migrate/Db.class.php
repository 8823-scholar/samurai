<?php
/**
 * PHP version 5.
 *
 * Copyright (c) 2007-2010, Samurai Framework Project, All rights reserved.
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
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    SVN: $Id$
 */

/**
 * DBのマイグレーションを実現するコマンド
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Migrate_Db extends Generator_Action
{
    /**
     * to
     *
     * @access   public
     * @var      int
     */
    public $to;

    /**
     * from
     *
     * @access   public
     * @var      int
     */
    public $from;

    /**
     * FileScannerコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $FileScanner;

    /**
     * 動作モード (up|down)
     *
     * @access   private
     * @var      string
     */
    private $_mode = 'up';


    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        //Usage
        if($this->_isUsage()) return 'usage';
        
        //up or down
        $this->_setFrom();
        if($this->to !== NULL && $this->to < $this->from){
            $this->_mode = 'down';
        }
        
        //ActiveGatewayのロード
        $this->_loadActiveGateway();
        
        //マイグレートファイルを収集する
        $migrates = $this->_collectMigrates();
        try {
            foreach($migrates as $migrate){
                $temp = $migrate->matches[2];
                $temp = join('', array_map('ucfirst', explode('_', $temp)));
                $class = 'Migrate_' . $temp;
                Samurai_Loader::load($migrate->path);
                $migrator = new $class();
                if(! $migrator instanceof Samurai_Migration){
                    throw new Samurai_Exception('Not Migrator');
                } else {
                    $this->_sendMessage('== ' . $temp . ': migrating ====================');
                    $migrator->dsn = $this->Request->get('dsn', 'base');
                    $migrator->start();
                    $migrator->setup();
                    $migrator->{$this->_mode}();
                    if($migrator->message){
                        $this->_sendMessage('-- ' . $migrator->message);
                        $this->_sendMessage('     -> ' . $migrator->getTime() . 's');
                    } else {
                        $this->_sendMessage('     -> ' . $migrator->getTime() . 's');
                    }
                    $this->_sendMessage('== ' . $temp . ': migrated (' . $migrator->getTime() . 's) ===========');
                }
            }
            $this->_sendMessage(count($migrates) > 0 ? 'finished all migration.' : 'not found migrations to do.');
        } catch(Exception $E){
            $this->_sendMessage('!!! failed to migrate.');
            $this->_sendMessage($E->getMessage());
        }
        
        //ログを残す
        if(count($migrates) > 0){
            $last_file = Samurai_Config::get('generator.directory.samurai') . '/log/migrate.db.last';
            file_put_contents($last_file, $migrate->matches[1]);
        }
    }


    /**
     * マイグレートファイルを収集する
     *
     * @access     private
     */
    private function _collectMigrates()
    {
        $directory = Samurai_Config::get('generator.directory.samurai') . '/migrate/db';
        $condition = $this->FileScanner->getCondition();
        $condition->setRegexp('/^([0-9]+)_([\w_]+)\.php$/');
        $_files = $this->FileScanner->scan($directory, $condition);
        $files  = array();
        foreach($_files as $_key => $file){
            $id = floatval($file->matches[1]);
            if($this->_mode == 'up'){
                if($this->to === NULL){
                    if($id >= $this->from){
                        $files[$id] = $file;
                    }
                } else {
                    if($id >= $this->from && $id <= $this->to){
                        $files[$id] = $file;
                    }
                }
            } else {
                if($id >= $this->to && $id <= $this->from){
                    $files[$id] = $file;
                }
            }
        }
        if($this->_mode == 'up'){
            ksort($files);
        } else {
            krsort($files);
        }
        return $files;
    }



    /**
     * fromをセットする
     *
     * @access     private
     */
    public function _setFrom()
    {
        if($this->from === NULL){
            $this->from = 0;
            $last_file = Samurai_Config::get('generator.directory.samurai') . '/log/migrate.db.last';
            if(Samurai_Loader::isReadable($last_file)){
                $this->from = floatval(file_get_contents($last_file)) + 1;
            }
        } else {
            $this->from = floatval($this->from);
        }
    }


    /**
     * ActiveGatewayをロード
     *
     * @access     private
     */
    private function _loadActiveGateway()
    {
        Samurai_Loader::load('library/ActiveGateway/ActiveGatewayManager.class.php');
        $Manager = ActiveGatewayManager::singleton();
        $appname = $this->Request->get('appname', Samurai_Config::get('generator.samurai.application_name'));
        $environment = $this->Request->get('env', 'production');
        $config_dir = Samurai_Config::get('generator.directory.samurai') . '/'
                        . Samurai_Config::get('generator.directory.config', Samurai_Config::get('directory.config'));
        $Manager->import(sprintf('%s/activegateway/activegateway.yml', $config_dir));
        $Manager->import(sprintf('%s/activegateway/activegateway.%s.yml', $config_dir, $appname));
        $Manager->import(sprintf('%s/activegateway/activegateway.%s.yml', $config_dir, $environment));
    }
}

