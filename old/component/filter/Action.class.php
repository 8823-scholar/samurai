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
 * Actionの実行を準備および実行を行うFilter
 * 
 * @package     Samurai
 * @subpackage  Filter
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Action extends Samurai_Filter
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
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        //カレントのActionを取得
        $Action = $this->ActionChain->getCurrentAction();
        //Actionにオートインジェクション
        $Container = Samurai::getContainer();
        if(Samurai_Config::get('enable.request_injection')){
            $Container->injectAttributes($Action, $this->Request->getParameters());
        }
        $Container->injectDependency($Action, $this->_attributes2Def($this->getAttributes()));
        foreach($this->getAttributes() as $_key => $_val){
            if(is_string($_val) && preg_match('/^\$(.*+)/', $_val, $matches)){
                $Container->injectAttributes($Action, array($_key=>$Container->getComponent($matches[1])));
            }
        }
        $Action->ErrorList = $this->ActionChain->getCurrentErrorList();
        //Actionの実行
        $error_type = $this->ActionChain->getCurrentErrorList()->getType();
        $result = $this->ActionChain->executeAction($Action, $error_type);
        $this->ActionChain->setCurrentActionResult($result);
    }


    /**
     * Actionの設定内容から、Component_Defを生成
     *
     * @access     private
     * @param      array   $attributes   属性
     */
    private function _attributes2Def(array $attributes = array())
    {
        $define = array();
        foreach($attributes as $_key => $_val){
            switch($_key){
                case 'rule':
                case 'setter':
                case 'allow':
                case 'deny':
                    $define[$_key] = $_val;
                    break;
            }
        }
        return new Samurai_Container_Def($define);
    }
}

