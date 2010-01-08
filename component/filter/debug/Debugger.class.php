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
 * Filter_Debugで使用されるデバッガの抽象クラス
 *
 * すべてのデバッガはこれを継承する
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Filter_Debug_Debugger
{
    /**
     * 表示場所(menu|top)
     *
     * @access   public
     * @var      string
     */
    public $position = 'menu';

    /**
     * 表示文字列 | アイコン画像
     *
     * @access   public
     * @var      string
     */
    public $icon = 'Debugger';

    /**
     * 見出し
     *
     * @access   public
     * @var      string
     */
    public $heading = 'Debugger';

    /**
     * デバッガーの一意のインデックス
     *
     * @access   protected
     * @var      int
     */
    protected $_index = 0;

    /**
     * 表示内容
     *
     * @access   protected
     * @var      mixed
     */
    protected $_content;

    /**
     * エラーが発生しているかどうか
     *
     * @access   protected
     * @var      boolean
     */
    protected $_has_error = false;

    /**
     * 内容をエスケープするかどうか
     *
     * @access   protected
     * @var      boolean
     */
    protected $_escape = true;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * セットアップ
     *
     * @access     public
     */
    public abstract function setup();


    /**
     * 内容の取得
     *
     * @access     public
     * @return     string
     */
    public function getContent()
    {
        $contents = array();
        $contents[] = $this->_getHeader();
        $contents[] = $this->_getContent();
        $contents[] = $this->_getFooter();
        return join("\r\n", $contents);
    }


    /**
     * フッターコンテンツの取得
     *
     * @access     public
     * @return     string
     */
    public function getFooterContent()
    {
        return '';
    }


    /**
     * ヘッダーの取得
     *
     * @access     protected
     * @return     string
     */
    protected function _getHeader()
    {
        $header = '<DIV id="samurai_debug_console_window_' . $this->_index . '" class="samurai_debug_console_window">';
        $header .= '<DIV class="header">';
        $header .= '<DIV style="float:left;">' . $this->heading . '</DIV>';
        $header .= '<DIV style="float:right;">'
                    .'<IMG src="/samurai/close.gif" class="icon" style="cursor:pointer;" onClick="' . $this->onClick() . '"></DIV>';
        $header .= '<DIV style="clear:both;"></DIV>';
        $header .= '</DIV>';
        $header .= '<DIV class="content">';
        return $header;
    }


    /**
     * 内容の取得
     *
     * @access     private
     * @return     string
     */
    protected function _getContent()
    {
        ob_start();
        dBug::$no_escape = !$this->_escape;
        new dBug($this->_content);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }


    /**
     * フッターの取得
     *
     * @access     protected
     * @return     string
     */
    protected function _getFooter()
    {
        $footer = '</DIV>';
        $footer .= '</DIV>';
        return $footer;
    }


    /**
     * onClick時の挙動
     *
     * @access     public
     * @return     string
     */
    public function onClick()
    {
        return "SamuraiDebug.swapWindow('samurai_debug_console_window_{$this->_index}');";
    }


    /**
     * indexをセット
     *
     * @access     public
     * @param      int     $index   index
     */
    public function setIndex($index)
    {
        $this->_index = $index;
    }


    /**
     * エラーが発生しているかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function hasError()
    {
        return $this->_has_error;
    }
}

