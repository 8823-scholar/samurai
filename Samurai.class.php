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

defined('PS') ? NULL : define('PS', PATH_SEPARATOR) ;
defined('DS') ? NULL : define('DS', DIRECTORY_SEPARATOR) ;
defined('SAMURAI_DIR') ? NULL : define('SAMURAI_DIR', dirname(__FILE__));

/**
 * Main class of Samurai Framework.
 * 
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai
{
    /**
     * version
     *
     * @const   string
     */
    const VERSION = '3.0.0';

    /**
     * state
     *
     * @const   string
     */
    const STATE = 'beta';

    /**
     * Samuraiディレクトリの候補
     * Samurai_Loaderクラスでロードする際の検索対象になる
     *
     * @var     array
     */
    private static $_samurai_dirs = array( SAMURAI_DIR );





    /**
     * コンストラクタ
     *
     * @access     private
     */
    private function __construct()
    {
    }
    
    
    /**
     * Samuraiの初期化を行う
     *
     * @access     public
     */
    public static function init()
    {
        //スタート
        self::_setEnvironment();

        //主要クラスのロード
        self::_load();

        //設定情報の取得
        Samurai_Config::import('config/samurai/config.yml');

        //DIContainerの初期化
        $Container = self::getContainer();
        $Container->import(Samurai_Config::get('container.dicon'));

        //Loggerの初期化
        $loggers = Samurai_Config::get('loggers');
        foreach((array)$loggers as $alias => $define){
            Samurai_Logger::addClient($alias, $define);
        }

        //環境用設定ファイルの読込
        if(SAMURAI_ENVIRONMENT != 'production') Samurai_Loader::includes('config/environment/' . SAMURAI_ENVIRONMENT . '.php');
        Samurai_Loader::includes('config/environment/production.php');
    }


    /**
     * 環境定数を設定する
     *
     * @access   private
     */
    private static function _setEnvironment()
    {
        define('SAMURAI_START', microtime(true));
        if($env = getenv('SAMURAI_ENVIRONMENT')){
            defined('SAMURAI_ENVIRONMENT') ? NULL : define('SAMURAI_ENVIRONMENT', $env);
        } else {
            defined('SAMURAI_ENVIRONMENT') ? NULL : define('SAMURAI_ENVIRONMENT', 'development');
        }
    }


    /**
     * 主要クラスをロードする
     *
     * @access     private
     */
    private static function _load()
    {
        require_once SAMURAI_DIR . '/component/samurai/Loader.class.php';
        Samurai_Loader::load('component/samurai/Config.class.php');
        Samurai_Loader::load('component/samurai/Exception.class.php');
        Samurai_Loader::load('component/samurai/Yaml.class.php');
        Samurai_Loader::load('component/samurai/container/Factory.class.php');
        Samurai_Loader::load('component/samurai/Logger.class.php');
        Samurai_Loader::appendIncludePath();
        spl_autoload_register(array('Samurai_Loader', 'autoload'));

        // composer auto load
        require_once SAMURAI_DIR . '/vendor/autoload.php';
    }


    /**
     * DIContainerの取得
     *
     * @access     public
     * @return     object  Samurai_Container
     */
    public static function getContainer($namespace=NULL)
    {
        if($namespace === NULL) $namespace = Samurai_Config::get('container.name');
        return Samurai_Container_Factory::create($namespace);
    }



    /**
     * samurai_dirを取得する
     *
     * @access     public
     * @return     array
     */
    public static function getSamuraiDirs()
    {
        return self::$_samurai_dirs;
    }


    /**
     * samurai_dirを追加する
     *
     * @param      string  $dir   ディレクトリ
     */
    public static function unshiftSamuraiDir($dir)
    {
        array_unshift(self::$_samurai_dirs, $dir);
    }





    /**
     * is production environment
     *
     * @access  public
     * @return  boolean
     */
    public static function isProduction()
    {
        return defined('SAMURAI_ENVIRONMENT') && SAMURAI_ENVIRONMENT === 'production';
    }
}

