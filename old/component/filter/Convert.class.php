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
 * Convert処理を行うFilter
 *
 * Request値に対してコンバートを行います
 * 指定のコンバーターが存在しない場合、PHPの関数へのブリッジを試みます
 *
 * <code>
 *  Convert:
 *      * : 'trim'           # すべての値にtrim
 *      foo : 'trim > bar'   # trim後にbarという値にリダイレクト
 *      bar : 'strtoupper | strtolower | ucfirst'   # パイプで複数のconverterを通すことも可能
 * </code>
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Convert extends Samurai_Filter
{
    /**
     * ConverterManagerコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Manager;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * パイプで分解したものを順番に格納
     *
     * @access   private
     * @var      array
     */
    private $_stacks = array();


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        $this->_createManager();
        $this->_createStacks();
        $this->_dissolveStacks();
    }


    /**
     * 積み上げられたスタックを解消していく
     *
     * @access     private
     */
    private function _dissolveStacks()
    {
        foreach($this->_stacks as $_key => $_val){
            //キーの分解
            $keys = $this->_resolveKeys($_key);
            //パイプで分解する
            $pipes = $this->_resolve($_val, '|');
            foreach($pipes as $pipe_value){
                $force_grouping = false;
                $redirects = $this->_resolve($pipe_value, '>');
                $converter = array_shift($redirects);
                if(preg_match('/^@(.*)$/', $converter, $matches)){
                    $force_grouping = true;
                    $converter = $matches[1];
                }
                $Converter = $this->Manager->getConverter($converter);
                //リダイレクトの指定がない場合
                if(!$redirects){
                    foreach($keys as $key){
                        $value = $this->Request->getParameter($key);
                        $value = $Converter->convert($value);
                        $this->Request->setParameter($key, $value);
                    }
                }
                //リダイレクトの指定がある場合
                elseif(count($keys) > 1 || $force_grouping){
                    $value = array();
                    foreach($keys as $key){
                        $value[$key] = $this->Request->getParameter($key);
                    }
                    $value = $Converter->convert($value);
                } elseif(count($keys) == 1){
                    $value = $this->Request->getParameter($keys[0]);
                    $value = $Converter->convert($value);
                }
                //リダイレクト
                foreach($redirects as $redirect){
                    $this->Request->setParameter($redirect, $value);
                    $keys = array($redirect);
                }
            }
        }
    }


    /**
     * キーを分解する
     *
     * @access     private
     * @param      string  $key   キー文字列
     * @return     array
     */
    private function _resolveKeys($key)
    {
        $keys = $this->_resolve($key, ',');
        if(in_array('*', $keys)){
            $keys = array_merge($keys, array_keys($this->Request->getParameters()));
            unset($keys[array_search('*', $keys)]);
        }
        $keys = array_unique($keys);
        return $keys;
    }


    /**
     * 文字列を指定の文字列で分解する
     *
     * @access     private
     * @param      string  $string      対象文字列
     * @param      string  $delimiter   区切り文字
     * @return     array
     */
    private function _resolve($string, $delimiter)
    {
        $delimiter = preg_quote($delimiter);
        return preg_split("/\s*{$delimiter}\s*/", trim($string));
    }



    /**
     * スタックの積み上げ
     *
     * @access     private
     */
    private function _createStacks()
    {
        foreach((array)$this->getAttributes() as $_key => $_val){
            $this->_stacks[$_key] = $_val;
        }
    }


    /**
     * ConverterManagerの生成
     *
     * @access     private
     */
    private function _createManager()
    {
        Samurai_Loader::loadByClass('Filter_Convert_Manager');
        $this->Manager = new Filter_Convert_Manager();
    }
}

