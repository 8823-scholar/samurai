<?php
/**
 * PHP version 5.
 *
 * Copyright (c) Samurai Framework Project, All rights reserved.
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
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * Samurai用走査クラスの実装
 * 
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Iterator implements Iterator
{
    /**
     * 要素
     *
     * @access   protected
     * @var      array
     */
    protected $_elements = array();

    /**
     * index
     *
     * @access   protected
     * @var      int
     */
    protected $_index = 0;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * 要素の追加
     *
     * @access     public
     * @param      mixed   $element   要素
     * @param      mixed   $key       キー
     */
    public function addElement($element, $key=NULL)
    {
        if($key === NULL){
            $this->_elements[] = $element;
        } else {
            $this->_elements[$key] = $element;
        }
    }


    /**
     * 空にする
     *
     * @access     public
     */
    public function clear()
    {
        $this->_elements = array();
    }


    /**
     * 逆にする
     *
     * @access     public
     */
    public function reverse()
    {
        $this->_elements = array_reverse($this->_elements);
    }


    /**
     * 要素を削除する
     *
     * @access     public
     * @param      int     $key
     */
    public function remove($_key)
    {
        if(isset($this->_elements[$_key])){
            unset($this->_elements[$_key]);
            $this->_elements = array_values($this->_elements);
            if($_key <= $this->_index){
                $this->_index--;
            }
        }
    }


    /**
     * 要素数を取得
     *
     * @access     public
     * @return     int     要素数
     */
    public function getSize()
    {
        return count($this->_elements);
    }





    /**
     * implements.
     */
    public function rewind()
    {
        $this->_index = 0;
    }
    public function key()
    {
        return $this->_index;
    }
    public function current()
    {
        return isset($this->_elements[$this->_index]) ? $this->_elements[$this->_index] : false;
    }
    public function next()
    {
        $this->_index++;
    }
    public function valid()
    {
        $current = $this->current();
        if($current === false) $this->rewind();
        return $current !== false;
    }
}

