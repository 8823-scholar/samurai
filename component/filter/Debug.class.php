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

Samurai_Loader::loadByClass('Filter_Debug_Debugger');

/**
 * デバッグ情報を表示するFilter
 *
 * dBugクラスなどを使用してデバッグ情報の表示をおこなう
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Debug extends Samurai_Filter
{
    /**
     * デバッガ
     *
     * @access   private
     * @var      array
     */
    private $_debuggers = array();

    /**
     * デバッグ判断
     *
     * @access   private
     * @var      boolean
     */
    private $_is_debug = false;

    /**
     * エラーが発生しているかどうか
     *
     * @access   private
     * @var      boolean
     */
    private $_has_error = false;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * Responseコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Response;

    /**
     * Cookieコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Cookie;

    /**
     * Sessionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Session;

    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;

    /**
     * FilterChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $FilterChain;


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        foreach($this->getAttributes() as $_key => $_val){
            switch($_key){
                case 'ip':
                case 'agent':
                case 'cookie':
                    $this->{'_isDebugFrom'.ucfirst(strtolower($_key))}($_val);
                    break;
            }
            if($this->_is_debug) break;
        }
        //debugging = 'all'やtrueの場合はdebugｴﾗｰの格納
        if($this->getAttribute('debugging') === 'all'
            || (!$this->_is_debug && $this->getAttribute('debugging'))){
            $ErrorList = $this->ActionChain->getCurrentErrorList();
            $ErrorList->setType(Samurai_Config::get('error.debug'));
        }
    }


    /**
     * IPからデバッグ判断
     *
     * @access     private
     * @param      array    $ip_list   IP一覧
     */
    private function _isDebugFromIp($ip_list)
    {
        foreach((array)$ip_list as $ip){
            $ip = preg_replace('/\*/', '.*?', $ip);
            if(preg_match("/{$ip}/", $this->Device->ip)){
                $this->_is_debug = true;
                Samurai_Logger::debug('[Filter_Debug] Auth success by ip. -> %s', $this->Device->ip);
                return;
            }
        }
    }

    /**
     * USER-AGENTからデバッグ判断
     *
     * @access     private
     * @param      array    $agent   user-agent一覧
     */
    private function _isDebugFromAgent($agent)
    {
        foreach((array)$agent as $_val){
            $_val = preg_quote($_val, '/');
            if(preg_match("/{$_val}/", $this->Request->getHeader('User-Agent'))){
                $this->_is_debug = true;
                Samurai_Logger::debug('[Filter_Debug] Auth success by user-agent. -> %s', $this->Request->getHeader('User-Agent'));
                return;
            }
        }
    }

    /**
     * Cookieからデバッグ判断
     *
     * @access     private
     * @param      array    $cookie   識別クッキー一覧
     */
    private function _isDebugFromCookie($cookie)
    {
        if(!is_object($this->Cookie)) return;
        foreach((array)$cookie as $_val){
            if($this->Cookie->getParameter($_val)){
                $this->_is_debug = true;
                Samurai_Logger::debug('[Filter_Debug] Auth success by cookie -> %s', $this->Cookie->getParameter($_val));
                return;
            }
        }
    }





    /**
     * @override
     */
    protected function _postfilter()
    {
        parent::_postfilter();
        if(!$this->_showWindow()) return;
        //デバッガーのセットアップ
        $this->_setupDebuggers();
        //モバイル拡張
        if($this->Device->isMobile()){
            $this->_adjust4Mobile();
        }
        //表示
        $this->_print();
    }



    /**
     * デバッグウインドウを表示させるかどうか
     *
     * @access     private
     * @return     boolean
     */
    private function _showWindow()
    {
        if(!$this->_is_debug) return false;
        if(!$this->ActionChain->isLast()) return false;
        //明示的にデバッグコンソールを表示したくない場合、動作させない
        if($this->getAttribute('debug_console') === false) return false;
        if(Samurai_Config::get('filter.debug.debug_console') === false) return false;
        if($this->Request->getParameter('debug_console') !== NULL){
            if(!$this->Request->getParameter('debug_console')){
                if($this->Cookie) $this->Cookie->setParameter('SAMURAI.Filter.Debug.console', '0');
                return false;
            } else {
                if($this->Cookie) $this->Cookie->setParameter('SAMURAI.Filter.Debug.console', '1');
            }
        }
        if(is_object($this->Cookie) && $this->Cookie->getParameter('SAMURAI.Filter.Debug.console') === '0'){
            return false;
        }
        return true;
    }


    /**
     * デバッガのセットアップ
     *
     * @access     private
     */
    private function _setupDebuggers()
    {
        foreach((array)$this->getAttribute('debugger') as $_key => $debugger){
            if(isset($this->_debuggers[$debugger])) continue;
            $Debugger = $this->getDebbuger($debugger);
            $Debugger->setup();
            $Debugger->setIndex($_key);
            $this->_debuggers[$debugger] = $Debugger;
            if($Debugger->hasError()) $this->_has_error = true;
        }
    }


    /**
     * デバッガの取得
     *
     * @access     public
     * @param      string  $debugger   デバッガ名
     * @return     object  Filter_Debug_Debugger
     */
    public function getDebbuger($debugger)
    {
        $class = sprintf('Filter_Debug_Debugger_%s', ucfirst($debugger));
        $Debugger = Samurai::getContainer()->getComponentByDef($class, new Samurai_Container_Def(array('class'=>$class)));
        return $Debugger;
    }


    /**
     * 表示する
     *
     * @access     private
     */
    private function _print()
    {
        Samurai_Loader::load(Samurai_Config::get('directory.library').'/dBug/dBug.php');
        ob_start();
        $this->_printCss();
        $this->_printHeader();
        $this->_printMenu();
        $this->_printFooter();
        $this->_printContent();
        $contents = ob_get_contents();
        ob_end_clean();
        if(Samurai_Config::get('encoding.output') != Samurai_Config::get('encoding.internal')){
            $contents = mb_convert_encoding($contents, Samurai_Config::get('encoding.output'), Samurai_Config::get('encoding.internal'));
        }
        echo $contents;
    }


    /**
     * ヘッダーを表示する
     *
     * @access     private
     */
    private function _printHeader()
    {
        echo '<DIV id="samurai_debug_console">';
        echo sprintf('<DIV id="samurai_debug_console_title" class="%s">', $this->_has_error ? 'error' : 'info');
        echo '<DIV style="float:left;">SamuraiFW</DIV>';
        echo '<DIV style="float:right;">';
        foreach($this->_debuggers as $Debugger){
            if($Debugger->position == 'top'){
                echo sprintf(' <IMG src="%s" class="icon" style="%s" onClick="%s">',
                                $Debugger->icon, '', $Debugger->onClick());
            }
        }
        echo sprintf(' <IMG src="/samurai/close.gif" class="icon" onClick="%s">',
                        "document.getElementById('samurai_debug_console').style.display='none';");
        echo '</DIV>';
        echo '<DIV style="clear:both;"></DIV>';
        echo '</DIV>';
    }


    /**
     * メニューを表示する
     *
     * @access     private
     */
    private function _printMenu()
    {
        echo sprintf('<DIV id="samurai_debug_console_menu">');
        foreach($this->_debuggers as $Debugger){
            if($Debugger->position == 'menu'){
                echo sprintf('<DIV id="samurai_debug_console_menu_item" class="%s" onMouseOver="%s" onMouseOut="%s" onClick="%s">%s</DIV>',
                                $Debugger->hasError() ? 'error' : 'item',
                                "this.style.color='#FFFFFF';",
                                "this.style.color='#333333';",
                                $Debugger->onClick(),
                                $Debugger->icon);
            }
        }
        echo '</DIV>';
    }


    /**
     * フッターを表示する
     *
     * @access     private
     */
    private function _printFooter()
    {
        foreach($this->_debuggers as $Debugger){
            if($content = $Debugger->getFooterContent()){
                echo '<DIV class="samurai_debug_console_footer_item">';
                echo $content;
                echo '</DIV>';
            }
        }
        echo '<DIV id="samurai_debug_console_footer">';
        echo '&copy; Samurai Framework Project';
        echo '</DIV>';
        echo '</DIV>';
    }


    /**
     * CSSを表示する
     *
     * @access     private
     */
    private function _printCss()
    {
        //CSS
        echo '<STYLE type="text/css">';
        echo file_get_contents(dirname(__FILE__).'/debug/debug.css');
        echo '</STYLE>';
        //JS
        echo '<SCRIPT type="text/javascript">';
        echo file_get_contents(dirname(__FILE__).'/debug/debug.js');
        echo '</SCRIPT>';
    }


    /**
     * 内容を表示する
     *
     * @access     private
     */
    private function _printContent()
    {
        foreach($this->_debuggers as $Debugger){
            echo $Debugger->getContent();
        }
    }


    /**
     * 携帯用に画面幅などを調節する
     *
     * @access     private
     */
    private function _adjust4Mobile()
    {
        $width = $this->Device->getDisplayX();
        echo '<STYLE type="text/css">';
        echo 'BODY { ';
        echo "    width : {$width}px;";
        echo '    border : 1px solid #CCCCCC;';
        echo '    margin : 5px !important;';
        echo '}';
        echo '</STYLE>';
    }
}

