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
 * generatorアクションの抽象クラス
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Generator_Action extends Samurai_Action
{
    /**
     * 引数
     *
     * @access   public
     * @var      array
     */
    public $args = array();

    /**
     * オプション
     *
     * @access   public
     * @var      array
     */
    public $options = array();

    /**
     * コマンド
     *
     * @access   public
     * @var      string
     */
    public $command = '';

    /**
     * HOMEディレクトリ
     *
     * @access   public
     * @var      string
     */
    public $dir_home = '';

    /**
     * Samuraiディレクトリ
     *
     * @access   public
     * @var      string
     */
    public $dir_samurai = '';

    /**
     * Generatorコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Generator;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }


    /**
     * 初期化
     *
     * @access     protected
     */
    protected function _init()
    {
        $now_dir = getcwd();
        $samurai_dir = $this->_searchSamuraiDir($now_dir);
        if($samurai_dir && is_dir($samurai_dir)){
            $this->dir_samurai = $samurai_dir;
            $this->dir_home = dirname($samurai_dir);
        } else {
            $this->dir_samurai = $now_dir . DS . 'Samurai';
            $this->dir_home = $now_dir;
        }
    }


    /**
     * samurai_dirの検索
     *
     * 現在のディレクトリから遡って、.samuraiファイルのあるディレクトリをsamurai_dirとする。
     *
     * @access     protected
     * @param      string  $dir_now   現在のディレクトリ
     * @return     string
     */
    protected function _searchSamuraiDir($dir_now)
    {
        $dirs = explode(DS, $dir_now);
        $samurai_dir = array_shift($dirs);
        foreach($dirs as $dir){
            $samurai_dir .= DS . $dir;
            $dot_samurai = $samurai_dir . DS . '.samurai';
            if(file_exists($dot_samurai)){
                return $samurai_dir;
            }
        }
        return false;
    }



    /**
     * メッセージを投射する
     *
     * クライアントにダイレクトに出力する。
     *
     * @access     protected
     * @param      string  $message   メッセージ
     * @param      string  $crlf      CRLF
     */
    protected function _sendMessage($message, $crlf = "\n")
    {
        echo $message . $crlf;
        flush();
    }


    /**
     * Yes/Noの簡単な確認を行う
     *
     * 正確には、選択肢に対応した値が返却される
     *
     * @access     protected
     * @param      string  $message   メッセージ
     * @param      array   $choices   選択肢
     * @param      array   $values    選択肢の値
     * @param      string  $default   デフォルトの解答
     * @return     string
     */
    protected function _confirm($message, $choices = array('y','n'), $values = array(true,false), $default = 'n')
    {
        $message = sprintf('%s [%s]: ', $message, join('/', $choices));
        $this->_sendMessage($message, "");
        //ユーザー入力待ち
        $answer = trim(fgets(STDIN));
        //回答の値を取得
        if(!in_array($answer, $choices)) $answer = $default;
        $choices = array_combine($choices, $values);
        return $choices[$answer];
    }


    /**
     * Usageオプションがついているかどうかの判断
     *
     * @access     protected
     * @return     boolean
     */
    protected function _isUsage()
    {
        //--help or -[hH] or --usage or -[uU]
        foreach($this->Request->getParameters() as $option_key => $option_value){
            if($option_key == 'help' || $option_key == 'usage'){
                return true;
            } elseif($option_key=='options'){
                foreach($option_value as $option => $_temp){
                    if(in_array($option, array('h','H','u','U'))){
                        return true;
                    }
                }
            }
        }
        return false;
    }


    /**
     * Versionオプションがついているかどうかの判断
     *
     * @access     protected
     * @return     boolean
     */
    protected function _isVersion()
    {
        //--version or -[vV]
        foreach($this->Request->getParameters() as $option_key => $option_value){
            if($option_key == 'version'){
                return true;
            } elseif($option_key == 'options'){
                foreach($option_value as $option => $_temp){
                    if(in_array($option, array('v','V'))){
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

