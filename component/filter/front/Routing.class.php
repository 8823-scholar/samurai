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
 * ルーティング機能を実装するためのフロントフィルター
 *
 * symfonyを完全にパクリました。
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Front_Routing extends Samurai_Filter
{
    /**
     * ルーティング情報
     *
     * @access   private
     * @var      array
     */
    private $_routes = array();

    /**
     * 現在のルーティング名
     *
     * @access   private
     * @var      string
     */
    private $_current_route_name = '';

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        if(!Samurai_Config::get('filter.routing.enable')) return;
        
        //設定ファイルの読み込み
        $configs = (array)$this->getAttribute('config');
        foreach($configs as $config){
            $this->_import($config);
        }
        
        //解析
        if(isset($_SERVER['REQUEST_URI'])){
            $results = $this->_parse(MAIN_URI);
            foreach($results as $_key => $_val){
                $this->Request->setParameter($_key, $_val);
            }
        }
    }


    /**
     * 設定ファイルの読み込み
     *
     * @access     private
     * @param      string  $conf_file   設定ファイルパス
     */
    private function _import($conf_file)
    {
        //読込
        $config = Samurai_Yaml::load($conf_file);
        foreach($config as $route_name => $route){
            $this->_addRoute(
                $route_name,
                isset($route['url']) && $route['url'] ? $route['url'] : '/',
                isset($route['param']) && $route['param'] ? $route['param'] : array(),
                isset($route['requirements']) && $route['requirements'] ? $route['requirements'] : array(),
                isset($route['matching']) && $route['matching'] ? $route['matching'] : array()
            );
        }
    }


    /**
     * ルーティングの追加
     *
     * @access     private
     * @param      string  $name           ルーティング名
     * @param      string  $route          ルートURI
     * @param      array   $default        ディフォルト値
     * @param      array   $requirements   必須条件
     */
    private function _addRoute($name, $route, $default=array(), $requirements=array(), $matching='full')
    {
        if(isset($this->_routes[$name])){
            Samurai_Logger::error('This routing is already exists. -> %s', $name);
        }
        //初期化
        $parsed     = array();
        $names      = array();
        $suffix     = '';
        $names_hash = array();
        //TOPページのルーティング
        if($route == '' || $route == '/'){
            $regexp = '|^[/]*$|';
            $this->_routes[$name] = array($route, $regexp, array(), array(), $default, $requirements, $suffix);
            
        //具体的なルーティング追加
        } else {
            //分解
            $elements = array();
            foreach(explode('/', $route) as $element){
                if(trim($element)){
                    $elements[] = $element;
                }
            }
            
            //suffix指定
            if(preg_match('/^(.+)(\.\w*)$/i', $elements[count($elements)-1], $matches)){
                $suffix = ($matches[2] == '.') ? '' : $matches[2] ;
                $elements[count($elements)-1] = $matches[1];
                $route = '/'.implode('/', $elements);
            } elseif($route[strlen($route)-1] == '/'){
                $suffix = '/';
            }
            $regexp_suffix = $suffix == '*' ? '.*?' : preg_quote($suffix);
            
            //プレースフォルダの内容取得用正規表現
            foreach($elements as $element){
                if(preg_match('/^:(.+)$/', $element, $matches)){
                    $parsed[] = '(?:/([^/]+))?';
                    $names[]  = $matches[1];
                    $names_hash[$matches[1]] = 1;
                    
                } elseif(preg_match('/^\*$/', $element, $matches)){
                    $parsed[] = '(?:/(.*))?';
                    
                } else {
                    $parsed[] = '/'.$element;
                }
            }
            if($matching == 'prefix'){
                $regexp = sprintf('|^%s%s|', join('', $parsed), $regexp_suffix);
            } else {
                $regexp = sprintf('|^%s%s$|', join('', $parsed), $regexp_suffix);
            }
            
            //追加
            $this->_routes[$name] = array($route, $regexp, $names, $names_hash, $default, $requirements, $suffix);
        }
    }


    /**
     * URLの解析
     *
     * @access     private
     * @param      string  $url   REQUEST_URI
     * @return     array   解析結果
     */
    private function _parse($url)
    {
        //補完
        if($url && $url[0] != '/') $url = '/'.$url;
        //クエリー部分の除去
        if($pos = strpos($url, '?')) $url = substr($url, 0, $pos);
        //prefixの除去
        if($this->getAttribute('prefix') != '') $url = preg_replace('|^' . $this->getAttribute('prefix', BASE_URI) . '|', '', $url);
        //連続した「/」の除去
        $url = preg_replace('|/+|', '/', $url);
        //$url中に「/id:1000/」のようなパス情報がある場合は、
        //$urlから除去された上で、/key:value/の関係で、GET内に保持される。
        foreach(explode('/', $url) as $_key => $element){
            if(preg_match('/^([a-z0-9_\-]+):(.+)$/i', $element, $matches)){
                $this->Request->setParameter($matches[1], urldecode($matches[2]));
            }
        }
        //解析開始
        $break = false;
        $parameters = array();
        foreach($this->_routes as $route_name => $route){
            //初期化
            $break      = false;
            $parameters = array();
            $matches    = array();
            list($route, $regexp, $names, $names_hash, $defaults, $requirements, $suffix) = $route;
            Samurai_Logger::debug('Routing connect to \'%s\'. -> regexp:%s url:%s', array($route_name, $regexp, $url));
            //正規表現に一致
            if(preg_match($regexp, $url, $matches)){
                $break = true;
                array_shift($matches);
                //最初に全て埋めておく
                foreach($names as $name) $parameters[$name] = NULL;
                //ディフォルト
                foreach($defaults as $name => $value){
                    if(preg_match("/[a-z_\-]/i", $name)){
                        $parameters[$name] = $value;
                    } else {
                        $parameters[$value] = true;
                    }
                }
                $pos = 0;
                foreach($matches as $found){
                    if(isset($names[$pos])){
                        //reqirements調査
                        if(isset($requirements[$names[$pos]]) && !preg_match('/' . $requirements[$names[$pos]] . '/', $found)){
                            $break = false;
                            break;
                        }
                        $parameters[$names[$pos]] = urldecode($found);
                    } else {
                        
                    }
                    $pos++;
                }
                //見つからない値があった場合
                foreach($names as $name){
                    if($parameters[$name]===NULL){
                        $break = false;
                    }
                }
                //全ての条件を満たした場合
                if($break){
                    $this->_setCurrentRouteName($route_name);
                    Samurai_Logger::debug('Routing connect to \'%s\' -> success', array($route_name));
                    break;
                } else {
                    $parameters = array();
                }
            }
        }
        //解析結果
        if(!$break && !$this->Request->getParameter(Samurai_Config::get('action.request_key'))){
            Samurai_Logger::debug('Routing connect to \'actions(chain_url_action)\'');
            $actions  = array();
            $elements = explode('/', $url);
            foreach($elements as $element){
                if(preg_match('/^[^~][a-z0-9]+$/i', $element)){
                    $actions[] = $element;
                } elseif(preg_match('/^(?!_)([^:]+)(\.\w*)$/i', $element, $matches)){
                    $actions[] = $matches[1];
                }
            }
            if($actions) $parameters[Samurai_Config::get('action.request_key')] = join('_', $actions);
        }
        return $parameters;
    }


    /**
     * 現在のルーティングネームの設定
     *
     * @access     private
     * @param      string  $route_name   ルーティングネーム
     */
    private function _setCurrentRouteName($route_name)
    {
        $this->_current_route_name = $route_name;
    }
}

