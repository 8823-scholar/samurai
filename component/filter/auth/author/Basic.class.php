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
 * BASIC認証による認証をかけるAuthor
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Auth_Author_Basic extends Filter_Auth_Author
{
    /**
     * Responseコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Response;


    /**
     * @implements
     */
    public function authorize(array $params)
    {
        //初期化
        $name  = 'AUTH_HTTP';
        $title = 'Please, Enter the ID & Password.';
        $users = array();
        $error_message = '認証に失敗しました。';
        foreach($params as $_key => $_val){
            switch($_key){
                case 'name':
                case 'title':
                case 'error_message':
                    $$_key = (string)$_val; break;
                case 'users':
                    $$_key = (array)$_val; break;
            }
        }
        
        //認証
        if(!isset($_SERVER['PHP_AUTH_USER'])){
            return $this->_basicAuthorize($title, $error_message);
        } else {
            foreach($users as $name => $pass){
                if($name == $_SERVER['PHP_AUTH_USER']){
                    if($this->_authorize($pass, $_SERVER['PHP_AUTH_PW'])){
                        return true;
                    }
                }
            }
        }
        return $this->_basicAuthorize($title, $error_message);
    }


    /**
     * 実際にBASIC認証を表示する
     *
     * @access     private
     * @param      string  $title           タイトル文
     * @param      string  $error_message   エラー文字列
     * @return     string  認証結果
     */
    private function _basicAuthorize($title, $error_message)
    {
        $this->Response->setStatus(401);
        $this->Response->setHeader('WWW-Authenticate', "Basic realm='{$title}'");
        return array(Samurai_Config::get('error.auth'), $error_message);
    }


    /**
     * パスワードと入力されたパスワードが一致するかチェック
     *
     * @access     private
     * @param      string  $pass    パスワード
     * @param      string  $input   入力されたパスワード
     * @param      boolean 結果
     */
    private function _authorize($pass, $input)
    {
        $hash = 'raw';
        if(strpos($pass, ':')){
            list($hash, $pass) = explode(':', $pass);
        }
        switch($hash){
            case 'md5':
                $input = md5($input);
                break;
            case 'sha1':
                $input = sha1($input);
                break;
        }
        return $pass == $input;
    }
}

