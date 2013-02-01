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
 * フィルターを保持、実行するクラス
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_FilterChain
{
    /**
     * フィルターを保持
     *
     * @access   private
     * @var      array
     */
    private $_filters = array();

    /**
     * フィルターの配置を保持
     *
     * @access   private
     * @var      array
     */
    private $_position = array();

    /**
     * 現在の位置を保持
     *
     * @access   private
     * @var      int
     */
    private $_index = 0;


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * FilterChainを組み立てる
     *
     * @access    public
     * @param     object  $ConfigStack   Samurai_ConfigStack
     */
    public function build($ConfigStack)
    {
        foreach($ConfigStack->getConfig() as $alias => $value){
            $config = $ConfigStack->getConfig($alias);
            if(!$this->add($alias, $config['filter'], $config['attributes'])){
                Samurai_Logger::error('Filter add failed... -> %s', $alias);
            }
        }
    }


    /**
     * FilterChainを実行する
     *
     * @access    public
     */
    public function execute()
    {
        if($this->hasNext()){
            $name = $this->getCurrentFilterName();
            $Filter = $this->getFilterByName($name);
            if($Filter){
                $this->next();
                Samurai::getContainer()->injectDependency($Filter);
                $Filter->execute();
            } else {
                throw new Samurai_Exception('Filter error.');
            }
        }
    }





    /**
     * FilterChainの最後にFilterを追加
     *
     * @access     public
     * @param      string  $alias        登録名
     * @param      string  $name         Filter名
     * @param      array   $attributes   属性
     */
    public function add($alias, $name, array $attributes = array())
    {
        //Filterのクラス名チェック
        if(!preg_match('/^[\w_]+$/', $name)){
            Samurai_Logger::error('Invalid Filter\'s name. -> %s', $name);
        }
        //Filterの検索
        list($class, $file) = $this->makeNames($name);
        if(!Samurai_Loader::load($file)){
            throw new Samurai_Exception('Filter unknown. -> '.$file);
        }
        //既に同名のFilterが追加されている場合は無視
        if($this->hasFilter($alias)){
            Samurai_Logger::info('This Filter is already exists. -> %s', $alias);
            return true;
        }
        //生成
        else {
            $Filter = new $class();
            if(!is_object($Filter)){
                Samurai_Logger::fatal('Filter create failed... -> %s', $file);
            }
            $Filter->setAttributes($attributes);
            //追加
            $this->_filters[$alias] = $Filter;
            $this->_position[] = $alias;
            return true;
        }
    }


    /**
     * Filterクラス名および、ファイルパスの取得
     *
     * @access    public
     * @param     string  $name    Filter名
     * @return    array
     */
    public function makeNames($name)
    {
        $name = Samurai_Config::get('filter.prefix') . '_' . $name;
        $name = join('_', array_map('ucfirst', explode('_', $name)));
        $path = Samurai_Config::get('directory.component') . DS . Samurai_Loader::getPathByClass($name);
        return array($name, $path);
    }


    /**
     * FilterChainの長さを返却
     *
     * @access    public
     * @return    int
     */
    public function getSize()
    {
        return count($this->_filters);
    }


    /**
     * 現在のFilter名を返却
     *
     * @access    public
     * @return    string
     */
    public function getCurrentFilterName()
    {
        if(isset($this->_position[$this->_index])){
            return $this->_position[$this->_index];
        }
    }


    /**
     * 指定された名前のFilterを返却
     *
     * @access     public
     * @param      string  $name    登録名
     * @return     object
     */
    public function getFilterByName($name)
    {
        if($this->hasFilter($name)){
            return $this->_filters[$name];
        }
    }


    /**
     * 既にフィルターがあるかチェック
     *
     * @access     public
     * @param      string  $alias   登録名
     * @return     boolean
     */
    public function hasFilter($alias)
    {
        return isset($this->_filters[$alias]) && is_object($this->_filters[$alias]);
    }


    /**
     * 次のフィルタがあるかどうか
     *
     * @access     public
     * @return     boolean
     */
    public function hasNext()
    {
        return $this->_index < $this->getSize();
    }


    /**
     * FilterChainを次へ進める
     *
     * @access     public
     */
    public function next()
    {
        $this->_index++;
    }


    /**
     * FilterChainをクリア
     *
     * @access    public
     */
    public function clear()
    {
        $this->_filters = array();
        $this->_position = array();
        $this->_index = 0;
    }
}

