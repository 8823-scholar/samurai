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
 * プロジェクトを生成する
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Add_Project extends Generator_Action
{
    /**
     * プロジェクト名
     *
     * @access   private
     * @var      string
     */
    private $project_name;

    /**
     * www/index.php
     *
     * @access   private
     * @var      string
     */
    private $www_index;

    /**
     * www/info.php
     *
     * @access   private
     * @var      string
     */
    private $www_info;

    /**
     * www/.htaccess
     *
     * @access   private
     * @var      string
     */
    private $www_htaccess;

    /**
     * www/samurai/samurai.css
     *
     * @access   private
     * @var      string
     */
    private $www_samurai_css;

    /**
     * close icon image
     *
     * @access   private
     * @var      string
     */
    private $www_samurai_image_close;

    /**
     * error icon image
     *
     * @access   private
     * @var      string
     */
    private $www_samurai_image_error;

    /**
     * info icon image
     *
     * @access   private
     * @var      string
     */
    private $www_samurai_image_info;

    /**
     * reload icon image
     *
     * @access   private
     * @var      string
     */
    private $www_samurai_image_reload;

    /**
     * toggle icon image
     *
     * @access   private
     * @var      string
     */
    private $www_samurai_image_toggle;

    /**
     * warning icon image
     *
     * @access   private
     * @var      string
     */
    private $www_samurai_image_warning;

    /**
     * 標準のレンダラー
     *
     * @access   public
     * @var      string
     */
    public $renderer = 'smarty';

    /**
     * テンプレートの拡張子
     *
     * @access   public
     * @var      string
     */
    public $renderer_suffix = 'tpl';

    /**
     * GeratorActionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $GeneratorAction;

    /**
     * GeneratorTemplateコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $GeneratorTemplate;

    /**
     * GeneratorComponentコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $GeneratorComponent;

    /**
     * GeneratorProjectコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $GeneratorProject;


    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        //Usage
        if($this->_isUsage() || !$this->args) return 'usage';
        //入力チェック
        if(!$this->_checkInput()) return 'usage';
        //ディレクトリー群作成
        if(!$this->_generateDirectories()){
            return 'aborted';
        //デフォルトファイル群作成
        } else {
            $this->_generateDefaultFiles();
        }
        //wwwファイルのコピー
        $this->_copyWww();
        //終了
        $this->_sendMessage('');
        $this->_sendMessage('Generated Project ...!');
    }


    /**
     * 入力チェック
     *
     * @access     private
     * @return     boolean
     */
    private function _checkInput()
    {
        //プロジェクト名のチェック
        $this->project_name = array_shift($this->args);
        if(!preg_match('/^[a-z][a-z0-9_\-]*?$/', $this->project_name)){
            $this->ErrorList->add('project_name', "{$this->project_name} -> Project's name is Invalid. ([a-z0-9_-])");
        }
        //レンダラー名のチェック
        if(!preg_match('/smarty|phptal|simple/', $this->renderer)){
            $this->ErrorList->add('renderer', "{$this->renderer} -> Renderer's name is Invalid. (smarty|phptal|simple)");
        } else {
            Samurai_Config::set('generator.renderer.name', $this->renderer);
        }
        return !$this->ErrorList->isExists();
    }



    /**
     * ディレクトリー群の作成
     *
     * @access     private
     */
    private function _generateDirectories()
    {
        //基本Samurai_Dirの作成
        $samurai_dir = Samurai_Config::get('generator.directory.samurai');
        if(!is_dir($samurai_dir)){
            if(!$this->_confirm("May I create the directory ? ({$samurai_dir})")) return false;
            if(!mkdir($samurai_dir, 0755)) Samurai_Logger::fatal('%s -> Failed creating.', $samurai_dir);
        }
        //サブディレクトリ群の作成
        $dirs = array(
            array($samurai_dir . DS . 'www', 0755),
            array($samurai_dir . DS . 'www/samurai', 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.bin'), 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.log'), 0777),
            array($samurai_dir . DS . Samurai_Config::get('directory.temp'), 0777),
            array($samurai_dir . DS . Samurai_Config::get('directory.config'), 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.config') . '/samurai', 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.config') . '/renderer', 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.config') . '/activegateway', 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.config') . '/routing', 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.component'), 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.action'), 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.template'), 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.skeleton'), 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.locale'), 0755),
            array($samurai_dir . DS . Samurai_Config::get('directory.spec'), 0755),
        );
        foreach($dirs as $dir_info){
            $dir  = $dir_info[0];
            $mode = $dir_info[1];
            if(is_dir($dir)){
                $this->_sendMessage("{$dir} -> Already exists. -> skip");
                continue;
            }
            if(mkdir($dir, $mode)){
                $this->_sendMessage("{$dir} -> Successfuly generated.");
            } else {
                Samurai_Logger::error("{$dir} -> Failed creating.");
            }
            if(!chmod($dir, $mode)){
                Samurai_Logger::error("{$dir} -> Faild changing mode.");
            }
        }
        return true;
    }


    /**
     * 基本ファイル群を作成する
     *
     * @access     private
     */
    private function _generateDefaultFiles()
    {
        $this->_sendMessage('Creating default files ...');
        $this->_generateActions();
        $this->_generateTemplates();
        $this->_generateConfigs();
        $this->_generateWwws();
        $this->_sendMessage('Finished default file.');
    }

    /**
     * アクションファイル群作成
     *
     * @access     private
     */
    private function _generateActions()
    {
        list($result, $action_file) = $this->GeneratorAction->generate('index', $this->GeneratorAction->getSkeleton());
        $this->GeneratorAction->generate4Yaml($action_file, $this->GeneratorAction->getSkeleton($this->GeneratorAction->SKELETON_YAML),
                                                array('action_names'=>array('*'), 'action'=>false,'global'=>true),
                                                $this->GeneratorAction->YAML_GLOBAL);
        $this->GeneratorAction->generate4Yaml($action_file, $this->GeneratorAction->getSkeleton($this->GeneratorAction->SKELETON_YAML),
                                                array('action_names'=>array('index'), 'action'=>true,'global'=>false, 'template'=>'index.tpl'),
                                                $this->GeneratorAction->YAML_ACTION);
        $this->GeneratorAction->generate4Yaml($action_file, $this->GeneratorAction->getSkeleton($this->GeneratorAction->SKELETON_DICON),
                                                array('action_names'=>array('*')), $this->GeneratorAction->DICON_GLOBAL);
    }

    /**
     * テンプレートファイル群作成
     *
     * @access     private
     */
    private function _generateTemplates()
    {
        $this->GeneratorTemplate->generate('index.tpl', $this->GeneratorTemplate->getSkeleton());
    }

    /**
     * 設定ファイル群作成
     *
     * @access     private
     */
    private function _generateConfigs()
    {
        //Samurai
        $this->GeneratorProject->generate4Config($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_SAMURAI_YAML),
                                                array(),
                                                $this->GeneratorProject->CONFIG_SAMURAI_YAML);
        $this->GeneratorProject->generate4Config($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_SAMURAI_DICON),
                                                array('renderer_name'=>$this->renderer),
                                                $this->GeneratorProject->CONFIG_SAMURAI_DICON);
        $this->GeneratorProject->generate4Config($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_SAMURAI_FRONTFILTER),
                                                array(),
                                                $this->GeneratorProject->CONFIG_SAMURAI_FRONTFILTER);
        //Renderer
        $this->GeneratorProject->generate4Config($this->project_name,
                                            $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_RENDERER_SMARTY),
                                            array(), $this->GeneratorProject->CONFIG_RENDERER_SMARTY);
        $this->GeneratorProject->generate4Config($this->project_name,
                                            $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_RENDERER_PHPTAL),
                                            array(), $this->GeneratorProject->CONFIG_RENDERER_PHPTAL);
        $this->GeneratorProject->generate4Config($this->project_name,
                                            $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_RENDERER_SIMPLE),
                                            array(), $this->GeneratorProject->CONFIG_RENDERER_SIMPLE);
        //ActiveGateway
        $this->GeneratorProject->generate4Config($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_AG),
                                                array(), $this->GeneratorProject->CONFIG_AG);
        //Routing
        $this->GeneratorProject->generate4Config($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_CONFIG_ROUTING),
                                                array(), $this->GeneratorProject->CONFIG_ROUTING);
        //Dot Samurai
        $this->GeneratorProject->generate4Dot($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_DOT_SAMURAI),
                                                array('renderer_name'=>$this->renderer,'renderer_suffix'=>$this->renderer_suffix),
                                                $this->GeneratorProject->DOT_SAMURAI);
    }

    /**
     * wwwファイル群作成
     *
     * @access     private
     */
    private function _generateWwws()
    {
        //Index
        list($result, $this->www_index) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_INDEX),
                                                array(), $this->GeneratorProject->WWW_INDEX);
        //Info
        list($result, $this->www_info) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_INFO),
                                                array(), $this->GeneratorProject->WWW_INFO);
        //Htaccess
        list($result, $this->www_htaccess) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_HTACCESS),
                                                array(), $this->GeneratorProject->WWW_HTACCESS);
        //Samurai
        list($result, $this->www_samurai_css) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_SAMURAI_CSS),
                                                array(), $this->GeneratorProject->WWW_SAMURAI_CSS);
        list($result, $this->www_samurai_image_close) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_SAMURAI_IMAGE_CLOSE),
                                                array(), $this->GeneratorProject->WWW_SAMURAI_IMAGE_CLOSE);
        list($result, $this->www_samurai_image_error) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_SAMURAI_IMAGE_ERROR),
                                                array(), $this->GeneratorProject->WWW_SAMURAI_IMAGE_ERROR);
        list($result, $this->www_samurai_image_info) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_SAMURAI_IMAGE_INFO),
                                                array(), $this->GeneratorProject->WWW_SAMURAI_IMAGE_INFO);
        list($result, $this->www_samurai_image_reload) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_SAMURAI_IMAGE_RELOAD),
                                                array(), $this->GeneratorProject->WWW_SAMURAI_IMAGE_RELOAD);
        list($result, $this->www_samurai_image_toggle) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_SAMURAI_IMAGE_TOGGLE),
                                                array(), $this->GeneratorProject->WWW_SAMURAI_IMAGE_TOGGLE);
        list($result, $this->www_samurai_image_warning) = $this->GeneratorProject->generate4Www($this->project_name,
                                                $this->GeneratorProject->getSkeleton($this->GeneratorProject->SKELETON_WWW_SAMURAI_IMAGE_WARNING),
                                                array(), $this->GeneratorProject->WWW_SAMURAI_IMAGE_WARNING);
    }
    
    
    /**
     * www以下に作成されたファイルを、コピーするドキュメントルートにコピーする
     *
     * @access     private
     */
    private function _copyWww()
    {
        $document_root = sprintf('%s/%s', $this->dir_home, Samurai_Config::get('generator.directory.www'));
        if(is_dir($document_root) && $this->_confirm('May I copy the files of www onto the document root?')){
            $this->_copy($this->www_index,    sprintf('%s/%s', $document_root, basename($this->www_index)));
            $this->_copy($this->www_info,     sprintf('%s/%s', $document_root, basename($this->www_info)));
            $this->_copy($this->www_htaccess, sprintf('%s/%s', $document_root, basename($this->www_htaccess)));
            @mkdir($document_root . DS . 'samurai', 0755);
            @chmod($document_root . DS . 'samurai', 0755);
            $this->_copy($this->www_samurai_css,           sprintf('%s/samurai/%s', $document_root, basename($this->www_samurai_css)));
            $this->_copy($this->www_samurai_image_close,   sprintf('%s/samurai/%s', $document_root, basename($this->www_samurai_image_close)));
            $this->_copy($this->www_samurai_image_error,   sprintf('%s/samurai/%s', $document_root, basename($this->www_samurai_image_error)));
            $this->_copy($this->www_samurai_image_info,    sprintf('%s/samurai/%s', $document_root, basename($this->www_samurai_image_info)));
            $this->_copy($this->www_samurai_image_reload,  sprintf('%s/samurai/%s', $document_root, basename($this->www_samurai_image_reload)));
            $this->_copy($this->www_samurai_image_toggle,  sprintf('%s/samurai/%s', $document_root, basename($this->www_samurai_image_toggle)));
            $this->_copy($this->www_samurai_image_warning, sprintf('%s/samurai/%s', $document_root, basename($this->www_samurai_image_warning)));
        }
    }
    
    
    /**
     * 単純にコピーを行う
     * 既に存在する場合は、上書きするか確認される
     *
     * @access     private
     */
    private function _copy($source, $dest)
    {
        if(file_exists($dest)){
            if(!$this->_confirm("Overwrite ? ({$dest})")){
                $this->_sendMessage("{$dest} -> Aborted overwrite. -> skip");
                return;
            }
        }
        if(copy($source, $dest)){
            $this->_sendMessage("{$dest} -> Succesfuly copied.");
        } else {
            Samurai_Logger::error('Failed file copy. -> %s -> %s', array($source, $dest));
        }
    }
}

