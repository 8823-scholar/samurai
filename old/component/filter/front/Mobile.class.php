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
 * 携帯端末への対応フロントフィルター
 *
 * Filter_Front_Deviceフィルターの後に配置する必要があります。
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @see        Filter_Front_Device
 */
class Filter_Front_Mobile extends Samurai_Filter
{
    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;


    /**
     * @override
     */
    public function _prefilter()
    {
        parent::_prefilter();
        if($this->Device && $this->Device->isMobile()){
            Samurai_Config::set('action.prefix', $this->getAttribute('action.prefix', 'action4mobile'));
            Samurai_Config::set('directory.action', $this->getAttribute('directory.action', 'component/action4mobile'));
            Samurai_Config::set('directory.template', $this->getAttribute('directory.template', 'template4mobile'));
            Samurai_Config::set('encoding.input', $this->getAttribute('encoding.input', 'SJIS-WIN'));
            Samurai_Config::set('encoding.output', $this->getAttribute('encoding.output', 'SJIS-WIN'));
            $this->Request->import($this->Request->getParameters());
        }
    }
}

