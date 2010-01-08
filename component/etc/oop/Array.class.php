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
 * オブジェクト指向的配列
 *
 * !!!!※!!!!
 * このクラスは遊び心から設置しているクラスです。
 * 仕様変更やサポートの中止など、報告なしに行われる可能性がありますので、業務等で使用するのは
 * 絶対におやめください。
 * 
 * @package    Samurai
 * @subpackage Etc.Oop
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Oop_Array extends Etc_Oop
{
    /**
     * 実体をセットする
     *
     * @access     public
     * @param      array  $entity
     */
    public function set($entity)
    {
        $this->_entity = (array)$entity;
    }


    /**
     * 文字列への変換
     *
     * @access     private
     * @return     string  文字列の実体
     */
    public function __toString()
    {
        return (string)$this->_entity;
    }


    /**
     * 配列へのアクセス
     *
     * @access     public
     * @param      string  $name   キー
     */
    public function __get($name)
    {
        $result = isset($this->_entity[$name]) ? $this->_entity[$name] : NULL ;
        return $this->cast($result);
    }

    /**
     * 配列にセット
     *
     * @access     public
     * @param      string  $name    キー
     * @param      mixed   $value   値
     */
    public function __set($name, $value)
    {
        $this->_entity[$name] = $value;
    }


    /**
     * PHP関数へのブリッジ
     *
     * @access     public
     * @param      string   $method   関数名
     * @param      array    $args     引数
     * @return     object   Etc_Oop
     */
    public function __call($method, $args = array())
    {
        if(function_exists($method)){
            array_unshift($args, $this->_entity);
            $result = call_user_func_array($method, $args);
            return $this->cast($result);
        } else {
            throw new Samurai_Exception('method not exists. -> '.$method);
        }
    }


    /**
     * XMLへの変換
     *
     * @access     public
     * @return     object  Etc_Oop_String
     */
    public function toXml()
    {
        
    }





    /**
     * 配列のサイズを返却
     *
     * @access     public
     * @return     object   Etc_Oop_Integer
     */
    public function length()
    {
        return $this->cast(count($this->_entity));
    }

    /**
     * 配列の先頭に加える
     *
     * @access     public
     * @param      mixed    $value
     * @return     object   $this
     */
    public function unshift($value)
    {
        array_unshift($this->_entity, $value);
        return $this;
    }

    /**
     * 配列の最後に加える
     *
     * @access     public
     * @param      mixed    $value
     * @return     object   $this
     */
    public function push($value)
    {
        array_push($this->_entity, $value);
        return $this;
    }

    /**
     * 配列の先頭を取得する
     *
     * @access     public
     * @return     object   Etc_Oop
     */
    public function shift()
    {
        return $this->cast(array_shift($this->_entity));
    }

    /**
     * 配列の最後から取得する
     *
     * @access     public
     * @return     object   Etc_Oop
     */
    public function pop()
    {
        return $this->cast(array_pop($this->_entity));
    }
}

