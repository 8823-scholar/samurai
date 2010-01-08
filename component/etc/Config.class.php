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
 * 設定管理クラス
 *
 * 基本的にはSamurai_Configと同等の動きをするが、決定的に違うのは静的参照か動的参照かの違いです。
 * Samurai_Configは単独で動作し静的に参照されますが、Etc_Configはオブジェクト化される事が前提となります。
 * また両者に繋がりはありません。
 * こちらはアプリケーション用の設定クラスだと思ってください。
 * 
 * @package    Samurai
 * @subpackage Etc
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  The BSD License
 */
class Etc_Config
{
    /**
     * 設定情報を保持
     *
     * @access   protected
     * @var      array
     */
    protected $_config = array();

    /**
     * 読み込まれた設定ファイルを保持
     *
     * @access   protected
     * @var      array
     */
    protected $_files = array();

    /**
     * Utilityコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Utility;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * 値の取得
     *
     * @access     public
     * @param      string  $key       設定キー
     * @param      mixed   $default   デフォルト値
     * @return     mixed
     */
    public function get($key, $default = NULL)
    {
        $keys = explode('.', $key);
        $value = $default;
        foreach($keys as $i => $_key){
            if(!$i && isset($this->_config[$_key])){
                $value = $this->_config[$_key];
            } elseif(is_array($value) && isset($value[$_key])){
                $value = $value[$_key];
            } else {
                $value = $default;
                break;
            }
        }
        return $value;
    }


    /**
     * 値の設定
     *
     * @access     public
     * @param      string  $key     設定キー
     * @param      mixed   $value   設定値
     */
    public function set($key, $value)
    {
        $keys = explode('.', $key);
        $key_str = '';
        foreach($keys as $key){
            $key_str .= (is_numeric($key) || !$key) ? "[{$key}]" : "['{$key}']" ;
        }
        $script = sprintf('$this->_config%s = $value;', $key_str);
        eval($script);
    }


    /**
     * その値を保持しているかチェック
     *
     * @access     public
     * @param      string  $key   設定キー
     * @return     boolean
     */
    public function has($key)
    {
        $result = $this->get($key);
        return $result !== NULL;
    }


    /**
     * 設定ファイルから値を取り込む
     *
     * @access     public
     * @param      string  $config_file   設定ファイル
     */
    public function import($config_file)
    {
        $config = Samurai_Yaml::load($config_file);
        $this->add($config);
        $this->_files[] = $config_file;
    }


    /**
     * 配列をまとめて追加する
     *
     * @access     public
     * @param      array   $config     設定配列
     */
    public function add(array $config)
    {
        if($this->Utility){
            $this->_config = $this->Utility->array_merge($this->_config, $config);
        } else {
            $this->_config = array_merge($this->_config, $config);
        }
    }


    /**
     * 設定された値をすべて取得する
     *
     * @access     public
     * @param      string  $prefix   prefixを指定できる
     * @return     array
     */
    public function getAll($prefix = NULL)
    {
        if($prefix === NULL){
            return $this->_config;
        } else {
            $config = $this->get($prefix);
            return (array)$config;
        }
    }


    /**
     * 初期化
     *
     * @access     public
     */
    public function clear()
    {
        $this->_config = array();
        $this->_files = array();
    }
}

