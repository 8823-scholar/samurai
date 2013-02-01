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
 * 携帯デバイスクラスの基本
 *
 * すべての携帯デバイスクラスは、このクラスを継承している。
 * 
 * @package    Samurai
 * @subpackage Etc.Device
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Device_Mobile extends Etc_Device_Base
{
    /**
     * キャリア
     *
     * @access   public
     * @var      string
     */
    public $carrier = 'unknown';

    /**
     * UID
     *
     * @access   public
     * @var      string
     */
    public $uid = '';

    /**
     * @override
     */
    public $display_x = 240;

    /**
     * @override
     */
    public $display_y = 360;


    /**
     * コンストラクタ
     *
     * @access    public
     * @param     string  $user_agent   user_agent
     */
    public function __construct($user_agent)
    {
        parent::__construct($user_agent);
    }


    /**
     * UIDを取得する
     *
     * @access     public
     * @return     string
     */
    public function getUid()
    {
        return $this->uid;
    }


    /**
     * Softbankかどうかを判断
     *
     * @access     public
     * @return     boolean
     */
    public function isSoftbank()
    {
        return $this->carrier == 'softbank';
    }


    /**
     * Imodeかどうかを判断
     *
     * @access     public
     * @return     boolean
     */
    public function isImode()
    {
        return $this->carrier == 'imode';
    }

    /**
     * isImodeのシノニム
     *
     * @access     public
     * @return     boolean
     */
    public function isDocomo()
    {
        return $this->isImode();
    }


    /**
     * Ezwebかどうかを判断
     *
     * @access     public
     * @return     boolean
     */
    public function isEzweb()
    {
        return $this->carrier == 'ezweb';
    }

    /**
     * isEzwebのシノニム
     *
     * @access     public
     * @return     boolean
     */
    public function isAu()
    {
        return $this->isEzweb();
    }
}

