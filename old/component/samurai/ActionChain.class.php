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

Samurai_Loader::loadByClass('Samurai_Action');

/**
 * ActionChainを統括するクラス
 *
 * 実行すべきActionを保持し管理する
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_ActionChain
{
    /**
     * 保持しているAction
     *
     * @access   protected
     * @var      array
     */
    protected $_actions = array();

    /**
     * 現在実行されているActionの位置
     *
     * @access   protected
     * @var      int
     */
    protected $_index = 0;

    /**
     * Actionの位置を保持
     *
     * @access   protected
     * @var      array
     */
    protected $_position = array();

    /**
     * エラーリストを保持
     *
     * @access   protected
     * @var      array
     */
    protected $_errors = array();

    /**
     * 実行結果を保持
     *
     * @access   protected
     * @var      array
     */
    protected $_results = array();


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * Actionの実行
     *
     * @access     public
     * @param      object  $Action       Samurai_Action
     * @param      string  $error_type   既にエラーが発生していた場合の値
     * @return     mixed   Actionの実行結果
     */
    public function executeAction(Samurai_Action $Action, $error_type)
    {
        $method = 'execute' . ucfirst($error_type);
        if(method_exists($Action, $method)){
            $result = $Action->$method();
        } else {
            $result = $error_type;
        }
        return $result;
    }





    /**
     * Actionインスタンスの追加
     *
     * @access    public
     * @param     string  $name   action名
     * @return    boolean
     */
    public function add($name)
    {
        //Action名の補完
        if(!$name) $name = Samurai_Config::get('action.default');
        if(!preg_match('/^[0-9a-zA-Z_]+$/', $name)){
            Samurai_Logger::info('Invalid ActionName. -> %s', $name);
            $name = Samurai_Config::get('action.default');
        }
        //Actionの存在確認
        if(!$this->isActionExists($name)){
            Samurai_Logger::debug('Action Not Found. -> %s', $name);
            $name = Samurai_Config::get('action.default');
        }
        //既に同名のActionが追加されている場合
        if($this->hasAction($name)){
            Samurai_Logger::debug('Already Exists Action. -> %s -> revolve', $name);
            $this->revolve($name);
        }
        //生成
        else {
            $Action = $this->createAction($name);
            if(!is_object($Action)){
                Samurai_Logger::fatal('Action create failed... -> %s', $name);
            }
            //追加
            $this->_actions[$name] = $Action;
            $this->_position[] = $name;
            $this->_errors[$name] = Samurai::getContainer()->getComponent('ErrorList');
            $this->_results[$name] = NULL;
        }
        return true;
    }


    /**
     * Actionの存在確認
     *
     * @access     public
     * @param      string  $name   Action名
     * @return     boolean
     */
    public function isActionExists($name)
    {
        $path = Samurai_Loader::getPathByClass($name);
        $path = Samurai_Config::get('directory.action') . DS . $path;
        return Samurai_Loader::isReadable(Samurai_Loader::getPath($path));
    }


    /**
     * Actionの生成
     *
     * @access     public
     * @param      string  $name   Action名
     * @return     object  Actionインスタンス
     */
    public function createAction($name)
    {
        //作成
        list($class, $file) = $this->makeNames($name);
        if(!class_exists($class, false)){
            Samurai_Loader::load($file);
        }
        $Action = new $class();
        return $Action;
    }


    /**
     * Actionクラス名および、ファイルパスの取得
     *
     * @access    public
     * @param     string  $name    Action名
     * @return    array   Actionのクラス名とファイルパス
     */
    public function makeNames($name)
    {
        $path = Samurai_Loader::getPathByClass($name);
        $name = Samurai_Config::get('action.prefix') . '_' . $name;
        $name = join('_', array_map('ucfirst', explode('_', $name)));
        $path = Samurai_Config::get('directory.action') . DS . $path;
        return array($name, $path);
    }


    /**
     * アクションがすでに追加されているかどうか
     *
     * @access     public
     * @param      string  $action_name
     * @return     boolean 同名のアクションが追加されているかどうか
     */
    public function hasAction($action_name)
    {
        return isset($this->_actions[$action_name]) && is_object($this->_actions[$action_name]);
    }


    /**
     * ActionChainを次に進めることができるかどうかを返却
     *
     * @access    public
     * @return    boolean
     */
    public function hasNext()
    {
        return $this->_index < $this->getSize();
    }


    /**
     * 最後のActionかどうかを判断
     *
     * @access     public
     * @return     boolean
     */
    public function isLast()
    {
        $current = $this->getCurrentActionName();
        $next = $this->getNextActionName();
        if(!$next || $current == $next){
            return true;
        } else {
            return false;
        }
    }


    /**
     * ActionChainを次に進める
     *
     * @access    public
     */
    public function next()
    {
        $this->_index++;
    }


    /**
     * ActionChainの長さを返却
     *
     * @access    public
     * @return    int
     */
    public function getSize()
    {
        return count($this->_actions);
    }


    /**
     * 登録されているActionすべて取得
     *
     * @access     public
     * @return     array
     */
    public function getActions()
    {
        return $this->_actions;
    }


    /**
     * 現在のAction名を返却
     *
     * @access    public
     * @return    string
     */
    public function getCurrentActionName()
    {
        if(isset($this->_position[$this->_index])){
            return $this->_position[$this->_index];
        }
    }


    /**
     * 現在のアクションの実行結果を取得
     *
     * @access     public
     * @return     mixed
     */
    public function getCurrentActionResult()
    {
        $name = $this->getCurrentActionName();
        return $this->_results[$name];
    }

    /**
     * 現在のActionの実行結果をセット
     *
     * @access     public
     * @param      mixed   $result   Action結果
     */
    public function setCurrentActionResult($result)
    {
        $name = $this->getCurrentActionName();
        $this->_results[$name] = $result;
    }

    /**
     * 次のAction名を返却
     *
     * @access     public
     * @return     string
     */
    public function getNextActionName()
    {
        if(isset($this->_position[($this->_index+1)])){
            return $this->_position[($this->_index+1)];
        }
    }


    /**
     * すべてのAction名を返却
     *
     * @access     public
     * @return     array
     */
    public function getAllActionName()
    {
        return array_values($this->_position);
    }


    /**
     * 現在のActionのインスタンスを返却
     *
     * @access    public
     * @return    object   現在のAction
     */
    public function getCurrentAction()
    {
        if($name = $this->getCurrentActionName()){
            return $this->getActionByName($name);
        }
    }


    /**
     * 指定された名前のActionを返却する
     *
     * @access    public
     * @param     string  $name   Action名
     * @return    object
     */
    public function getActionByName($name)
    {
        if(!$this->hasAction($name)){
            Samurai_Logger::error('Action Not Registed. -> %s', array($name));
        }
        return $this->_actions[$name];
    }


    /**
     * 指定されたActionに対するErrorListインスタンスを返却
     *
     * @access    public
     * @param     string  $name   Action名
     * @return    object
     */
    public function getErrorListByName($name)
    {
        if($this->hasAction($name)){
            return $this->_errors[$name];
        }
    }


    /**
     * 現在のActionのErrorListインスタンスを返却
     *
     * @access    public
     * @return    object
     */
    public function getCurrentErrorList()
    {
        $name = $this->getCurrentActionName();
        return $this->getErrorListByName($name);
    }


    /**
     * 直前のActionのErrorListインスタンスを返却
     *
     * @access     public
     * @param      object  ErrorListインスタンス
     */
    public function getPreviousErrorList()
    {
        if(isset($this->_position[($this->_index-1)])){
            $name = $this->_position[($this->_index-1)];
            return $this->getErrorListByName($name);
        }
    }





    /**
     * アクションチェインをクリアする
     *
     * @access     public
     */
    public function clear()
    {
        $this->_actions = array();
        $this->_index = 0;
        $this->_position = array();
        $this->_errors = array();
        $this->_results = array();
    }
}

