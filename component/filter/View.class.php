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
 * 表示を担当するFilter
 *
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_View extends Filter_ViewSimple
{
    /**
     * Rendererコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Renderer;

    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;


    /**
     * Template:
     *
     * @access     protected
     */
    protected function _doTemplate()
    {
        if($this->_do_value){
            //Actionを登録
            $this->Renderer->setAction($this->ActionChain->getCurrentAction());
            $this->Renderer->setErrorList($this->ActionChain->getCurrentErrorList());
            //Parameter関連
            if($this->Request){
                $this->Renderer->setRequest($this->Request);
                $this->Renderer->assign('protocol', $this->Request->isHttps() ? 'https' : 'http');
            }
            if($this->Cookie)  $this->Renderer->setCookie($this->Cookie);
            if($this->Session) $this->Renderer->setSession($this->Session);
            $this->Renderer->setServer($_SERVER);
            $this->Renderer->setScriptName($_SERVER['SCRIPT_NAME']);
            //Token
            if($this->Token) $this->Renderer->setToken($this->Token);
            //表示
            $body = $this->Renderer->render($this->_do_value);
            if(Samurai_Config::get('encoding.output') != Samurai_Config::get('encoding.internal')){
                $body = mb_convert_encoding($body, Samurai_Config::get('encoding.output'), Samurai_Config::get('encoding.internal'));
            }
            $Body = $this->Response->setBody($body);
            if(is_object($Body) && !$this->Response->hasHeader('content-type')){
                $encoding = Samurai_Config::get('encoding.output');
                $encoding = $encoding == 'SJIS-WIN' ? 'Shift_JIS' : $encoding;
                $mime_type = $this->Device->isMobile() && $this->Device->isDocomo() ? 'application/xhml+xml' : 'text/html' ;
                $Body->setHeader('content-type', sprintf('%s; charset=%s', $mime_type, $encoding));
            }
            $this->Response->execute();
        }
    }
}

