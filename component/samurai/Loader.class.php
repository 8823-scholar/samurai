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
 * ロード処理の担当クラス
 *
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Loader
{
    /**
     * コンストラクタ
     *
     * @access     private
     */
    private function __construct()
    {
        
    }





    /**
     * ロード
     *
     * @access     public
     * @param      string  $path   パス
     * @param      array   $dirs
     */
    public static function load($path, $dirs = NULL)
    {
        $file = self::getPath($path, $dirs);
        if(self::isReadable($file)){
            include_once($file);
            return true;
        } else {
            return (boolean)@include_once($path);
        }
    }


    /**
     * クラスからロード
     *
     * component_dirを優先して検索した後に、library_dirを検索して、
     * 最後にinclude_pathを総ざらいします
     *
     * @access     public
     * @param      string  $class_name
     * @param      array   $dirs
     */
    public static function loadByClass($class_name, $dirs = NULL)
    {
        if(class_exists($class_name, false)) return true;

        $class_path = self::getPathByClass($class_name);
        //component_dirの検索
        if(preg_match('|^action/|', $class_path)){
            $component_path = Samurai_Config::get('directory.action') . DS . preg_replace('|^action/|', '', $class_path);
        } else {
            $component_path = Samurai_Config::get('directory.component') . DS . $class_path;
        }
        if(!self::load($component_path, $dirs)){
            //library_dirの検索
            $library_path = Samurai_Config::get('directory.library') . DS . $class_path;
            if(!self::load($library_path)){
                //PEARの検索
                $pear_path = str_replace('_', '/', $class_name) . '.php';
                if(!self::load($pear_path, explode(PS, get_include_path()))){
                    return self::load($class_path, explode(PS, get_include_path()));
                }
            }
        }
        return true;
    }


    /**
     * ファイルのパスを取得する
     * 見つからない場合でも、最後の候補ディレクトリに存在した場合のパスが返される
     *
     * @access     public
     * @param      string  $path        パス
     * @param      boolean $firstonly   存在するかどうかに関わらず、優先度の高い場所を返却するかどうか
     * @param      array   $dirs        候補ディレクトリ
     * @return     string
     */
    public static function getPath($path, $firstonly = false, $dirs = NULL)
    {
        $pathes = self::getPathes($path, $dirs);
        foreach($pathes as $i => $file){
            if(self::isReadable($file)){
                return $file;
            }
            if($firstonly && !$i) return $file;
        }
        return $file;
    }


    /**
     * あるパスに対する候補ディレクトリを全て返却する
     *
     * @access     public
     * @return     array
     */
    public static function getPathes($path, $dirs = NULL)
    {
        $pathes = array();
        if(self::isAbsolutePath($path)){
            $pathes[] = $path;
        } else {
            if($dirs === NULL) $dirs = Samurai::getSamuraiDirs();
            foreach($dirs as $dir){
                $pathes[] = $dir . DS . $path;
            }
        }
        return $pathes;
    }


    /**
     * ファイルパスをクラス名から取得する
     * ここが規約となる
     *
     * @access     public
     * @param      string  $class_name
     * @return     string
     */
    public static function getPathByClass($class_name)
    {
        $names = explode('_', $class_name);
        $main_name = ucfirst(array_pop($names));
        foreach($names as &$name){
            $name = strtolower($name);
        }
        array_push($names, $main_name);
        return join(DS, $names) . '.class.php';
    }





    /**
     * 登録されているSamurai_Dirを走査し、発見されたファイルをすべてincludeする
     *
     * @access     public
     * @param      string  $path
     */
    public static function includes($path)
    {
        if(Samurai_Loader::isAbsolutePath($path)){
            include($path);
        } else {
            foreach(Samurai::getSamuraiDirs() as $dir){
                $filepath = $dir . DS . $path;
                if(Samurai_Loader::isReadable($filepath)){
                    include_once($filepath);
                }
            }
        }
    }


    /**
     * SamuraiFW用のオートロード
     *
     * @access     public
     * @param      string  $class_name   クラス名
     */
    public static function autoload($class_name)
    {
        self::loadByClass($class_name);
    }





    /**
     * ファイルが読み込み可能かチェックする
     *
     * @access     public
     * @param      string  $file   ファイルの絶対パス
     * @return     boolean
     */
    public static function isReadable($file)
    {
        return file_exists($file) && is_readable($file);
    }


    /**
     * 絶対パスかどうかを判断する
     *
     * @access     public
     * @param      string  $path
     * @return     boolean
     */
    public static function isAbsolutePath($path)
    {
        if(preg_match('/^WIN/', PHP_OS)){
            return preg_match('/^[A-Z]:\\\/i', $path);
        } else {
            return preg_match('|^/|', $path);
        }
    }


    /**
     * include_pathにsamurai_dirのlibraryを追加する
     *
     * @access     public
     */
    public function appendIncludePath()
    {
        foreach(array_reverse(Samurai::getSamuraiDirs()) as $dir){
            set_include_path($dir . DS . 'library' . PS . get_include_path());
        }
    }
}

