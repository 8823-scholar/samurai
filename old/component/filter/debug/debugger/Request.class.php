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
 * Requestデバッガ
 *
 * Request関連コンポーネントの内容をデバッグウインドウに反映する。
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Debug_Debugger_Request extends Filter_Debug_Debugger
{
    /**
     * @override
     */
    public $position = 'menu';
    public $icon = 'Request';
    public $heading = 'Request';

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * Sessionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Session;

    /**
     * Cookieコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Cookie;

    /**
     * Fileコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $File;


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
        $no = '(none)';
        $this->_content = array();
        //$_REQUEST
        $this->_content['$_REQUEST'] = $this->Request->getParameters();
        //$_COOKIE
        $var = $this->Cookie ? $this->Cookie->getParameters() : $no ;
        $this->_content['$_COOKIE'] = $var;
        //$_SESSION
        $var = $this->Session ? $this->Session->getParameters() : $no ;
        $this->_content['$_SESSION'] = $var;
        //$_FILES
        $var = $this->File ? $this->File->getFiles() : $no ;
        $this->_content['$_FILES'] = $var;
        //$_SERVER
        $var = (isset($_SERVER) && $_SERVER) ? $_SERVER : $no ;
        $this->_content['$_SERVER'] = $var;
    }
}

