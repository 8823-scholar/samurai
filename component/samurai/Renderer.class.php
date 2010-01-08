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
 * テンプレートエンジンへのブリッジクラスの抽象クラス
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Renderer
{
    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        $this->_setEngine();
    }


    /**
     * Engineのセット
     *
     * @access     protected
     */
    abstract protected function _setEngine();
    
    
    /**
     * 初期化メソッド
     *
     * @access     public
     * @return     string  $init_script
     */
    public function init($init_script = NULL)
    {
        if($init_script !== NULL){
            include(Samurai_Loader::getPath($init_script));
        }
    }


    /**
     * レンダリング実行
     * 渡されたファイルを解釈しようとします
     *
     * @access     public
     * @param      string  $template   テンプレート
     */
    abstract public function render($template);


    /**
     * 変数の登録
     *
     * @access     public
     * @param      string  $key     キー
     * @param      mixed   $value   値
     */
    abstract public function assign($key, $value);


    /**
     * 登録した変数をすべて取得する
     *
     * @access     public
     * @return     array   アサインされた変数
     */
    abstract public function getAssignedVars();


    /**
     * ヘルパーを登録する
     *
     * @access     public
     * @param      string   $alias
     * @param      array    $define   初期化情報
     * @return     object
     */
    abstract public function addHelper($alias, $define);





    /**
     * ActionのEngineへの登録
     *
     * @access     public
     * @param      object  $Action   Actionコンポーネント
     */
    public function setAction($Action)
    {
        $Action->beforeRenderer();
        $this->assign('Action', $Action);
        foreach(get_object_vars($Action) as $_key => $_val){
            if(!preg_match('/^_/', $_key)){
                $this->assign($_key, $_val);
            }
        }
    }

    /**
     * ErrorListのEngineへの登録
     *
     * @access    public
     * @param     object   $ErrorList   ErrorListコンポーネント
     */
    public function setErrorList($ErrorList)
    {
        $this->assign('ErrorList', $ErrorList);
    }
    
    
    /**
     * RequestのEngineへの登録
     *
     * @access     public
     * @param      object  $Request   Requestコンポーネント
     */
    public function setRequest($Request)
    {
        $this->assign('Request', $Request);
        $this->assign('request', $Request->getParameters());
    }

    /**
     * CookieのEngineへの登録
     *
     * @access     public
     * @param      object  $Cookie   Cookieコンポーネント
     */
    public function setCookie($Cookie)
    {
        $this->assign('Cookie', $Cookie);
        $this->assign('cookie', $Cookie->getParameters());
    }

    /**
     * SessionのEngineへの登録
     *
     * @access     public
     * @param      object  $Session   Sessionコンポーネント
     */
    public function setSession($Session)
    {
        $this->assign('Session', $Session);
        $this->assign('session', $Session->getParameters());
    }

    /**
     * ServerのEngineへの登録
     *
     * @access     public
     * @param      array   $server   $_SERVERのEngineへの登録
     */
    public function setServer($server)
    {
        $this->assign('server', $server);
    }

    /**
     * SCRIPT_NAMEのEngineへの登録
     *
     * @access     public
     * @param      string  $script_name   $_SERVER['SCRIPT_NAME']
     */
    public function setScriptName($script_name)
    {
        $this->assign('script_name', $script_name);
    }

    /**
     * TokenのEngineへの登録
     *
     * @access     public
     * @param      object  $Token
     */
    public function setToken($Token)
    {
        $this->assign('Token', $Token);
        $this->assign('token', array('name' => $Token->getName(), 'value' => $Token->getValue()));
    }
}

