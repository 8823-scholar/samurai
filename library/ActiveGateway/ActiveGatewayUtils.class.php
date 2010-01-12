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
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    SVN: $Id$
 */

/**
 * ActiveGatewayの雑務クラス
 * 
 * @package    ActiveGateway
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class ActiveGatewayUtils
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
     * YAMLのロードを行う
     *
     * @access     public
     * @return     array   読み込み結果
     */
    public static function loadYaml($yaml_file)
    {
        //Samurai_Yamlの検索
        /*
        if(class_exists('Samurai_Yaml')){
            $config = Samurai_Yaml::load($yaml_file);
        } else
        */
        if(file_exists($yaml_file)){
            //syckエクステンションの検索
            if(function_exists('syck_load')){
                $config = syck_load(file_get_contents($yaml_file));
            }
            //Spycの検索
            else {
                if(!class_exists('Spyc')) require_once 'Spyc/spyc.php';
                $config = Spyc::YAMLLoad($yaml_file);
            }
        } else {
            $config = array();
        }
        return $config;
    }


    /**
     * オブジェクトを配列に変換する
     *
     * @access     public
     * @param      object  $object      対象インスタンス
     * @param      boolean $reflexive   再帰的に適用するかどうか
     * @return     array
     */
    public static function object2Array($object, $reflexive = false)
    {
        $result = array();
        if(is_object($object)){
            if(method_exists($object, 'toArray')){
                $result = $object->toArray();
            } else {
                foreach(get_object_vars($object) as $_key => $_val){
                    if(!preg_match('/^_/', $_key)){
                        if(is_object($_val) && $reflexive){
                            $_val = self::object2Array($_val, $reflexive);
                        }
                        $result[$_key] = $_val;
                    }
                }
            }
        } elseif(is_array($object)){
            foreach($object as $_key => $_val){
                if(is_object($_val) && $reflexive){
                    $_val = self::object2Array($_val, $reflexive);
                }
                $result[$_key] = $_val;
            }
        } else {
            return $object;
        }
        return $result;
    }
}

