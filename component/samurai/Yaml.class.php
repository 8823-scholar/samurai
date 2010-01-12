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
 * Yaml解析用のクラス
 *
 * 独自実装部分は極わずかで、基本的にはsyckやSpycのような外部ライブラリに依存している
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Yaml
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
     * @param      string  $yaml_file   YAMLファイルパス
     * @return     array
     */
    public static function load($yaml_file)
    {
        $result = array();
        $result = self::_merge($result, self::_load($yaml_file));
        if(defined('SAMURAI_APPLICATION_NAME')){
            $app_yaml_file = self::_replaceYamlName($yaml_file, SAMURAI_APPLICATION_NAME);
            $result = self::_merge($result, self::_load($app_yaml_file));
        }
        if(defined('SAMURAI_ENVIRONMENT')){
            $env_yaml_file = self::_replaceYamlName($yaml_file, SAMURAI_ENVIRONMENT);
            $result = self::_merge($result, self::_load($env_yaml_file));
        }
        return $result;
    }


    /**
     * ロード処理実体
     * 一度ロードしたファイルはキャッシュします
     *
     * @access     private
     * @param      string  $yaml_file   YAMLファイルパス
     * @return     array
     */
    private static function _load($yaml_file)
    {
        if($cache_file = self::hasCache($yaml_file)){
            return self::loadByCache($cache_file);
        } elseif(self::enableSyck()){
            return self::loadBySyck($yaml_file);
        } elseif(self::enableSpyc()){
            return self::loadBySpyc($yaml_file);
        } else {
            throw new Samurai_Exception('YAML parser (example Syck or Spyc) is not found...');
        }
    }


    /**
     * キャッシュからロードする
     *
     * @access     public
     * @param      string  $cache_file   キャッシュパス
     * @return     array
     */
    public static function loadByCache($cache_file)
    {
        $result = unserialize(file_get_contents($cache_file));
        return $result;
    }


    /**
     * Syckを使用してロードする
     *
     * @access     public
     * @param      string  $yaml_file   YAMLファイルパス
     * @return     array
     */
    public static function loadBySyck($yaml_file)
    {
        $contents = self::_includeYaml($yaml_file);
        $result = syck_load($contents);
        if(!$result) $result = array();
        self::_saveCache($yaml_file, $result);
        return $result;
    }


    /**
     * Spycを使用してロードする
     *
     * @access     public
     * @param      string  $yaml_file   YAMLファイルパス
     * @return     array
     */
    public static function loadBySpyc($yaml_file)
    {
        self::_loadSpyc();
        $Spyc = new Spyc();
        $contents = self::_includeYaml($yaml_file);
        $result = $Spyc->load($contents);
        if(!$result) $result = array();
        self::_saveCache($yaml_file, $result);
        return $result;
    }





    /**
     * キャッシュが存在するかどうか
     *
     * @access     public
     * @param      string  $yaml_file
     * @return     boolean
     */
    public static function hasCache($yaml_file)
    {
        $cache_file = Samurai_Loader::getPath(sprintf('temp/yaml/%s', urlencode($yaml_file)));
        $yaml_file = Samurai_Loader::getPath($yaml_file);
        if(Samurai_Loader::isReadable($cache_file) && Samurai_Loader::isReadable($yaml_file)){
            return filemtime($cache_file) > filemtime($yaml_file) ? $cache_file : false ;
        }
        return false;
    }


    /**
     * キャッシュを保存する
     *
     * @access     private
     */
    private function _saveCache($yaml_file, $data = array())
    {
        if(!Samurai_Config::get('enable.yaml_cache')) return false;
        $cache_dir = Samurai_Loader::getPath('temp' . '/yaml', true);
        if(!file_exists($cache_dir)) mkdir($cache_dir) && @chmod($cache_dir, 0777) ;
        $cache_file = sprintf('%s/%s', $cache_dir, urlencode($yaml_file));
        file_put_contents($cache_file, serialize($data)) && @chmod($cache_file, 0777);
    }


    /**
     * Syackでの解析が可能かどうか
     * つまり、syack_load関数が存在するかどうか
     *
     * @access     public
     * @return     boolean
     */
    public static function enableSyck()
    {
        return function_exists('syck_load');
    }


    /**
     * Spycでの解析が可能かどうか
     * つまり、Spycライブラリが存在し、かつ読み込めて、利用できるかどうか
     *
     * @access     public
     * @return     boolean
     */
    public static function enableSpyc()
    {
        return Samurai_Loader::isReadable(Samurai_Loader::getPath('library/Spyc/spyc.php'))
                    || Samurai_Loader::isReadable(Samurai_Loader::getPath('spyc/spyc.php', false, explode(PS, get_include_path())));
    }


    /**
     * Spycライブラリをロードする
     *
     * @access     private
     * @return     boolean
     */
    private static function _loadSpyc()
    {
        $spyc = Samurai_Loader::getPath('library/Spyc/spyc.php');
        if(Samurai_Loader::isReadable($spyc)){
            require_once $spyc;
            return true;
        }
        $spyc = Samurai_Loader::getPath('spyc/spyc.php', false, explode(PS, get_include_path()));
        if(Samurai_Loader::isReadable($spyc)){
            require_once $spyc;
            return true;
        }
    }


    /**
     * YamlファイルにPHPコードを記述できるように一工夫
     * ただし、キャッシュされるファイルに関してはPHPコード解釈後の内容がキャッシュされるので、
     * 元の情報を変更した後は、キャッシュを削除することをおすすめします
     *
     * @access     private
     * @param      string  $yaml_file   YAMLファイルパス
     * @return     string
     */
    private static function _includeYaml($yaml_file)
    {
        $yaml_file = Samurai_Loader::getPath($yaml_file);
        if(Samurai_Loader::isReadable($yaml_file)){
            if(class_exists('Samurai_Logger', false)) Samurai_Logger::debug('YAML loaded. -> %s', $yaml_file);
            ob_start();
            include($yaml_file);
            $contents = ob_get_clean();
            $contents = $contents === NULL ? '' : preg_replace_callback('/%([a-z0-9\._]*?)%/i', 'Samurai_Yaml::_tag2Entity', $contents) ;
            return $contents;
        }
        return '[]';
    }


    /**
     * YAMLファイルに「%...%」形式で書かれた記述を展開する
     * %の中身が大文字のみで構成されていると、定数として解釈しようとします
     * その他の場合は、全てSamurai_Configから取得しようとします
     *
     * @access     private
     * @param      array   $matches   ヒットした箇所の情報
     * @return     string
     */
    private static function _tag2Entity($matches)
    {
        $value = $matches[1];
        switch(true){
            //大文字のみ(定数として解釈)
            case preg_match('/[A-Z_]+/', $value) && defined($value):
                $value = constant($value);
                break;
            //Samurai_Config検索
            case is_scalar(Samurai_Config::get($value)):
                $value = Samurai_Config::get($value);
                break;
            //元に戻す
            default:
                $value = '%'.$value.'%';
                break;
        }
        return $value;
    }


    /**
     * マージ
     *
     * @access     private
     * @return     array
     */
    private static function _merge($array1, $array2)
    {
        try {
            $Utility = Samurai::getContainer()->getComponent('Utility');
            $result = $Utility->array_merge($array1, $array2);
        } catch(Samurai_Exception $E){
            $result = $array1;
            if(is_array($array2)){
                foreach($array2 as $_key => $_val){
                    if(!isset($result[$_key])){
                        $result[$_key] = $_val;
                    } else {
                        $result[$_key] = self::_merge($result[$_key], $_val);
                    }
                }
            } else {
                $result = $array2;
            }
        }
        return $result;
    }


    /**
     * yamlの名前を置き換える
     *
     * @access     private
     * @param      string  $yaml_file   元のファイル名
     * @param      string  $postfix     差し込み文字列
     * @return     string
     */
    private static function _replaceYamlName($yaml_file, $postfix)
    {
        $info = pathinfo($yaml_file);
        if(isset($info['extension'])){
            $filename = preg_replace(sprintf('/\.%s$/', $info['extension']), '', $info['basename']);
            $filename = sprintf('%s.%s.%s', $filename, $postfix, $info['extension']);
        } else {
            $filename = sprintf('%s.%s', $info['basename'], $postfix);
        }
        return $info['dirname'] . '/' . $filename;
    }
}

