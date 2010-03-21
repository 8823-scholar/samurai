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
 * Pagerの基本ユニット
 *
 * Pagingに必要な基本的な能力を一通り備えている。
 * 
 * @package    Samurai
 * @subpackage Etc.Helper.Pager
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_Helper_Pager_Unit
{
    /**
     * 現在のオフセット
     *
     * @access   public
     * @var      int
     */
    public $offset = 0;

    /**
     * 1ページの表示件数
     *
     * @access   public
     * @var      int
     */
    public $per_page = 10;

    /**
     * pageを保持するリクエストキー
     *
     * @access   public
     * @var      string
     */
    public $url_var = 'page';

    /**
     * アイテムの総件数
     *
     * @access   public
     * @var      int
     */
    public $total_items = 0;

    /**
     * 前のページID
     *
     * @access   public
     * @var      int
     */
    public $prev;

    /**
     * 次のページID
     *
     * @access   public
     * @var      int
     */
    public $next;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * 次のページがあるかどうか
     *
     * @access     public
     * @return     mixed   次のページID
     */
    public function hasNext()
    {
        $page = $this->getNow();
        if($this->total_items - $page * $this->per_page > 0){
            return $page + 1;
        } else {
            return false;
        }
    }


    /**
     * 前のページがあるかどうか
     *
     * @access     public
     * @return     mixed   前のページID
     */
    public function hasPrev()
    {
        $page = $this->getNow();
        if($page > 1){
            return $page - 1;
        } else {
            return false;
        }
    }


    /**
     * 現在のページを取得する
     *
     * @access     public
     * @return     int
     */
    public function getNow()
    {
        $page = $this->Request->getParameter($this->url_var);
        if(!$page || !is_numeric($page)){
            $page = ceil( ($this->offset + 1) / $this->per_page );
        }
        return (int)$page;
    }


    /**
     * 何件目から取得しているかを返却
     *
     * @access     public
     * @return     int
     */
    public function getStart()
    {
        return $this->total_items == 0 ? 0 : $this->offset + 1;
    }


    /**
     * 何件目まで取得しているかを返却
     *
     * @access     public
     * @return     int
     */
    public function getEnd()
    {
        //次ページがある場合はそのページでのMaxまで
        if($this->hasNext()){
            return $this->getStart() + $this->per_page - 1;
        }
        //そうでない場合はMax値を
        else {
            return $this->total_items;
        }
    }


    /**
     * 総件数を返却
     *
     * @access     public
     * @return     int
     */
    public function getTotal()
    {
        return $this->total_items;
    }


    /**
     * 総ページ数を取得する
     *
     * @access     public
     * @return     int
     */
    public function getTotalPage()
    {
        return ceil($this->getTotal() / $this->per_page);
    }


    /**
     * Pear::Pagerのslidingモードのようにページを取得できる。
     * Pear::Pagerはリンクを直接吐くが、これはページIDの配列を返す。
     *
     * @access     public
     * @param      int     $delta   現在のページサイドに表示するリンク数
     * @return     array   sliding配列
     */
    public function sliding($delta = 5)
    {
        $pages = array();
        $page_count = ceil($this->total_items / $this->per_page);
        $page_now = $this->getNow();
        $all_delta = $delta * 2 + 1;
        //基準点追加
        $pages[] = $page_now;
        for($i=1; count($pages) < $all_delta; $i++){
            //手前に余裕があれば入れる
            if($page_now - $i > 0) $pages[] = $page_now - $i;
            //後ろに余裕があれば入れる
            if($page_now + $i <= $page_count) $pages[] = $page_now + $i;
            //リンク数がmaxまでくれば終了
            if(count($pages) >= $page_count) break;
            //無限ループ回避
            if($i > 100) break;
        }
        sort($pages);
        return $pages;
    }



    /**
     * REQUEST値に引数のパラメータをくっつけて返却する
     *
     * @access     public
     * @param      string  $addtional_param
     * @return     string  
     */
    public function appendQuery($addtional_param = '')
    {
        $params = $this->Request->getParameters();
        if(isset($params[$this->url_key])) unset($params[$this->url_key]);
        $params = $this->_array2UrlStrings($params);
        $params[] = $addtional_param;
        return '?' . join('&', $params);
    }


    /**
     * 配列をURL形式の文字列に変換する
     *
     * @access     protected
     * @param      array   $array
     * @return     array   url_strings
     */
    protected function _array2UrlStrings(array $array, $prefix = '')
    {
        $url_strings = array();
        foreach($array as $_key => $_val){
            if($prefix == '' && $_key == Samurai_Config::get('action.request_key')) continue;
            if(is_array($_val)){
                $url_strings = array_merge($url_strings, $this->_array2UrlStrings($_val, $prefix ? "{$prefix}[{$_key}]" : $_key));
            } else {
                $_string = $prefix ? "{$prefix}[{$_key}]" : $_key ;
                $url_strings[] = $_string . '=' . urlencode($_val);
            }
        }
        return $url_strings;
    }
}

