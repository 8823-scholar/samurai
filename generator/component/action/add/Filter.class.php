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
 * Filterを生成する
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Add_Filter extends Generator_Action
{
    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        //Usage
        if($this->_isUsage() || !$this->args) return 'usage';
        //入力チェック
        if(!$this->_checkInput()) return 'usage';
        //ループ(複数指定する事が可能)
        while($filter_name = array_shift($this->args)){
            $filter_name = strtolower($filter_name);
            $filter_file = $this->_addFilter($filter_name);
        }
    }


    /**
     * 入力チェック
     *
     * @access     private
     * @return     boolean
     */
    private function _checkInput()
    {
        //フィルター名のチェック
        foreach($this->args as $filter_name){
            if(!preg_match('/^[a-zA-Z][a-zA-Z0-9_]+?$/', $filter_name)){
                $this->ErrorList->add('filter_name', "{$filter_name} -> Filter's name is Invalid. ([a-zA-Z0-9_])");
            }
        }
        return !$this->ErrorList->isExists();
    }


    /**
     * フィルターを追加する
     *
     * @access     private
     * @param      string  $filter_name   フィルター名
     * @param      array   $params        Rendererに渡される値
     */
    private function _addFilter($filter_name, $params = array())
    {
        //Skeletonの決定
        $skeleton = $this->Generator->getSkeleton();
        //Generate
        list($result, $filter_file) = $this->Generator->generate($filter_name, $skeleton, $params);
        //成功
        if($result == $this->Generator->RESULT_SUCCESS){
            $this->_sendMessage("{$filter_name} -> Successfully generated. [{$filter_file}]");
        //既にある
        } elseif($result == $this->Generator->RESULT_ALREADY){
            $this->_sendMessage("{$filter_name} -> Already exists. [{$filter_file}] -> skip");
        } else {
            $this->_sendMessage("{$filter_name} -> Failed.");
        }
        return $filter_file;
    }
}

