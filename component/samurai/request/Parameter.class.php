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
 * パラメータ系(Request,Cookie,Sessionなど)の抽象クラス
 *
 * 基本的なメソッドやインターフェースを提供する
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Request_Parameter
{
    /**
     * 値を格納
     *
     * @access   protected
     * @var      array
     */
    protected $_parameters = array();





    /**
     * 値のインポート
     *
     * @access     public
     * @param      array   $parameters   パラメータ値
     */
    public function import(array $parameters)
    {
        $this->_parameters = array_merge($this->_parameters, $parameters);
    }



    /**
     * 値を取得する
     *
     * @access     public
     * @param      string  $key   キー
     * @return     mixed
     */
    public function getParameter($key, $default = NULL)
    {
        $keys = explode('.', $key);
        $parameter = $default;
        foreach($keys as $_key => $_val){
            if(!$_key && isset($this->_parameters[$_val])){
                $parameter = $this->_parameters[$_val];
            } elseif(is_array($parameter) && isset($parameter[$_val])){
                $parameter = $parameter[$_val];
            } else {
                $parameter = $default;
            }
        }
        return $parameter;
    }

    /**
     * getParameterのシノニム
     *
     * @access     public
     * @see        Samurai_Request_Parameter::getParameter
     */
    public function get()
    {
        return call_user_func_array(array($this, 'getParameter'), func_get_args());
    }


    /**
     * 全ての値を取得する
     *
     * @access     public
     * @return     array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }



    /**
     * 値の格納
     *
     * @access    public
     * @param     string  $key     パラメータ名
     * @param     mixed   $value   パラメータの値
     */
    public function setParameter($key, $value)
    {
        $keys = explode('.', $key);
        $key_str = '';
        foreach($keys as $key){
            $key_str .= (is_numeric($key) || !$key) ? "[{$key}]" : "['{$key}']" ;
        }
        $script = "\$this->_parameters{$key_str} = \$value;";
        eval($script);
    }

    /**
     * setParameterのシノニム
     *
     * @access     public
     * @see        Samurai_Request_Parameter::setParameter
     */
    public function set()
    {
        return call_user_func_array(array($this, 'setParameter'), func_get_args());
    }


    /**
     * 値を削除
     *
     * @access    public
     * @param     string  $key   パラメータ名
     */
    public function delParameter($key)
    {
        $keys = explode('.', $key);
        $key_str = '';
        foreach($keys as $key){
            if($key == '') return false;
            $key_str .= (is_numeric($key)) ? "[{$key}]" : "['{$key}']" ;
        }
        $script = "unset(\$this->_parameters{$key_str});";
        eval($script);
    }

    /**
     * delParameterのシノニム
     *
     * @access     public
     * @see        Samurai_Request_Parameter::delParameter
     */
    public function del()
    {
        return call_user_func_array(array($this, 'delParameter'), func_get_args());
    }
}

