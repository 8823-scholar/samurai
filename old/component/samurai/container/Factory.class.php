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
 * DIContainerを管理するクラス
 *
 * DIContainer自体はsingleton機能を保持していないが、このFactoryクラスを利用すれば、
 * singleton的に扱うことが可能。
 * さらに、Containerに登録名をつけることができるので、複数のContainerを共存させる
 * ことも可能。
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Container_Factory
{
    /**
     * コンテナを格納
     *
     * @access   private
     * @var      array
     */
    private static $_containers = array();


    /**
     * コンストラクタ
     *
     * @access     private
     */
    private function __construct()
    {
        
    }





    /**
     * コンテナーを作成
     *
     * 既に作成されている場合は、既存のものを返却
     * が、それを無視することも可能
     *
     * @access     public
     * @param      string  $name        登録名
     * @param      boolean $force_new   既に登録されていても無理やりnewするか
     * @return     object
     */
    public static function create($name, $force_new = false)
    {
        if(!isset(self::$_containers[$name]) || $force_new){
            $container_class = Samurai_Config::get('container.class');
            if($container_class){
                Samurai_Loader::loadByClass($container_class);
                self::$_containers[$name] = new $container_class();
            } else {
                throw new Samurai_Exception('un inited Samurai.');
            }
        }
        return self::$_containers[$name];
    }
}

