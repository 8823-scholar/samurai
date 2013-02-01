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
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * インスタンスの保持および管理を行うクラス
 *
 * 単体ではsingletonを保障しません
 * Samurai_Container_Factoryなどを使用することによってsingletonが保障されます
 *
 * インスタンスを保持するだけではなく、様々な初期化方法やオートインジェクションなど、
 * Samurai Frameworkにとって重要な役割を果たしている
 * 
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @see         Samurai_Container_Factory
 */
class Samurai_Container
{
    /**
     * コンポーネントを格納
     *
     * @access   private
     * @var      array
     */
    private $_components = array();


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }





    /**
     * コンポーネントの登録
     * インスタンスを直接登録することも可能だが、通常はSamurai_Container_Defを使用する
     *
     * @access     public
     * @param      string  $name   登録名
     * @param      object  $Def    コンポーネント宣言(実コンポーネントも可能)
     */
    public function registerComponent($name, $Def)
    {
        $this->_components[$name] = $Def;
    }


    /**
     * コンポーネントの取得
     *
     * @access     public
     * @param      string  $name   登録名
     * @return     object
     */
    public function getComponent($name)
    {
        if(isset($this->_components[$name])){
            $Component = $this->_components[$name];
            if(is_object($Component) && $Component instanceof Samurai_Container_Def){
                return $this->getComponentByDef($name, $Component);
            } else {
                return $Component;
            }
        } else {
            throw new Samurai_Exception("Component is not found... -> {$name}");
        }
    }


    /**
     * 全てのコンポーネントを取得する
     *
     * @access     public
     * @return     array
     */
    public function getComponents()
    {
        return $this->_components;
    }


    /**
     * コンポーネントを宣言から取得する
     *
     * @access     public
     * @param      string  $name   登録名
     * @param      object  $Def    コンポーネント宣言
     * @return     object
     */
    public function getComponentByDef($name, Samurai_Container_Def $Def)
    {
        //登録されていない場合は登録
        if(!$this->hasComponent($name)) $this->registerComponent($name, $Def);
        //実体化
        $Component = $this->_def2Instance($Def);
        //Singleton
        if($Def->instance == 'singleton'){
            $this->_components[$name] = $Component;
        }
        //依存性注入
        $this->injectDependency($Component, $Def);
        return $Component;
    }


    /**
     * Defからインスタンスを生成する
     *
     * @access     private
     * @param      object  $ComponentDef   コンポーネント宣言
     * @return     object
     */
    private function _def2Instance(Samurai_Container_Def $Def)
    {
        //実体化(FactoryやgetInstanceなど)
        if(preg_match('/^[\w_]+::[\w_]+$/i', $Def->class)){
            list($class, $method) = explode('::', $Def->class);
            if(!class_exists($class, false)){
                $Def->path == '' ? Samurai_Loader::loadByClass($class) : Samurai_Loader::load($Def->path) ;
            }
            $script = sprintf('%s::%s(%s)', $class, $method, $this->_array2ArgsWithInjectDependency('$Def->args', $Def->args));
        //実体化(new)
        } else {
            $class = $Def->class;
            if(!class_exists($class, false)){
                $Def->path == '' ? Samurai_Loader::loadByClass($class) : Samurai_Loader::load($Def->path) ;
            }
            $script = sprintf('new %s(%s)', $class, $this->_array2ArgsWithInjectDependency('$Def->args', $Def->args));
        }
        //実体化
        if(!class_exists($class, false)) throw new Samurai_Exception(sprintf('Class %s is not found', $class));
        $script = sprintf('$Component = %s;', $script);
        eval($script);
        //初期化メソッド
        if($Def->initMethod){
            $args = $Def->initMethod['args'];
            $script = sprintf('$Component->%s(%s);', $Def->initMethod['name'], $this->_array2ArgsWithInjectDependency('$args', $Def->initMethod['args']));
            eval($script);
        }
        Samurai_Logger::debug('Def to Entity success -> %s', array($class));
        return $Component;
    }


    /**
     * 依存性注入
     *
     * @access     public
     * @param      object  $Component   コンポーネント
     * @param      object  $Def         コンポーネント宣言
     */
    public function injectDependency($Component, $Def=NULL)
    {
        if($Def === NULL) $Def = new Samurai_Container_Def();
        //オートインジェクション
        foreach($Component as $_key => $_val){
            if((($Def->rule == 'AllowAll' && !in_array($_key, $Def->deny))
                || ($Def->rule == 'DenyAll' && in_array($_key, $Def->allow))) && $this->hasComponent($_key)){
                $Component->$_key = $this->getComponent($_key);
            }
        }
        //セッターインジェクション
        foreach($Def->setter as $_key => $_val){
            if(is_string($_val) && preg_match('/^\$([\w_]+)$/', $_val, $matches)){
                $_val = $this->getComponent($matches[1]);
            }
            $setter = 'set' . ucfirst($_key);
            if(method_exists($Component, $setter)){
                $Component->$setter($_val);
            } else {
                $Component->$_key = $_val;
            }
        }
    }


    /**
     * publicなメンバへの単純なアサイン
     *
     * @access     public
     * @param      object  $Component    コンポーネント
     * @param      array   $attributes   上書対象
     */
    public function injectAttributes($Component, array $attributes)
    {
        $vars = get_object_vars($Component);
        foreach($attributes as $_key => $_val){
            if(!preg_match('/^_/', $_key) && array_key_exists($_key, $vars)){
                $Component->$_key = $_val;
            }
        }
    }





    /**
     * Container設定ファイルからコンポーネント宣言情報をインポートする
     * 
     * <code>
     *     @import:
     *         path : "foo/bar/zoo.dicon"
     * </code>
     *
     * @access     public
     * @param      string  $dicon_file   Container設定ファイル
     */
    public function import($dicon_file)
    {
        $dicon = Samurai_Yaml::load($dicon_file);
        foreach($dicon as $name => $define){
            //importコマンド
            if(preg_match('/^@import/', $name)){
                if(isset($define['path'])){
                    $this->import($define['path']);
                }
            //登録
            } else {
                $this->registerComponent($name, new Samurai_Container_Def($define));
            }
        }
    }


    /**
     * コンポーネント宣言を取得する
     *
     * @access     public
     * @return     object  Samurai_Container_Def
     */
    public function getDef()
    {
        return new Samurai_Container_Def();
    }

    /**
     * getDefのシノニム
     *
     * @access     public
     */
    public function getContainerDef()
    {
        return $this->getDef();
    }





    /**
     * そのコンポーネント(名前)が既に登録されているかどうかを調べる
     *
     * @access     public
     * @param      string  $name   コンポーネント名
     * @return     boolean
     */
    public function hasComponent($name)
    {
        return isset($this->_components[$name]);
    }


    /**
     * 配列を関数へ渡す引数形式に変換する
     *
     * @access     private
     * @param      string  その空間での配列名
     * @param      array   $array   配列
     * @return     string
     */
    private function _array2ArgsWithInjectDependency($name, array $array = array())
    {
        $args = array();
        foreach($array as $_key => $_val){
            if(is_string($_val) && preg_match('/^\$([\w_]+)$/', $_val, $matches)){
                $args[] = sprintf('$this->getComponent("%s")', $matches[1]);
            } else {
                $args[] = is_numeric($_key) ? sprintf('%s[%s]', $name, $_key) : sprintf("%s['%s']", $name, $_key) ;
            }
        }
        return join(', ', $args);
    }
}

