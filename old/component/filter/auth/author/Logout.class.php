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
 * ログアウトのみおこなうAuthor
 *
 * 認証情報を保持・継続の仕方は、なるべく各Authorで共通の方法にしておくこと。
 * このAuthorで共通でログアウトできるようになります。
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Auth_Author_Logout extends Filter_Auth_Author
{
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
     * @implements
     */
    public function authorize(array $params)
    {
        //初期化
        $name = 'AUTH_DB';
        foreach($params as $_key => $_val){
            switch($_key){
                case 'name':
                    $$_key = trim((string)$_val); break;
            }
        }
        //情報のクリア
        $this->_clearInfoFromCookie($name);
        $this->_clearInfoFromSession($name);
        return true;
    }


    /**
     * セッションの値を削除する
     *
     * @access     protected
     * @param      string  $namespace   認証名前空間
     */
    protected function _clearInfoFromSession($namespace)
    {
        $this->Session->delParameter('SAMURAI_FILTER_AUTH.' . $namespace);
    }


    /**
     * クッキーの情報を消去する
     *
     * @access     protected
     * @param      string  $namespace   認証名前空間
     */
    protected function _clearInfoFromCookie($namespace)
    {
        $this->Cookie->delParameter('SAMURAI_FILTER_AUTH.' . $namespace);
    }
}

