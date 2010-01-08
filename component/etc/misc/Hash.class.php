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
 * 暗号化のためのクラス
 * 
 * @package    Samurai
 * @subpackage Etc.Misc
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Misc_Hash
{
    /**
     * Blowfishインスタンス
     *
     * @access   private
     * @var      object
     */
    private $Blowfish;


    /**
     * 独自md5
     *
     * 単純にmd5するのではなく、複合語としてmd5化する。
     * 複合される言葉は「misc.hash.key」で指定できる。
     *
     * @param      string  $string   暗号化対象文字列
     * @return     string
     */
    function md5($string, $seed = '我思う故に我在り')
    {
        $seed = Samurai_Config::get('misc.hash.seed', $seed);
        return md5($string.$seed);
    }


    /**
     * 独自blowfish暗号化
     * 複合される言葉は「misc.hash.key」で指定できる。
     *
     * @param      string  $string   暗号化したい文字列
     * @return     string
     */
    function bfEncrypt($string, $seed = '我思う故に我在り')
    {
        if(!$this->Blowfish){
            require_once 'Crypt/Blowfish.php';
            $this->Blowfish = new Crypt_Blowfish(Samurai_Config::get('misc.hash.seed', $seed));
        }
        $encrypted = $this->Blowfish->encrypt(trim($string));
        $encrypted = bin2hex($encrypted);
        return trim($encrypted);
    }


    /**
     * 独自blowfish復号化
     * 複合される言葉は「misc.hash.key」で指定できる。
     *
     * @param      string  $string   復号化したい文字列
     * @return     string
     */
    function bfDecrypt($string, $seed = '我思う故に我在り')
    {
        if(!$this->Blowfish){
            require_once 'Crypt/Blowfish.php';
            $this->Blowfish = new Crypt_Blowfish(Samurai_Config::get('misc.hash.seed', $seed));
        }
        $decrypted = $this->Blowfish->decrypt(pack('H*',trim($string)));
        return trim($decrypted);
    }
}

