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
 * diconファイルの読み込みを行うFilter
 *
 * 現在のアクション階層までのglobalなdiconファイルと現アクション専用のdiconファイルをオートに読み込む。
 * その他指定のdiconファイルも読み込み可能。
 *
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_DIContainer extends Samurai_Filter
{
    /**
     * 自動読み込み
     *
     * @access   private
     * @var      boolean
     */
    private $_autoload = true;

    /**
     * 読み込み対象diconファイル
     *
     * @access   private
     * @var      array
     */
    private $_dicon_files = array();

    /**
     * DIContainerコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Container;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        //初期化
        $this->_init();
        //オートロード
        if($this->_autoload){
            $this->_loadActionDicons();
        }
        //指定のdiconファイル
        $this->_loadDicons();
    }
    
    
    /**
     * diconファイルの読み込み
     *
     * @access     public
     * @param      string  $dicon_file
     */
    public function import($dicon_file)
    {
        $defines = Samurai_Yaml::load($dicon_file);
        foreach($defines as $alias => $define){
            $this->Container->registerComponent($alias, new Samurai_Container_Def($define));
        }
    }


    /**
     * Actionディレクトリ配下の、現在のActionまでのdiconファイルをロードする
     *
     * @access     private
     */
    private function _loadActionDicons()
    {
        $action = $this->ActionChain->getCurrentActionName();
        $path_list = explode('_', $action);
        $current_path = Samurai_Config::get('directory.action');
        foreach($path_list as $i => $path){
            //globalファイル
            $this->import(sprintf('%s%s%s', $current_path, DS, Samurai_Config::get('action.dicon_file')));
            //localファイル
            if($i == count($path_list) - 1){
                $this->import(sprintf('%s%s%s.dicon', $current_path, DS, $path));
            }
            $current_path = sprintf('%s%s%s', $current_path, DS, $path);
        }
    }


    /**
     * 指定のdiconファイルをロードする
     *
     * @access     private
     */
    private function _loadDicons()
    {
        foreach($this->_dicon_files as $dicon_file){
            $this->import($dicon_file);
        }
    }


    /**
     * 初期化
     *
     * @access     private
     */
    private function _init()
    {
        //オートロード
        $autoload = $this->getAttribute('autoload');
        if($autoload !== NULL && !$autoload){
            $this->_autoload = false;
        }
        //指定のdicon
        $this->_dicon_files = (array)$this->getAttribute('dicon');
        //コンテナ
        $this->Container = Samurai::getContainer();
    }
}

