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
 * マイグレートクラス
 *
 * Railsのぱくりです
 * 
 * @package    Samurai
 * @subpackage Migrate
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Migration
{
    /**
     * ActiveGateway
     *
     * @access   public
     * @var      object
     */
    public $AG;

    /**
     * ActiveGatewayの接続名
     *
     * @access   public
     * @var      string
     */
    public $dsn = 'base';

    /**
     * 実行時間
     *
     * @access   public
     * @var      float
     */
    public $time = 0.0;

    /**
     * 実行後メッセージ
     *
     * @access   public
     * @var      string
     */
    public $message = '';


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * 初期化メソッド
     * 実行前に呼ばれる
     *
     * @access     public
     */
    public function setup()
    {
        $this->AG = ActiveGatewayManager::getActiveGateway($this->dsn);
    }


    /**
     * up処理
     * バージョンアップの際に呼ばれる
     *
     * @access     public
     */
    public function up()
    {
        $this->message = 'Nothing to do...';
    }


    /**
     * down処理
     * バージョンダウンの際に呼ばれる
     *
     * @access     public
     */
    public function down()
    {
        $this->message = 'Nothing to do...';
    }





    /**
     * migrate開始
     *
     * @access     public
     */
    public function start()
    {
        $this->time = microtime(true);
    }

    /**
     * 経過時間取得
     *
     * @access     public
     * @return     float
     */
    public function getTime()
    {
        return round(microtime(true) - $this->time, 4);
    }
}

