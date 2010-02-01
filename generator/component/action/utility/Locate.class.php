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
 * Commandから指定アクションへのロケートをおこなうためのAction
 *
 * このアクションが実際の処理を行うことはない
 *
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Utility_Locate extends Generator_Action
{
    /**
     * Actionのロケート
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        //設定の取得
        $this->_loadSetting();
        //Action
        if($action = $this->_getLocateAction()) return array('success', 'locate_action' => $action);
        //Usage
        if($this->_isUsage()) return 'usage';
        //Version
        if($this->_isVersion()) return 'version';
        //Info
        if($this->Request->get('info')) return 'info';
        return 'usage';
    }


    /**
     * ロケート先のアクションを返却
     *
     * @access     public
     */
    private function _getLocateAction()
    {
        $action = array_shift($this->args);
        $this->Request->set('args', $this->args);
        if($action){
            $action = str_replace('-', '_', $action);
            if($action == 'spec') $action = 'samurai_spec';
            Samurai_Config::set('action.default', 'error_command');
        }
        return $action;
    }


    /**
     * 設定をロードする
     *
     * @access     protected
     */
    protected function _loadSetting()
    {
        //Homeディレクトリの設定
        Samurai_Config::set('directory.home', $this->dir_home);
        //SamuraiDirの設定
        if(!Samurai_Config::get('generator.directory.samurai')){
            Samurai_Config::set('generator.directory.samurai', $this->dir_samurai);
        }
        if($this->Request->get('samurai_dir')){
            $this->dir_samurai = $this->Request->get('samurai_dir');
            Samurai_Config::set('generator.directory.samurai', $this->dir_samurai);
        }
        //設定の読み込み
        $conf_file = $this->dir_samurai . DS . '.samurai';
        if(Samurai_Loader::isReadable($conf_file)){
            Samurai_Config::import($conf_file);
        }
    }
}

