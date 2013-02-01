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
 * ロガークライアントの抽象クラス
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Logger_Client
{
    /**
     * このロガーが有効かどうか
     *
     * @access   public
     * @var      boolean
     */
    public $enable = true;

    /**
     * ロガークライアント名
     *
     * @access   public
     * @var      string
     */
    public $client = '';

    /**
     * ログレベル(default=LOG_LEVEL_WARN)
     *
     * @access   public
     * @var      int
     */
    public $log_level = 3;

    /**
     * ログレベル - fatal
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_FATAL = 5;

    /**
     * ログレベル - error
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_ERROR = 4;

    /**
     * ログレベル - warn
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_WARN  = 3;

    /**
     * ログレベル - info
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_INFO  = 2;

    /**
     * ログレベル - debug
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_DEBUG = 1;


    /**
     * コンストラクタ
     *
     * @access     public
     * @param      array    $define   初期化情報
     */
    public function __construct(array $define = array())
    {
        $this->define($define);
    }


    /**
     * 宣言内容をセットする
     *
     * @access     public
     * @param      array   $define   宣言内容
     */
    public function define(array $define)
    {
        foreach($define as $_key => $_val){
            switch($_key){
                case 'enable':
                    $this->$_key = (bool)$_val;
                    break;
                case 'client':
                    $this->$_key = (string)$_val;
                    break;
                case 'log_level':
                    $this->log_level = $this->_logLevelStr2Int($_val);
                    break;
            }
        }
    }





    /**
     * トリガー
     * 実際にはこのメソッドが、Samurai_Loggerによって起動される
     *
     * @access     public
     * @param      int      $level
     * @param      string   $message
     * @param      string   $file
     * @param      int      $line
     */
    abstract public function trigger($level, $message, $file, $line);





    /**
     * このクライアントが現在有効かどうかのチェック
     *
     * @access     public
     * @return     boolean
     */
    public function isEnable()
    {
        return $this->enable;
    }


    /**
     * この渡されたログレベルがクライアントによって有効な値かどうか
     * また動作すべきかどうか
     *
     * @access     public
     * @param      string  $log_level   ログレベル(文字列)
     * @return     boolean
     */
    public function validLevel($log_level)
    {
        $level = $this->_logLevelStr2Int($log_level);
        return $level >= $this->log_level;
    }


    /**
     * ログレベルの文字列から、定数で定義されている数値に変換する
     *
     * @access     protected
     * @param      string  $log_level
     * @return     int
     */
    protected function _logLevelStr2Int($log_level)
    {
        $_key = 'LOG_LEVEL_' . strtoupper($log_level);
        return isset($this->$_key) ? $this->$_key : NULL ;
    }


    /**
     * ログレベルの数値から、定数名の一部を変換する
     *
     * @access     protected
     * @param      int     $log_level
     * @return     string
     */
    protected function _logLevelInt2Str($log_level)
    {
        foreach(get_object_vars($this) as $_key => $_val){
            if(preg_match('/^LOG_LEVEL_([\w_]+)/', $_key, $matches)){
                if($_val == $log_level) return strtolower($matches[1]);
            }
        }
        return NULL;
    }
}

