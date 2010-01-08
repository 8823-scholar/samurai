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
 * Tokenチケットクラス
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Token_Ticket
{
    /**
     * トークン名
     *
     * @access   public
     * @var      string
     */
    public $name = '';

    /**
     * Sessionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Session;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * Tokenの名前を返却
     *
     * @access    public
     * @return    string
     */
    public function getName()
    {
        if($this->_name == '') $this->_name = '_token';
        return $this->_name;
    }

    /**
     * Tokenの名前を設定
     *
     * @access    public
     * @param     string  $name   Tokenの名前
     */
    public function setName($name)
    {
        $this->_name = $name;
    }


    /**
     * Tokenの値を返却
     *
     * @access    public
     * @return    string
     */
    public function getValue()
    {
        return $this->Session->getParameter($this->getName());
    }



    /**
     * Tokenの値を生成
     *
     * @access    public
     */
    public function build()
    {
        $this->Session->setParameter($this->getName(), md5(uniqid(rand(),1)));
    }


    /**
     * Tokenの値を比較
     *
     * @access    public
     * @return    boolean
     */
    public function check()
    {
        if($this->getValue() == ''){
            return false;
        } else {
            return $this->getValue() === $this->Request->getParameter($this->getName());
        }
    }


    /**
     * Tokenの値を削除
     *
     * @access    public
     */
    public function remove()
    {
        $this->Session->delParameter($this->getName());
    }
}

