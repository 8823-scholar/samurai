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
 * PHPのエラーハンダラーをロガーにブリッジします
 *
 * PHPのエラーが発生すると、Samurai_Logger::triggerがコールされるようになります
 * 特殊ロガー
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Logger_Client_PhpErrorHandler extends Samurai_Logger_Client
{
    /**
     * ログレベル - error
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_ERROR = 1;

    /**
     * ログレベル - warning
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_WARNING = 2;

    /**
     * ログレベル - parse
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_PARSE = 4;

    /**
     * ログレベル - notice
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_NOTICE = 8;

    /**
     * ログレベル - core error
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_CORE_ERROR = 16;

    /**
     * ログレベル - core warning
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_CORE_WARNING = 32;

    /**
     * ログレベル - compile error
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_COMPILE_ERROR = 64;

    /**
     * ログレベル - compile warning
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_COMPILE_WARNING = 128;

    /**
     * ログレベル - user error
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_USER_ERROR = 256;

    /**
     * ログレベル - user warning
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_USER_WARNING = 512;

    /**
     * ログレベル - user notice
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_USER_NOTICE = 1024;

    /**
     * ログレベル - strict
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_STRICT = 2048;

    /**
     * ログレベル - recoverable error
     *
     * @access   public
     * @const    int
     */
    public $LOG_LEVEL_RECOVERABLE_ERROR = 4096;

    /**
     * fatalに属するエラー
     *
     * @access   private
     * @var      array
     */
    private $_fatal = array('error', 'core_error', 'core_warning', 'compile_error', 'compile_warning');

    /**
     * errorに属するエラー
     *
     * @access   private
     * @var      array
     */
    private $_error = array('warning', 'parse', 'user_error', 'user_warning');

    /**
     * warnに属するエラー
     *
     * @access   private
     * @var      array
     */
    private $_warn = array('notice', 'user_notice');

    /**
     * infoに属するエラー
     *
     * @access   private
     * @var      array
     */
    private $_info = array('strict');

    /**
     * debugに属するエラー
     *
     * @access   private
     * @var      array
     */
    private $_debug = array('recoverable_error');


    /**
     * コンストラクタ
     *
     * @access     public
     * @param      array    $define
     */
    public function __construct(array $define = array())
    {
        parent::__construct($define);
        if($this->enable){
            //set_error_handler
            set_error_handler(array($this, 'trigger'));
            //通常のログトリガーで起動しないように
            $this->enable = false;
        }
    }





    /**
     * @override
     */
    public function define(array $define)
    {
        parent::define($define);
        foreach($define as $_key => $_val){
            switch($_key){
                case 'fatal':
                case 'error':
                case 'warn':
                case 'info':
                case 'debug':
                    $_key = "_{$_key}";
                    $this->$_key = (array)$_val;
                    break;
            }
        }
    }


    /**
     * @implements
     */
    public function trigger($level_int, $message, $file, $line)
    {
        if($this->validLevel($level_int)){
            $level = $this->_logLevelInt2Str($level_int);
            Samurai_Logger::trigger($this->_toLoggerLevel($level), '[php error][%s] %s', array($level, $message), $file, $line);
            return false;
        } else {
            return true;
        }
    }


    /**
     * レベルの有効判断
     *
     * @access     public
     * @param      int      $log_level
     * @see        Samurai_Logger_Client::validLevel
     */
    public function validLevel($log_level)
    {
        if(error_reporting() === 0) return false;
        if((error_reporting() & $log_level) === 0) return false;
        return true;
    }


    /**
     * PHPのエラーレベルをSamurai_Logger上のエラーレベルに置き換える
     * noticeもwarnに置き換えられる
     *
     * @access     private
     * @param      string   $log_level
     * @return     string
     */
    private function _toLoggerLevel($log_level)
    {
        $level = 'warn';
        if(in_array($log_level, $this->_fatal)){
            $level = 'fatal';
        } elseif(in_array($log_level, $this->_error)){
            $level = 'error';
        } elseif(in_array($log_level, $this->_warn)){
            $level = 'warn';
        } elseif(in_array($log_level, $this->_info)){
            $level = 'info';
        } elseif(in_array($log_level, $this->_debug)){
            $level = 'debug';
        }
        return $level;
    }
}

