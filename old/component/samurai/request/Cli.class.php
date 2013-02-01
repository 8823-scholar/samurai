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

Samurai_Loader::loadByClass('Samurai_Request_Http');

/**
 * Client用のRequestクラス
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Request_Cli extends Samurai_Request_Http
{
    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        $request = array();
        if(isset($_SERVER['argv'])){
            //第一引数は実行ファイルが入ってくる
            $script = array_shift($_SERVER['argv']);
            //その他引数の解釈
            foreach($_SERVER['argv'] as $arg){
                //ロングオプションの場合
                if(preg_match('/^\-\-(..*)/', $arg, $matches)){
                    $option = $matches[1];
                    $option = explode('=', $option);
                    $option_key   = array_shift($option);
                    $option_value = ($option) ? array_shift($option) : true ;
                    $request[$option_key] = $option_value;
                //オプションの場合
                } elseif(preg_match('/^\-(..*)/', $arg, $matches)){
                    $option = $matches[1];
                    for($i=0;isset($option[$i]);$i++){
                        $request['options'][$option[$i]] = true;
                    }
                //それ以外は「args」の中に放り込む
                } else {
                    $request['args'][] = $arg;
                }
            }
        }
        //格納
        $this->import($request);
    }


    /**
     * @override
     */
    public function getMethod()
    {
        return php_sapi_name();
    }


    /**
     * cliではActionの上書きはなし
     *
     * @override
     */
    public function dispatchAction()
    {
        return false;
    }
}

