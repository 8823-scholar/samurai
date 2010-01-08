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
 * オブジェクト指向的数値
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
class Etc_Oop_Integer extends Etc_Oop
{
    /**
     * 実体をセットする
     *
     * @access     public
     * @param      int     $entity
     */
    public function set($entity)
    {
        $this->_entity = is_float($entity) ? (float)$entity : (int)$entity;
    }


    /**
     * 文字列への変換
     *
     * @access     private
     * @return     string
     */
    public function __toString()
    {
        return $this->_entity;
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
            throw new Samurai_Exception('method not exists. -> ' . $method);
        }
    }
}

