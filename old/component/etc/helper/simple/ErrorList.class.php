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
 * ErrorListとRenderer_Simpleをブリッジするためのヘルパー
 * 
 * @package    Samurai
 * @subpackage Etc.Helper.Simple
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Helper_Simple_ErrorList
{
    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }



    /**
     * エラー取得メソッド
     *
     * @access     public
     * @return     array   エラー配列
     */
    public function getMessages($action = '', $keep_key = false)
    {
        //Actionの決定
        if($action){
            $actions = $this->_resolveActions($action);
        } else {
            $actions = $this->ActionChain->getAllActionName();
        }
        //エラーの取得
        $errors = array();
        foreach($actions as $action){
            $ErrorList = $this->ActionChain->getErrorListByName($action);
            if($keep_key){
                $errors += $ErrorList->getAllMessage();
            } else {
                $errors += $ErrorList->getAllMessages();
            }
        }
        return $errors;
    }


    /**
     * Actionの分解
     *
     * @access     private
     * @param      string  $action   Action名が区切り文字で連結されているもの
     * @return     array
     */
    private function _resolveActions($action)
    {
        return preg_split('/\s*[,\|]\s*/', $action);
    }
}

