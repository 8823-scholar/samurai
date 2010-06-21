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
 * ProjectGenerator
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Generator_Generator_Project extends Generator
{
    /**
     * skel : .samurai
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_DOT_SAMURAI = 'dot.samurai.skeleton.php';

    /**
     * skel : www/index.php
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_INDEX = 'www/index.skeleton.php';

    /**
     * skel : www/info.php
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_INFO = 'www/info.skeleton.php';

    /**
     * skel : www/.htaccess
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_HTACCESS = 'www/htaccess.skeleton.php';

    /**
     * skel : www/samurai/samurai.css
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_SAMURAI_CSS = 'www/css.skeleton.php';

    /**
     * skel : www/samurai/close.gif
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_SAMURAI_IMAGE_CLOSE = 'close.gif';

    /**
     * skel : www/samurai/error.gif
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_SAMURAI_IMAGE_ERROR = 'error.gif';

    /**
     * skel : www/samurai/info.gif
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_SAMURAI_IMAGE_INFO = 'info.gif';

    /**
     * skel : www/samurai/reload.gif
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_SAMURAI_IMAGE_RELOAD = 'reload.gif';

    /**
     * skel : www/samurai/toggle.gif
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_SAMURAI_IMAGE_TOGGLE = 'toggle.gif';

    /**
     * skel : www/samurai/warning.gif
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WWW_SAMURAI_IMAGE_WARNING = 'warning.gif';

    /**
     * skel : config/samurai/samurai.yml
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_SAMURAI_YAML = 'config/samurai.yaml.skeleton.php';

    /**
     * skel : config/samurai/samurai.dicon
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_SAMURAI_DICON = 'config/samurai.dicon.skeleton.php';

    /**
     * skel : config/samurai/frontfilter.yml
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_SAMURAI_FRONTFILTER = 'config/samurai.frontfilter.skeleton.php';

    /**
     * skel : config/renderer/smarty.php
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_RENDERER_SMARTY = 'config/renderer.smarty.skeleton.php';

    /**
     * skel : config/renderer/phptal.php
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_RENDERER_PHPTAL = 'config/renderer.phptal.skeleton.php';

    /**
     * skel : config/renderer/simple.php
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_RENDERER_SIMPLE = 'config/renderer.simple.skeleton.php';

    /**
     * skel : config/activegateway/activegateway.conf
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_AG = 'config/ag.skeleton.php';

    /**
     * config/routing/routing.yml
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CONFIG_ROUTING = 'config/routing.skeleton.php';

    /**
     * 識別値
     *
     * @access   public
     * @var      int
     */
    public $DOT_SAMURAI = 11;
    public $WWW_INDEX = 21;
    public $WWW_INFO = 22;
    public $WWW_HTACCESS = 23;
    public $WWW_SAMURAI_CSS = 241;
    public $WWW_SAMURAI_IMAGE_CLOSE = 251;
    public $WWW_SAMURAI_IMAGE_ERROR = 252;
    public $WWW_SAMURAI_IMAGE_INFO = 253;
    public $WWW_SAMURAI_IMAGE_RELOAD = 254;
    public $WWW_SAMURAI_IMAGE_TOGGLE = 255;
    public $WWW_SAMURAI_IMAGE_WARNING = 256;
    public $CONFIG_SAMURAI_YAML = 311;
    public $CONFIG_SAMURAI_DICON = 312;
    public $CONFIG_SAMURAI_FRONTFILTER = 313;
    public $CONFIG_RENDERER_SMARTY = 321;
    public $CONFIG_RENDERER_PHPTAL = 322;
    public $CONFIG_RENDERER_SIMPLE = 323;
    public $CONFIG_AG = 33;
    public $CONFIG_ROUTING = 34;



    /**
     * @implements
     */
    public function generate($project_name, $skeleton, $params = array())
    {
        //何もしない
    }


    /**
     * dotファイル系のgenerateメソッド
     *
     * @access     public
     * @param      string  $project_name   プロジェクト名
     * @param      string  $skeleton       スケルトン名
     * @param      array   $params         Rendererに渡される値
     * @param      int     $scope          空間識別値
     * @return     array   結果
     */
    public function generate4Dot($project_name, $skeleton, $params = array(), $scope = NULL)
    {
        if(!$scope) $scope = $this->DOT_SAMURAI;
        $params['project_name'] = $project_name;
        //ドットファイルの決定
        switch($scope){
            case $this->DOT_SAMURAI:
                $dot_file = Samurai_Config::get('generator.directory.samurai') . DS . '.samurai';
                break;
        }
        //generate
        $result = $this->_generate($dot_file, $skeleton, $params);
        return array($result, $dot_file);
    }


    /**
     * configファイル系のgenerateメソッド
     *
     * @access     public
     * @param      string  $project_name   プロジェクト名
     * @param      string  $skeleton       スケルトン名
     * @param      array   $params         Rendererに渡される値
     * @param      int     $scope          空間識別値
     * @return     array   結果
     */
    public function generate4Config($project_name, $skeleton, $params = array(), $scope = NULL)
    {
        if(!$scope) $scope = $this->CONFIG_SAMURAI_YAML;
        $params['project_name'] = $project_name;
        //コンフィグファイルの決定
        $config_dir = Samurai_Config::get('generator.directory.samurai') . DS . Samurai_Config::get('directory.config');
        switch($scope){
            case $this->CONFIG_SAMURAI_YAML:
                $config_file = "{$config_dir}/samurai/config.{$project_name}.yml";
                break;
            case $this->CONFIG_SAMURAI_DICON:
                $config_file = "{$config_dir}/samurai/samurai.{$project_name}.dicon";
                break;
            case $this->CONFIG_SAMURAI_FRONTFILTER:
                $config_file = "{$config_dir}/samurai/frontfilter.{$project_name}.yml";
                break;
            case $this->CONFIG_RENDERER_SMARTY:
                $config_file = "{$config_dir}/renderer/smarty.{$project_name}.php";
                break;
            case $this->CONFIG_RENDERER_PHPTAL:
                $config_file = "{$config_dir}/renderer/phptal.{$project_name}.php";
                break;
            case $this->CONFIG_RENDERER_SIMPLE:
                $config_file = "{$config_dir}/renderer/simple.{$project_name}.php";
                break;
            case $this->CONFIG_AG:
                $config_file = "{$config_dir}/activegateway/activegateway.production.yml";
                break;
            case $this->CONFIG_ROUTING:
                $config_file = "{$config_dir}/routing/routing.{$project_name}.yml";
                break;
        }
        //generate
        $result = $this->_generate($config_file, $skeleton, $params);
        return array($result, $config_file);
    }


    /**
     * wwwファイル系のgenerateメソッド
     *
     * @access     public
     * @param      string  $project_name   プロジェクト名
     * @param      string  $skeleton       スケルトン名
     * @param      array   $params         Rendererに渡される値
     * @param      int     $scope          空間識別値
     * @return     array   結果
     */
    public function generate4Www($project_name, $skeleton, $params = array(), $scope = NULL)
    {
        if(!$scope) $scope = $this->WWW_INDEX;
        $params['project_name'] = $project_name;
        $params['samurai_dir'] = Samurai_Config::get('generator.directory.samurai');
        //ファイルの決定
        $www_dir = Samurai_Config::get('generator.directory.samurai') . DS . 'www';
        $is_resource = false;
        switch($scope){
            case $this->WWW_INDEX:
                $www_file = "{$www_dir}/index.php";
                $params['samurai_file'] = SAMURAI_DIR . DS . 'Samurai.class.php';
                break;
            case $this->WWW_INFO:
                $www_file = "{$www_dir}/info.php";
                break;
            case $this->WWW_HTACCESS:
                $www_file = "{$www_dir}/.htaccess";
                break;
            case $this->WWW_SAMURAI_CSS:
                $www_file = "{$www_dir}/samurai/samurai.css";
                break;
            case $this->WWW_SAMURAI_IMAGE_CLOSE:
                $www_file = "{$www_dir}/samurai/close.gif";
                $is_resource = true;
                break;
            case $this->WWW_SAMURAI_IMAGE_ERROR:
                $www_file = "{$www_dir}/samurai/error.gif";
                $is_resource = true;
                break;
            case $this->WWW_SAMURAI_IMAGE_INFO:
                $www_file = "{$www_dir}/samurai/info.gif";
                $is_resource = true;
                break;
            case $this->WWW_SAMURAI_IMAGE_RELOAD:
                $www_file = "{$www_dir}/samurai/reload.gif";
                $is_resource = true;
                break;
            case $this->WWW_SAMURAI_IMAGE_TOGGLE:
                $www_file = "{$www_dir}/samurai/toggle.gif";
                $is_resource = true;
                break;
            case $this->WWW_SAMURAI_IMAGE_WARNING:
                $www_file = "{$www_dir}/samurai/warning.gif";
                $is_resource = true;
                break;
        }
        //generate
        $result = $this->_generate($www_file, $skeleton, $params, $is_resource);
        return array($result, $www_file);
    }



    /**
     * スケルトンの取得
     *
     * @access     public
     * @return     string
     */
    public function getSkeleton($filename)
    {
        $resources = array(
            $this->SKELETON_WWW_SAMURAI_IMAGE_CLOSE,
            $this->SKELETON_WWW_SAMURAI_IMAGE_ERROR,
            $this->SKELETON_WWW_SAMURAI_IMAGE_INFO,
            $this->SKELETON_WWW_SAMURAI_IMAGE_RELOAD,
            $this->SKELETON_WWW_SAMURAI_IMAGE_TOGGLE,
            $this->SKELETON_WWW_SAMURAI_IMAGE_WARNING,
        );
        if(in_array($filename, $resources)){
            return Samurai_Loader::getPath(sprintf('%s/samurai/%s', Samurai_Config::get('directory.resource'), $filename));
        } else {
            return parent::getSkeleton($filename);
        }
    }
}

