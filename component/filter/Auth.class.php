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
 * 認証のためのフィルター
 *
 * 基本的な認証をサポートしています。
 * 現在サポートしているのは以下のとおりです。
 *
 * - role  (役割)
 * - level (権限レベル)
 * - http  (BASIC認証)
 * - ip    (IPアドレス)
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Auth extends Samurai_Filter
{
    /**
     * AuthManagerコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Manager;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        //初期化
        $this->_init();
        //Authorの決定
        $Author = $this->Manager->getAuthor($this->getAttribute('type'));
        //認証
        $result = $Author->authorize($this->getAttributes());
        if($result !== true){
            if(is_array($result)){
                $message = array_pop($result);
                $result  = array_shift($result);
            } else {
                $message = '';
            }
            $ErrorList = $this->ActionChain->getCurrentErrorList();
            $ErrorList->setType($result);
            if($message && !$ErrorList->isExists()) $ErrorList->add('authorize', $message);
        }
    }


    /**
     * 初期化
     *
     * @access     private
     */
    private function _init()
    {
        //Managerの生成
        Samurai_Loader::loadByClass('Filter_Auth_Manager');
        $this->Manager = new Filter_Auth_Manager();
    }
}

