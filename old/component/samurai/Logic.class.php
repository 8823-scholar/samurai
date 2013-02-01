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
 * Logicの抽象クラス
 *
 * 非推奨クラス
 * 特に理由がない限り、Samurai_Modelを使用してください
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Logic
{
    /**
     * ActiveGateway
     *
     * @access   public
     * @var      object
     */
    public $AG;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * AGConditionの初期化を行う
     *
     * @access     protected
     * @param      object  $AGdto   ActiveGatewayCondition
     */
    protected function _initAGCondition(&$AGdto)
    {
        if(is_array($AGdto)){
            $_AGdto = ActiveGateway::getCondition();
            foreach($AGdto as $_key => $_val){
                $_AGdto->$_key = $_val;
            }
            $AGdto = $_AGdto;
        } elseif(is_object($AGdto)){
            if(!$AGdto instanceof ActiveGatewayCondition){
                $_AGdto = ActiveGateway::getCondition();
                foreach($AGdto as $_key => $_val){
                    $_AGdto->$_key = $_val;
                }
                $AGdto = $_AGdto;
            }
        } else {
            $AGdto = ActiveGateway::getCondition();
        }
    }
}

