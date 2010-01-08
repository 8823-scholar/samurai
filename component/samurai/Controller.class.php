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
 * 世間一般のFrameworkでのControllerとは少々ちがった動きをするが、
 * Samurai Frameworkの処理の流れの起点になるクラス
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Controller
{
    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;

    /**
     * ConfigStackコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ConfigStack;

    /**
     * FilterChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $FilterChain;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * Samurai Frameworkの起動トリガー
     *
     * @access     public
     */
    public function execute()
    {
        //FrontFilterの作動
        $this->_frontFilter();
        //Actionの追加
        $this->Request->dispatchAction();
        $this->ActionChain->clear();
        $this->ActionChain->add($this->Request->getParameter(Samurai_Config::get("action.request_key")));
        //Actionのスタックがある限り動作をつづける
        while($this->ActionChain->hasNext()){
            //ConfigStackの構築
            $this->ConfigStack->clear();
            $this->ConfigStack->execute();
            //FilterChainの構築＆実行
            $this->FilterChain->clear();
            $this->FilterChain->build($this->ConfigStack);
            $this->FilterChain->execute();
            //次のアクションへ
            $this->ActionChain->next();
        }
    }


    /**
     * FrontFilterの起動
     * 通常のFilterChainでは、Actionが決定しないと動作しないために、別途手前で動作させる
     *
     * @access     private
     */
    private function _frontFilter()
    {
        $this->ConfigStack->clear();
        $this->ConfigStack->import(Samurai_Config::get('directory.config').'/samurai/frontfilter.yml', NULL);
        $this->FilterChain->clear();
        $this->FilterChain->build($this->ConfigStack);
        $this->FilterChain->execute();
    }
}

