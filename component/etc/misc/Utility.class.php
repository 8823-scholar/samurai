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
 * なんだか便利な関数の詰め合わせ的なクラス
 *
 * PHP組み込み関数の補完などを目的としています。
 * 
 * @package    Samurai
 * @subpackage Etc.Misc
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Misc_Utility
{
    /**
     * 配列の再帰的マージを実現するメソッド
     *
     * @access     public
     * @return     array
     */
    public function array_merge()
    {
        $marged = NULL;
        foreach(func_get_args() as $arg){
            //最初は上書
            if($marged === NULL){
                $marged = $arg;
            //タイプが一致しない場合も上書
            } elseif(gettype($marged) != gettype($arg)){
                $marged = $arg;
            //配列でない場合も上書
            } elseif(!is_array($arg)){
                $marged = $arg;
            //お互い配列の場合
            } else {
                $int_key = true;
                foreach($arg as $_key => $_val){
                    if($int_key && is_int($_key)){
                        $marged[] = $_val;
                    } elseif(!isset($marged[$_key])){
                        $int_key = false;
                        $marged[$_key] = $_val;
                    } else {
                        $marged[$_key] = $this->array_merge($marged[$_key], $_val);
                    }
                }
            }
        }
        return $marged;
    }


    /**
     * マルチソート
     *
     * @access     public
     * @param      array   &$array
     * @param      string  $key
     * @param      string  $order
     * @return     array   並び替えられた配列
     */
    public static function multisort(&$array, $key, $order = SORT_ASC, $comparison = SORT_REGULAR)
    {
        //チェック
        if(!is_array($array)) return false;
        //基準となる配列の生成
        $base_array = array();
        foreach($array as $value){
            $base_array[] = $value[$key];
        }
        //並び替え
        return array_multisort($base_array, $order, $comparison, $array);
    }


    /**
     * ディレクトリの補完
     *
     * @access     public
     * @param      string  $directory   ディレクトリ
     * @param      int     $mode        作成する場合のモード
     */
    public function fillupDirectory($directory, $mode = 0777)
    {
        $current_dir = '';
        foreach($this->splitPath($directory) as $dir){
            $current_dir .= sprintf('%s%s', $dir, DS);
            if($current_dir != '' && !file_exists($current_dir) && !is_dir($current_dir)){
                if(is_writable(dirname($current_dir))){
                    mkdir($current_dir);
                    chmod($current_dir, $mode);
                } else {
                    throw new Samurai_Exception('No permission to write. -> ' . dirname($current_dir));
                }
            }
        }
    }


    /**
     * ディレクトリをOSを考慮して分割する
     *
     * @access     public
     * @param      string  $path
     * @return     array
     */
    public function splitPath($directory)
    {
        return preg_split('|[\\/]|', $directory);
    }


    /**
     * 配列をURL形式に変換する(ディープな配列にも対応)
     *
     * @access     public
     * @param      string  $base_url   基本URL
     * @param      array   $array      配列
     * @param      string  $base_key   基本キー
     * @return     string
     */
    public function array2Url($base_url, array $array = array(), $base_key = '')
    {
        foreach($array as $_key => $_val){
            if($base_key){
                $_key = sprintf('%s[%s]', $base_key, $_key);
            }
            if(is_array($_val)){
                $base_url .= $this->array2Url($base_url, $_val, $_key);
            } else {
                $base_url .= strpos($base_url, '?') !== false ? '&' : '?' ;
                $base_url .= sprintf('%s=%s', $_key, urlencode($_val));
            }
        }
        return $base_url;
    }


    /**
     * 文字列を真偽値に変換する
     *
     * true  -> true,on,1
     * false -> false,off,0
     *
     * 基本的には上記通りだが、仕組みはtrueでなければfalseである。
     *
     * @access     public
     * @param      string  $string   文字列
     * @return     boolean
     */
    public function str2Bool($string)
    {
        $trues = array('true', 'on', '1');
        return $string === true || in_array((string)$string, $trues);
    }


    /**
     * str_getcsvがPHPの後期のバージョンでしかサポートされていないので、別途サポート
     *
     * @access     public
     * @param      string   $input
     * @param      string   $delimiter
     * @param      string   $enclosure
     * @param      string   $escape
     * @return     array
     */
    public function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = "\\")
    {
        if(!function_exists('str_getcsv')){
            $fiveMBs = 5 * 1024 * 1024;
            $handle = fopen('php://temp/maxmemory:' . $fiveMBs, 'r+');
            fputs($handle, $input);
            rewind($handle);
            $data = fgetcsv($handle, 1000, $delimiter, $enclosure); //  $escape only got added in 5.3.0
            fclose($handle);
            return $data;
        } else {
            return str_getcsv($input, $delimiter, $enclosure, $escape);
        }
    }
}

