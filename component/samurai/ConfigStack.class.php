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
 * 設定ファイルの解析、及び内容を保持するクラス
 *
 * ここで言う設定ファイルとはActionに付随する、各種samurai.ymlなどです
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_ConfigStack
{
    /**
     * 設定情報
     *
     * @access   private
     * @var      array
     */
    private $_config = array();

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;

    /**
     * Utilityコンポーネント
     *
     * @access   public
     * @var      object
     */
    public function $Utility;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public function $Request;


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * 実行開始
     *
     * 設定ファイルを順次読み込む。優先順位は以下の通り
     * Action: foo_bar_zooの場合
     *    samurai.yml -> foo/samurai.yml -> foo/bar/samurai.yml -> foo/bar/zoo.yml
     *
     * @access    public
     */
    public function execute()
    {
        $action_name = $this->ActionChain->getCurrentActionName();
        $path_list = explode('_', $action_name);
        //設定ファイルの読み込み
        $current_path = Samurai_Config::get('directory.action');
        foreach($path_list as $i => $path){
            //globalファイル
            $this->import(sprintf('%s%s%s', $current_path, DS, Samurai_Config::get('action.config_file')));
            //localファイル
            if($i == count($path_list)-1){
                $this->import(sprintf('%s%s%s.yml', $current_path, DS, $path));
            }
            $current_path = sprintf('%s%s%s', $current_path, DS, $path);
        }
    }


    /**
     * 指定された設定ファイルを読み込む
     *
     * @access     public
     * @param      string  $config_file   設定ファイルを読み込みたいパス
     * @param      boolean $level         アクションと同階層かどうか
     */
    public function import($config_file, $level = false)
    {
        //設定ファイルの読み込み
        try {
            $config = Samurai_Yaml::load($config_file);
            Samurai_Logger::debug('ConfigStack loaded. -> %s', $config_file);
        } catch(Samurai_Exception $E){
            if($level){
                $config = array();
            } else {
                return false;
            }
        }
        //Actionには実行する順序があるのでプールしておいて、それ以外のものをセットする
        $action_key = '';
        $action_sections = array();
        foreach($config as $section => $_val){
            $sections = $this->_parseSection($section);
            //Methodフィルタリング
            if($this->_validMethod($sections['method'])){
                if(preg_match('/^Action$/i', $sections['filter'])){
                    $action_key = $section;
                    $action_sections = $sections;
                } else {
                    $this->addConfig($section, (array)$_val);
                }
            }
        }
        //ActionのみはセクションがなくてもディフォルトのActionを設定
        //さらに事前にActionが設定されていた場合は最後尾へと移動
        if($level !== NULL){
            if($action_key){
                $action_sections['type'] = 'move';
                $this->addConfig($this->_reverseSection($action_sections), (array)$config[$action_key]);
            } else {
                $this->addConfig('Action@move', array());
            }
        }
    }


    /**
     * 指定されたセクションの設定情報を追加する
     *
     * @access     private
     * @param      string  $section       セクション名
     * @param      array   $attributes    設定情報
     */
    private function addConfig($section, array $attributes = array())
    {
        $info = $this->_parseSection($section);
        if($info['type'] == 'delete'){
            if(isset($this->_config[$info['alias']])) unset($this->_config[$info['alias']]);
        } else {
            $_method = '_preDo4' . ucfirst($info['type']);
            $this->$_method($info, $attributes);
            //追加(マージ)
            if(isset($this->_config[$info['alias']])){
                $attributes = $this->Utility->array_merge($this->_config[$info['alias']]['attributes'], $attributes);
                $this->_config[$info['alias']] = array('filter' => $info['filter'], 'attributes' => $attributes);
            } else {
                $this->_config[$info['alias']] = array('filter' => $info['filter'], 'attributes' => $attributes);
            }
        }
    }


    /**
     * 位置はそのままで、内容もマージ
     *
     * @access     private
     * @param      array   $info    セクション情報
     */
    private function _preDo4Merge(&$info)
    {
        //何もしない
    }

    /**
     * 同じフィルターでも違うAlias名にして後ろに追加する
     *
     * @access     private
     * @param      array   $info    セクション情報
     */
    private function _preDo4Push(&$info)
    {
        //エイリアスの自動調節
        do {
            $exists = false;
            foreach($this->_config as $alias => $_val){
                if($info['alias'] == $alias){
                    $exists = true;
                    $info['alias'] .= '__pushed__';
                    Samurai_Logger::debug('push型においてaliasの衝突がありました。 -> %s -> %s', array($alias, $info['alias']));
                }
            }
        } while($exists);
    }

    /**
     * 位置はそのままで、内容を上書
     *
     * @access     private
     * @param      array   $info    セクション情報
     */
    private function _preDo4Override(&$info)
    {
        if(isset($this->_config[$info['alias']])){
            $this->_config[$info['alias']]['attributes'] = array();
        }
    }

    /**
     * 位置は移動、内容は上書
     *
     * @access     private
     * @param      array   $info    セクション情報
     */
    private function _preDo4Clear(&$info)
    {
        if(isset($this->_config[$info['alias']])){
            unset($this->_config[$info['alias']]);
        }
    }
    /**
     * 位置を移動、内容はマージ
     *
     * @access     private
     * @param      array   $info   セクション情報
     */
    private function _preDo4Move(&$info)
    {
        if(isset($this->_config[$info['alias']])){
            $attributes = $this->_config[$info['alias']];
            unset($this->_config[$info['alias']]);
            $this->_config[$info['alias']] = $attributes;
        }
    }





    /**
     * 指定のコンフィグの解析を行う
     * [Filter]:[METHOD]:[ALIAS]@[TYPE]
     *
     * @access     private
     * @param      string  $section   セクション名
     * @return     array
     */
    private function _parseSection($section)
    {
        //セクション部分と、タイプ部分とに分離
        @list($section, $type) = explode('@', $section);
        if(empty($type)) $type = 'merge';
        //セクション部分の分離
        @list($filter, $method, $alias) = explode(':', $section);
        if(empty($method)) $method = 'BOTH';
        if(empty($alias))  $alias  = $filter;
        return array(
            'alias'  => $alias,
            'filter' => $filter,
            'method' => $method,
            'type'   => strtolower($type)
        );
    }

    /**
     * 解析されたコンフィグ情報から、再びセクションの生成
     *
     * @access     private
     * @param      array   $sections   セクション解析情報
     * @return     string
     */
    private function _reverseSection($sections)
    {
        $section = sprintf('%s:%s:%s@%s', $sections['filter'], $sections['method'], $sections['alias'], $sections['type']);
        return $section;
    }


    /**
     * 有効なmethodかチェック
     *
     * @access     private
     * @return     boolean 有効なmethodかどうか
     */
    private function _validMethod($method)
    {
        if($method == 'BOTH'){
            return true;
        } else {
            return strtolower($this->Request->getMethod()) == strtolower($method);
        }
    }





    /**
     * 設定内容を全て取得
     *
     * @param      string  $alias
     */
    public function getConfig($alias = NULL)
    {
        if($alias === NULL){
            return $this->_config;
        } else {
            return isset($this->_config[$alias]) ? $this->_config[$alias] : array() ;
        }
    }


    /**
     * 設定情報をクリア
     *
     * @access    public
     * @since     1.0.0
     */
    public function clear()
    {
        $this->_config = array();
    }
}

