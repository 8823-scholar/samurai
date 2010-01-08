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
 * 配列、オブジェクトから循環参照を取り除くためのクラス
 *
 * dBugとは無関係です。
 * 
 * @package    Samurai
 * @subpackage Library.dBug
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class dBug_DumpHelper
{
    /**
     * 参照を保持
     *
     * @access   private
     * @var      array
     */
    private $_ref_stack = array();


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * 循環参照を取り除いたものを返却
     *
     * @access     public
     * @param      mixed   $var   対象変数
     * @return     mixed   結果
     */
    public function removeCircularReference($var, $depth = 0)
    {
        //初期化
        $result = NULL;
        //オブジェクトの場合
        if(is_object($var)){
            if($depth > 5) return '5 over nest...';
            if($this->_push($var)){
                $result = clone $var;
                foreach(array_keys(get_object_vars($var)) as $_val){
                    $temp = $this->removeCircularReference($var->$_val, $depth + 1);
                    $result->$_val =& $temp;
                    unset($temp);
                }
                $this->_pop();
            } else {
                $result = $this->_getSubstituteFor($var);
            }
        //配列の場合
        } elseif(is_array($var)){
            if($this->_push($var)){
                $result = array();
                foreach(array_keys($var) as $_val){
                    $temp = $this->removeCircularReference($var[$_val], $depth + 1);
                    $result[$_val] =& $temp;
                    unset($temp);
                }
                $this->_pop();
            } else {
                $result = $this->_getSubstituteFor($var);
            }
        //なんでもない場合
        } else {
            $result = $var;
        }
        return $result;
    }


    /**
     * 循環しているobject or arrayの代替表現を返す
     *
     * @access     private
     * @param      mixed   $var   対象変数
     * @return     string
     */
    private function _getSubstituteFor($var)
    {
        if(is_object($var)){
            return sprintf('&object(%s)', get_class($var));
        } elseif(is_array($var)) {
            return '&array';
        } else {
            return '&string';
        }
    }


    /**
     * 参照をスタックに積む
     * 既にスタック内に同一の参照が存在する場合、スタックには積まずfalseを返す。
     *
     * @access     private
     * @param      mixed   $var   対象変数
     * @return     boolean
     */
    private function _push(&$var)
    {
        $var_type = gettype($var);
        foreach($this->_ref_stack as $_key => $_val){
            if($var_type == gettype($this->_ref_stack[$_key])
                && $this->isReference($var, $this->_ref_stack[$_key])){
                return false;
            }
        }
        $this->_ref_stack[] =& $var;
        return true;
    }


    /**
     * スタックから参照を取り除く
     *
     * @access     private
     */
    private function _pop()
    {
        array_pop($this->_ref_stack);
    }


    /**
     * スタックのリセット。
     * 厳密には処理が終わった時点でスタックは空になっているはずだが、念のため。
     *
     * @access     public
     */
    public function reset()
    {
        $this->_ref_stack = array();
    }


    /**
     * 参照かどうかを判断する
     *
     * @access     public
     * @param      mixed   $first    第一引数
     * @param      mixed   $second   第二引数
     * @return     boolean 参照かどうか
     */
    public function isReference(&$first, &$second)
    {
        //オブジェクトの場合
        if(is_object($first)){
            return $first === $second;
        }
        //その他の場合
        $temp = $first;
        $first = uniqid('dumphelper');
        $is_ref = $first === $second;
        $first = $temp;
        return $is_ref;
    }
}

