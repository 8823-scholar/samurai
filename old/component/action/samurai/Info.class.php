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
 * Samurai情報を表示するためのAction
 * 
 * @package    Samurai
 * @subpackage Action.Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Samurai_Info extends Samurai_Action
{
    /**
     * 情報
     *
     * @access   private
     * @var      array
     */
    private $_info = array();


    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        //情報の設定
        $this->_setInfo();
        //cli
        if($this->Device->isCli()){
            $this->_output4Cli();
        }
        //web
        else {
            if(Samurai_Config::get('enable.samuraiinfo')){
                $this->_output4Web();
            }
        }
        exit;
    }


    /**
     * 情報の設定
     *
     * @access     private
     */
    private function _setInfo()
    {
        //SamuraiFW
        $this->_info['SamuraiFW'] = array(
            'version'=>Samurai::VERSION,
            'samurai_dirs' => Samurai::getSamuraiDirs(),
        );
        //Config
        $this->_info['Samurai_Config'] = Samurai_Config::getAll();
    }


    /**
     * cli用出力
     *
     * @access     private
     */
    private function _output4Cli()
    {
        //情報出力
        foreach($this->_info as $heading => $info){
            $this->_show($info, $heading);
        }
    }


    /**
     * web用出力
     *
     * @access     private
     */
    private function _output4Web()
    {
        Samurai_Loader::load(Samurai_Config::get('directory.library') . '/dBug/dBug.php');
        //ヘッダー出力
        $this->_printHeader();
        //情報出力
        foreach($this->_info as $heading => $info){
            $this->_show($info, $heading);
        }
        //フッター出力
        $this->_printFooter();
    }


    /**
     * ヘッダーを出力する
     *
     * @access     private
     */
    private function _printHeader()
    {
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
        echo '<HTML lang="ja-JP">';
        echo '<HEAD>';
        echo '<META http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        echo '<LINK rel="stylesheet" type="text/css" href="/samurai/samurai.css">';
        echo '<TITLE>Samurai Info - [Samurai Framework]</TITLE>';
        echo '</HEAD>';
        echo '<BODY>';
    }


    /**
     * フッターを出力する
     *
     * @access     private
     */
    private function _printFooter()
    {
        echo '</BODY>';
        echo '</HTML>';
    }


    /**
     * 出力
     *
     * @access     private
     * @param      mixed   $contents   内容
     * @param      string  $heading    見出し
     * @param      int     $depth      深度
     */
    private function _show($contents, $heading = NULL, $depth = 0)
    {
        //cli
        if($this->Device->isCli()){
            for($i = 0; $i <= $depth; $i++) echo ' ';
            if($heading !== NULL){
                echo "{$heading} : ";
            }
            if(is_array($contents) || is_object($contents)){
                echo "\n";
                foreach($contents as $_key => $_val){
                    $this->_show($_val, $_key, $depth + 3);
                }
            } else {
                echo $contents;
            }
            echo "\n";
        }
        //web
        else {
            if($heading !== NULL){
                echo "<H1>{$heading}</H1>";
            }
            $dBug = new dBug($contents);
        }
    }
}

