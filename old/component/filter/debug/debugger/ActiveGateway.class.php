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
 * ActiveGatewayデバッガ
 *
 * ActiveGAtewayの内容をデバッグウインドウに反映する。
 *
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Debug_Debugger_ActiveGateway extends Filter_Debug_Debugger
{
    /**
     * @override
     */
    public $position = 'menu';
    public $icon = 'ActiveGateway';
    public $heading = 'ActiveGateway';

    /**
     * 全てのクエリー
     *
     * @access   private
     * @var      array
     */
    private $_queries = array();


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * @implements
     */
    public function setup()
    {
        if(class_exists('ActiveGatewayManager')){
            $this->_queries = ActiveGatewayManager::getPoolQuery();
        }
        foreach($this->_queries as $_key => $query){
            $query['error'] = $query['time'] > 0.5;
            $query['image'] = sprintf('<IMG src="/samurai/%s">', $query['error'] ? 'error.gif' : 'info.gif');
            $query['time'] = $query['time'] * 1000;
            $this->_queries[$_key] = $query;
            if($query['error']) $this->_has_error = true;
        }
    }


    /**
     * @override
     */
    protected function _getContent()
    {
        $content = array();
        $content[] = '<TABLE class="list">';
        $content[] = '<TR><TH style="width:25px;">#</TH><TH style="width:25px;">　</TH><TH style="width:50px;">ms</TH><TH>query</TH></TR>';
        foreach($this->_queries as $_key => $query){
            $class = $query['error'] ? 'error' : 'item' ;
            $content[] = sprintf('<TR class="%s"><TD>%d</TD><TD style="text-align:center;">%s</TD><TD>+%0.1f</TD><TD>%s</TD></TR>',
                                    $class, $_key, $query['image'], $query['time'], htmlspecialchars($query['query']));
        }
        $content[] = '</TABLE>';
        return join("\r\n", $content);
    }
}

