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
 * ファイルへ単純に書き出します
 *
 * ローテーションはサイズで行います
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Logger_Client_SimpleFile extends Samurai_Logger_Client
{
    /**
     * ログファイル
     *
     * @access   public
     * @var      string
     */
    public $logfile = 'log/logger_simple_file.log';

    /**
     * ログファイルの最大サイズ
     *
     * @access   public
     * @var      int
     */
    public $maxsize = 512000000;





    /**
     * @override
     */
    public function define(array $define)
    {
        parent::define($define);
        foreach($define as $_key => $_val){
            switch($_key){
                case 'maxsize':
                    $this->$_key = (int)$_val;
                    break;
                case 'logfile':
                    $this->$_key = (string)$_val;
                    break;
            }
        }
    }


    /**
     * @implements
     */
    public function trigger($level, $message, $file, $line)
    {
        $_logfile = $this->_setLogfile();
        $this->_output($this->logfile, array("[{$level}]", $message, "{$file}(line{$line})"));
        if($this->_isSizeover($this->logfile, $this->maxsize)){
            $this->_backup($this->logfile);
        }
        $this->_setLogfile($_logfile);
    }


    /**
     * ファイルに出力する
     *
     * @access     private
     * @param      string  $logfile    ログファイル
     * @param      array   $messages   メッセージ配列
     */
    private function _output($logfile, array $messages = array())
    {
        //メッセージの補完
        array_unshift($messages, date('Y/m/d H:i:s'));
        array_push($messages, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        array_push($messages, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        array_push($messages, isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
        foreach($messages as &$message) $message = preg_replace("/[\r\n\t]+/", ' ', $message);
        //出力
        $handle = fopen($logfile, 'a+');
        fwrite($handle, join("\t", $messages)."\r\n");
        fclose($handle);
        @chmod($logfile, 0777);
    }


    /**
     * ログファイルがサイズオーバーしていないかチェック
     *
     * @access     private
     * @param      string  $logfile   ログファイル
     * @param      int     $maxsize   最大サイズ
     * @return     boolean
     */
    private function _isSizeover($logfile, $maxsize)
    {
        return filesize($logfile) >= $maxsize;
    }


    /**
     * ログファイルをバックアップします
     *
     * @access     private
     * @param      string  $logfile   バックアップ対象ファイル
     */
    private function _backup($logfile)
    {
        $backup = $logfile . '.' . date('YmdHis');
        copy($logfile, $backup);
        @chmod($backup, 0777);
        file_put_contents($logfile, '');
    }


    /**
     * Samurai_Configでsamrai_dirに追加があった場合に対処できるように
     *
     * @access     private
     * @param      string  $logfile
     * @return     string  変更される前のlogfile
     */
    private function _setLogfile($logfile = NULL)
    {
        if($logfile){
            $this->logfile = $logfile;
            return $this->logfile;
        } else {
            $_logfile = $this->logfile;
            $log_dir = Samurai_Loader::getPath(dirname($this->logfile));
            $this->logfile = $log_dir . DS . basename($this->logfile);
            return $_logfile;
        }
    }
}

