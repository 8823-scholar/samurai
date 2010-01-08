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
 * Token処理を行うFilter
 *
 * 「Token」とは、和訳すると「証拠」という意味であり、
 * 要は、ユーザーがこちらの意図する遷移できたかどうかの証明に使われます。
 * フォームなどの二重投稿禁止などは、このTokenで簡単に処理できます。
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Token extends Samurai_Filter
{
    /**
     * Tokenコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Token;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;

    /**
     * ErrorListコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ErrorList;

    /**
     * 動作モード
     *
     * @access   private
     * @var      array
     */
    private $_modes = array();


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();

        $this->_createComponent();
        $this->_initToken();
        $this->_execute();
    }


    /**
     * Tokenの検証
     *
     * @access     private
     */
    private function _execute()
    {
        foreach($this->_modes as $mode){
            switch($mode){
                case 'build':
                    $this->Token->build();
                    break;
                case 'check':
                    if(!$this->Token->check()){
                        $this->ErrorList->setType(Samurai_Config::get('error.token'));
                    }
                    break;
                case 'remove':
                    $this->Token->remove();
                    break;
                default:
                    Samurai_Logger::error('[filter token] invalid mode. -> %s', $mode);
                    break;
            }
        }
    }


    /**
     * コンポーネントの生成
     *
     * @access     private
     */
    private function _createComponent()
    {
        //Token
        $Container = Samurai::getContainer();
        $Container->registerComponent('Token', new Samurai_Container_Def(array('class'=>'Filter_Token_Ticket')));
        $this->Token = $Container->getComponent('Token');
        //ErrorList
        $this->ErrorList = $this->ActionChain->getCurrentErrorList();
    }


    /**
     * Tokenコンポーネントの初期化
     *
     * @access     private
     */
    private function _initToken()
    {
        if($this->getAttribute('name')){
            $this->Token->setName($this->getAttribute('name'));
        }
        //モード設定
        if($this->getAttribute('mode')){
            $this->_modes = explode(',', $this->getAttribute('mode'));
            array_walk($this->_modes, 'trim');
        } else {
            $this->_modes = array('build');
        }
    }
}

