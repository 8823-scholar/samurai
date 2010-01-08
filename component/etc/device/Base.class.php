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
 * デバイスクラスの基本
 *
 * すべての拡張デバイスクラスは、このクラスを継承している。
 * 
 * @package    Samurai
 * @subpackage Etc.Device
 * @copyright  2009-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Device_Base
{
    /**
     * user_agent
     *
     * @access   public
     * @var      string
     */
    public $user_agent = '';

    /**
     * user_agentを分解したもの
     *
     * @access   public
     * @var      array
     */
    public $user_agents = array();

    /**
     * クライアント種類
     *
     * @access   public
     * @var      string
     */
    public $client = 'pc';

    /**
     * ブラウザ名
     *
     * @access   public
     * @var      string
     */
    public $browser_name = '';

    /**
     * ブラウザタイプ(xhtml|html)
     *
     * @access   public
     * @var      string
     */
    public $browser_type = 'xhtml';

    /**
     * 画面サイズ(xga|svga|qvga|qqvga)
     *
     * @access   public
     * @var      string
     */
    public $display_type = 'svga';

    /**
     * 画面カラー数
     *
     * @access   public
     * @var      int
     */
    public $display_color = 16777216;

    /**
     * 画面横幅
     *
     * @access   public
     * @var      int
     */
    public $display_x = 800;

    /**
     * 画面縦幅
     *
     * @access   public
     * @var      int
     */
    public $display_y = 600;

    /**
     * IPアドレス
     *
     * @access   public
     * @var      string
     */
    public $ip = '';

    /**
     * 個人特定文字列
     *
     * @access   public
     * @var      string
     */
    public $serial = '';


    /**
     * コンストラクタ
     *
     * @access    public
     * @param     string  $user_agent   user_agent
     */
    public function __construct($user_agent)
    {
        $this->user_agent = $user_agent;
        $this->user_agents = preg_split('/[\s\/\-\(\);]/', $this->user_agent);
        switch(true){
            case !$this->user_agent && php_sapi_name() == 'cli':
                $this->client = 'cli';
                break;
            case $this->user_agents[0] == 'DoCoMo':
                $this->client = 'imode';
                break;
            case $this->user_agents[0] == 'KDDI' || $this->user_agents[0] == 'UP.Browser':
                $this->client = 'ezweb';
                break;
            case $this->user_agents[0] == 'SoftBank' || $this->user_agents[0] == 'Vodafone'
                    || $this->user_agents[0] == 'J-PHONE' || $this->user_agents[0] == 'Semulator':
                $this->client = 'softbank';
                break;
            case isset($this->user_agents[2]) && $this->user_agents[2] == 'WILLCOM':
                $this->client = 'willcom';
                break;
        }
        $this->ip = $this->getIP();
    }


    /**
     * ユーザーエージェントの取得
     *
     * @access     public
     * @return     string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }


    /**
     * clientの取得
     *
     * @access     public
     * @return     string
     */
    public function getClient()
    {
        return $this->client;
    }


    /**
     * ブラウザ名の取得
     *
     * @access     public
     * @return     string
     */
    public function getBrowserName()
    {
        return $this->browser_name;
    }


    /**
     * browser_typeの取得
     *
     * @access     public
     * @return     string
     */
    public function getBrowserType()
    {
        return $this->browser_type;
    }


    /**
     * 画面サイズの取得
     *
     * @access     public
     * @return     string
     */
    public function getDisplayType()
    {
        return $this->display_type;
    }


    /**
     * 画面カラー数の取得
     *
     * @access     public
     * @return     int
     */
    public function getDisplayColor()
    {
        return $this->display_color;
    }


    /**
     * 画面横幅の取得
     *
     * @access     public
     * @return     int
     */
    public function getDisplayX()
    {
        return $this->display_x;
    }

    /**
     * 画面縦幅の取得
     *
     * @access     public
     * @return     int
     */
    public function getDisplayY()
    {
        return $this->display_y;
    }


    /**
     * IPアドレスを取得する
     *
     * @access     public
     * @return     string
     */
    public function getIP()
    {
        if($this->ip != ''){
            return $this->ip;
        } elseif(isset($_SERVER['REMOTE_ADDR'])){
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }


    /**
     * 個人特定IDの取得
     * IPアドレスや、UID等
     *
     * @access     public
     * @return     string
     */
    public function getSerial()
    {
        return $this->serial;
    }



    /**
     * 携帯かどうかの判断
     *
     * @access     public
     * @return     boolean
     */
    public function isMobile()
    {
        $mobiles = array('imode', 'ezweb', 'softbank', 'emobile', 'willcom');
        return in_array($this->client, $mobiles);
    }


    /**
     * cliかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function isCli()
    {
        return $this->client === 'cli';
    }
}

