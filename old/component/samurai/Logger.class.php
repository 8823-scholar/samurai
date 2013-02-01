<?php
/**
 * PHP version 5.
 *
 * Copyright (c) Samurai Framework Project, All rights reserved.
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
 * @copyright  Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * Samurai用のロガークラス
 *
 * が、実際のロギング処理は担当しない
 * あくまでもロガークライアントへのトリガークラスです
 *
 * <code>
 *     $define = array(
 *         'enable' => true,
 *         'client' => 'SimpleFile',
 *         'log_level' => 'debug'
 *     );
 *     Samurai_Logger::addClient('alias', $define);
 *     Samurai_Logger::error('message for error or logging');
 * </code>
 * 
 * @package    Samurai
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Logger
{
    /**
     * client保持
     *
     * @access   private
     * @var      array
     */
    private static $_clients = array();

    /**
     * メッセージ格納
     *
     * @access   private
     * @var      array
     */
    private static $_messages = array();


    /**
     * コンストラクタ
     *
     * @access     private
     */
    private function __construct()
    {
        
    }





    /**
     * clientrの追加
     * aliasさえ調節すれば、いくらでも登録可能
     *
     * @access     public
     * @param      string  $alias    登録名
     * @param      array   $define   設定情報
     */
    public static function addClient($alias, $define)
    {
        self::$_clients[$alias] = self::createClient($define['client'], $define);
    }


    /**
     * clientの作成
     *
     * @param      string  $client_name   クライアント名
     * @param      array   $define        その他の宣言内容
     * @return     object
     */
    public static function createClient($client_name, array $define = array())
    {
        $class_name = sprintf('Samurai_Logger_Client_%s', ucfirst($client_name));
        try {
            Samurai_Loader::loadByClass($class_name);
            return new $class_name($define);
        } catch(Samurai_Exception $E){
            throw new Samurai_Exception("Not found logger client -> {$client_name}");
        }
    }





    /**
     * ロギングトリガー
     *
     * @access     public
     * @param      string  $level     ログレベル
     * @param      string  $message   メッセージ
     * @param      array   $binds     メッセージに埋め込みたいもの
     * @param      string  $file      ファイル名
     * @param      int     $line      実行ライン
     */
    public static function trigger($level, $message, $binds, $file = NULL, $line = NULL)
    {
        //メッセージの埋め込み
        $message = self::_bindMessage($message, $binds);
        //情報の補完
        if($file === NULL || $line === NULL){
            $backtrace = self::_getBacktraceInfo();
            if($file === NULL) $file = $backtrace['file'];
            if($line === NULL) $line = $backtrace['line'];
        }
        //クライアントの起動
        foreach(self::$_clients as $Client){
            if($Client->isEnable() && $Client->validLevel($level)){
                $Client->trigger($level, $message, $file, $line);
            }
        }
        self::$_messages[] = array('level' => $level, 'message' => $message, 'time' => microtime(true));
        //ERROR以上はスクリプトの停止
        if(in_array($level, array('fatal', 'error'))){
            exit;
        }
    }

    /**
     * fatal
     *
     * @access     public
     * @param      string  $message   表示メッセージ
     * @param      array   $binds     メッセージに埋め込みたいもの
     */
    public static function fatal($message, $binds=array())
    {
        $backtrace = self::_getBacktraceInfo();
        self::trigger('fatal', $message, $binds, $backtrace['file'], $backtrace['line']);
    }

    /**
     * error
     *
     * @access     public
     * @param      string  $message   表示メッセージ
     * @param      array   $binds     メッセージに埋め込みたいもの
     */
    public static function error($message, $binds=array())
    {
        $backtrace = self::_getBacktraceInfo();
        self::trigger('error', $message, $binds, $backtrace['file'], $backtrace['line']);
    }

    /**
     * warn
     *
     * @access     public
     * @param      string  $message   表示メッセージ
     * @param      array   $binds     メッセージに埋め込みたいもの
     */
    public static function warn($message, $binds=array())
    {
        $backtrace = self::_getBacktraceInfo();
        self::trigger('warn', $message, $binds, $backtrace['file'], $backtrace['line']);
    }

    /**
     * info
     *
     * @access     public
     * @param      string  $message   表示メッセージ
     * @param      array   $binds     メッセージに埋め込みたいもの
     */
    public static function info($message, $binds=array())
    {
        $backtrace = self::_getBacktraceInfo();
        self::trigger('info', $message, $binds, $backtrace['file'], $backtrace['line']);
    }

    /**
     * debug
     *
     * @access     public
     * @param      string  $message   表示メッセージ
     * @param      array   $binds     メッセージに埋め込みたいもの
     */
    public static function debug($message, $binds=array())
    {
        $backtrace = self::_getBacktraceInfo();
        self::trigger('debug', $message, $binds, $backtrace['file'], $backtrace['line']);
    }





    /**
     * sprintfへのブリッジ
     *
     * @access     private
     * @param      string  $message   メッセージ本文
     * @param      array   $args      埋め込み候補
     */
    private static function _bindMessage($message, $args=array())
    {
        if(!is_array($args)) $args = array($args);
        array_unshift($args, $message);
        return call_user_func_array('sprintf', $args);
    }


    /**
     * バックトレースの情報を取得する
     *
     * @access     public
     * @return     array
     */
    public static function _getBacktraceInfo()
    {
        $backtrace = debug_backtrace();
        $backtrace = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0] ;
        return $backtrace;
    }


    /**
     * プールされたメッセージを返却
     *
     * @access     public
     * @return     array
     */
    public static function getMessages()
    {
        return self::$_messages;
    }
}

