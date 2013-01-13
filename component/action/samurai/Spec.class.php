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
 * @package    Samurai
 * @copyright  Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * SPEC実行コマンド
 *
 * BDD(Behavior Driven Development)開発のためのツール
 * generatorアプリの下に置きたいが、WEB上からのテスト実行もあり得るのでこちらに配置。
 * 
 * @package    Samurai
 * @subpackage Spec
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Samurai_Spec extends Samurai_Action
{
    /**
     * 設定情報
     *
     * @access   private
     * @var      array
     */
    private $_options = NULL;


    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        $this->_init();

        //SPECのための初期化スクリプトを実行する
        $this->_doInitialization();

        //実行
        PHPSpec_Runner::run($this->_options);
        if($this->Device->isCli()) echo "\n";
    }


    /**
     * 初期化
     *
     * @access     private
     */
    private function _init()
    {
        require_once 'PHPSpec.php';
        $this->_options = new stdClass();
        $this->_options->recursive = true;
        $this->_options->specdoc = true;
        $this->_options->reporter = $this->Device->isCli() ? 'console' : 'html' ;
        if(Samurai_Config::get('generator.directory.samurai')){
            Samurai::unshiftSamuraiDir(Samurai_Config::get('generator.directory.samurai'));
            if($this->Request->get('directory')){
                chdir(Samurai_Config::get('generator.directory.samurai') . DS . $this->Request->get('directory'));
            } else {
                chdir(Samurai_Config::get('generator.directory.samurai') . DS . Samurai_Config::get('directory.spec'));
            }
        } else {
            chdir(Samurai_Loader::getPath(Samurai_Config::get('directory.spec')));
        }
        //もしファイルの指定があれば
        if($args = $this->Request->get('args')){
            $this->_options->specFile = array_shift($args);
        }
    }


    /**
     * SPECがSPECのために初期化が必要な場合、
     * Initialization.phpをSPEC直下に配置することで初期化を行うことが出来る
     *
     * @access     private
     */
    private function _doInitialization()
    {
        if($init_file = $this->Request->get('initfile')){
            $init_file = Samurai_Config::get('generator.directory.samurai') . DS . $init_file;
        } else {
            $init_file = getcwd() . '/Initialization.php';
        }
        if(file_exists($init_file)){
            include_once($init_file);
        }
    }
}

