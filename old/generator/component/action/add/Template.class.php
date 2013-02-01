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
 * Templateを生成する
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Add_Template extends Generator_Action
{
    /**
     * CLIテンプレートオプション
     *
     * @access   public
     * @var      boolean
     */
    public $cli = false;


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
        //ループ(テンプレートを複数指定する事が可能)
        while($template_name = array_shift($this->args)){
            //Templateの追加
            $template_name = strtolower($template_name);
            if($this->cli){
                $template_file = $this->_addTemplate($template_name, $this->Generator->BROWSER_CLI);
            } else {
                $template_file = $this->_addTemplate($template_name, $this->Generator->BROWSER_WEB);
            }
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
        //テンプレート名のチェック
        foreach($this->args as $index => $template_name){
            if(!preg_match('/^[a-zA-Z0-9_\/\.]*?$/', $template_name)){
                $this->ErrorList->add('template_name', "{$template_name} -> Template's name is Invalid. ([a-zA-Z0-9_\\/\\.])");
            } elseif(!preg_match('/\.\w+$/', $template_name)){
                $this->args[$index] = $template_name . '.' . Samurai_Config::get('generator.renderer.suffix', 'tpl');
            }
        }
        return !$this->ErrorList->isExists();
    }


    /**
     * テンプレートをを追加する
     *
     * @access     private
     * @param      string  $template_name   テンプレート名
     * @param      int     $browser_type    ブラウザタイプ
     * @param      array   $params          Rendererに渡される値
     */
    private function _addTemplate($template_name, $browser_type, $params = array())
    {
        //Skeletonの決定
        if($browser_type == $this->Generator->BROWSER_CLI){
            $skeleton = 'SKELETON_CLI_' . strtoupper(Samurai_Config::get('generator.renderer.name', 'smarty'));
            $skeleton = $this->Generator->getSkeleton($this->Generator->$skeleton);
        } else {
            $skeleton = 'SKELETON_WEB_' . strtoupper(Samurai_Config::get('generator.renderer.name', 'smarty'));
            $skeleton = $this->Generator->getSkeleton($this->Generator->$skeleton);
        }
        //Generate
        list($result, $template_file) = $this->Generator->generate($template_name, $skeleton, $params);
        //成功
        if($result == $this->Generator->RESULT_SUCCESS){
            $this->_sendMessage("{$template_name} -> Successfully generated. [{$template_file}]");
        //既にある
        } elseif($result == $this->Generator->RESULT_ALREADY){
            $this->_sendMessage("{$template_name} -> Already exists. [{$template_file}] -> skip");
        //無視された(template_dirがないなど)
        } elseif($result == $this->Generator->RESULT_IGNORE){
            
        } else {
            $this->_sendMessage("{$template_name} -> Failed.");
        }
        return $template_file;
    }
}

