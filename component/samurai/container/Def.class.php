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
 *  インスタンスの初期化方法を宣言するクラス
 * 
 * @package    Samurai
 * @copyright  2007-2009 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Container_Def
{
    /**
     * インスタンの実体
     *
     * @access   private
     * @var      object
     */
    private $_instance;

    /**
     * alias名
     *
     * @access   public
     * @var      string
     */
    public $alias = '';

    /**
     * クラス名
     *
     * @access   public
     * @var      string
     */
    public $class = '';

    /**
     * ファイルパス
     *
     * @access   public
     * @var      string
     */
    public $path = '';

    /**
     * インスタンスを生成する際の引数
     *
     * @access   public
     * @var      array
     */
    public $args = array();

    /**
     * インスタンスタイプ(singleton|prototype)
     *
     * @access   public
     * @var      string
     */
    public $instance = 'singleton';

    /**
     * オートインジェクションのルール(AllowAll|DenyAll)
     *
     * @access   public
     * @var      string
     */
    public $rule = 'AllowAll';

    /**
     * インジェクション許可リスト(DenyAllより優先される)
     *
     * @access   public
     * @var      array
     */
    public $allow = array();

    /**
     * インジェクション拒否リスト(AllowAllより優先される)
     *
     * @access   public
     * @var      array
     */
    public $deny = array();

    /**
     * セッターインジェクション情報
     *
     * @access   public
     * @var      array
     */
    public $setter = array();

    /**
     * 初期化メソッドインジェクション情報
     *
     * @access   public
     * @var      array
     */
    public $initMethod = array();

    
    /**
     * コンストラクタ
     *
     * @access     public
     * @param      array    $define   初期化情報
     */
    public function __construct(array $define = array())
    {
        //割り当て
        foreach($define as $_key => $_val){
            switch($_key){
                case 'alias':
                case 'class':
                case 'path':
                case 'instance':
                case 'rule':
                    $this->$_key = (string)$_val;
                    break;
                case 'args':
                case 'allow':
                case 'deny':
                case 'setter':
                case 'initMethod':
                    $this->$_key = (array)$_val;
                    break;
            }
        }
        //情報の正規化
        $this->validate();
    }





    /**
     * 情報がvalidateされる
     * 不正な値はデフォルト値に書き換えられる
     *
     * @access     public
     */
    public function validate()
    {
        //instance
        if(!in_array($this->instance, array('singleton', 'prototype'))){
            $this->instance = 'singleton';
        }
        //rule
        if(!in_array($this->rule, array('AllowAll', 'DenyAll'))){
            $this->rule = 'AllowAll';
        }
        //initMethod
        if($this->initMethod){
            if(!isset($this->initMethod['name']) || !is_string($this->initMethod['name'])){
                $this->initMethod = array();
            } elseif(!is_array($this->initMethod['args'])){
                $this->initMethod['args'] = array();
            }
        }
    }
}

