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
 * Requestで受け取ったファイルのDTO
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Request_Upload_File
{
    /**
     * ファイル名
     *
     * @access   public
     * @var      string
     */
    public $name = '';

    /**
     * ファイル名(拡張子抜き)
     *
     * @access   public
     * @var      string
     */
    public $filename = '';

    /**
     * content-type
     *
     * @access   public
     * @var      string
     */
    public $type = '';

    /**
     * ファイルサイズ
     *
     * @access   public
     * @var      int
     */
    public $size = 0;

    /**
     * ファイルパス
     *
     * @access   public
     * @var      string
     */
    public $path = '';

    /**
     * 拡張子
     *
     * @access   public
     * @var      string
     */
    public $extension = '';

    /**
     * エラーコード
     *
     * @access   public
     * @var      int
     */
    public $error = UPLOAD_ERR_OK;

    /**
     * 配列でアップされた際はこちらに格納される
     *
     * @access   private
     * @var      array
     */
    private $_files = array();

    /**
     * アップロードされたのかどうか
     *
     * @access   private
     * @var      boolean
     */
    private $_uploaded = false;


    /**
     * コンストラクタ
     *
     * @access    public
     * @param     array    $file
     */
    public function __construct($file = array())
    {
        if($file){
            $this->import($file);
        }
    }


    /**
     * インポート
     *
     * @access     public
     * @param      array   $_FILEの値
     * @return     object  $this
     */
    public function import($file)
    {
        if(is_array($file['name'])){
            foreach($file['name'] as $_key => $_val){
                $_file = array();
                $_file['name'] = $file['name'][$_key];
                $_file['type'] = $file['type'][$_key];
                $_file['size'] = $file['size'][$_key];
                $_file['tmp_name'] = $file['tmp_name'][$_key];
                $_file['error'] = $file['error'][$_key];
                $this->addFile(new Samurai_Request_Upload_File($_file), $_key);
            }
        } else {
            $pathinfo = explode('.', $file['name'], 2);
            $this->name = $file['name'];
            $this->type = $file['type'];
            $this->size = $file['size'];
            $this->path = $file['tmp_name'];
            $this->error = $file['error'];
            $this->filename = $pathinfo[0];
            if(isset($pathinfo[1])){
                $this->extension = $pathinfo[1];
            }
            if($this->error == UPLOAD_ERR_OK){
                $this->_uploaded = true;
            }
        }
    }


    /**
     * ファイルを追加する
     *
     * @access     public
     * @param      object  $File   Samurai_Request_Upload_File
     * @param      string  $name   キー
     */
    public function addFile(Samurai_Request_Upload_File $File, $name=NULL)
    {
        if($name){
            $this->_files[$name] = $File;
        } else {
            $this->_files[] = $File;
        }
    }


    /**
     * ファイルを取得する
     *
     * @param      string  $name   キー
     * @return     object  Samurai_Request_Upload_File
     */
    public function getFile($name)
    {
        $names = explode('.', $name);
        $basekey = array_shift($names);
        $postfix = join('.', $names);
        if(isset($this->_files[$basekey])){
            if($postfix === ''){
                return $this->_files[$basekey];
            } else {
                return $this->_files[$basekey]->getFile($postfix);
            }
        } else {
            return NULL;
        }
    }


    /**
     * ファイルを全部取得する
     *
     * @access     public
     * @return     array   ファイル配列
     */
    public function getFiles($with_no_file=true)
    {
        if(!$with_no_file){
            $files = array();
            foreach($this->_files as $file){
                if($file->error !== UPLOAD_ERR_NO_FILE) $files[] = $file;
            }
            return $files;
        } else {
            return $this->_files;
        }
    }





    /**
     * ファイルを指定ディレクトリへ移動する
     *
     * @access     public
     * @param      string  $dest   異動先ファイルパス
     * @return     boolean 移動できたかどうか
     */
    public function move($dest, $mode=0644)
    {
        if(!$this->isArray()){
            $Utility = Samurai::getContainer()->getComponent('Utility');
            $Utility->fillupDirectory(dirname($dest), 0777);
            if(move_uploaded_file($this->path, $dest)){
                $this->path = $dest;
                chmod($this->path, $mode);
                return true;
            }
        }
        return false;
    }


    /**
     * ファイルのバイナリ文字列を取得する
     *
     * @access     public
     * @return     string  バイナリ文字列
     */
    public function getString()
    {
        return file_get_contents($this->path);
    }


    /**
     * ファイルのリソースを取得する
     *
     * @access     public
     * @param      resource ファイルリソース
     */
    public function getResource()
    {
        return fopen($this->path, 'r');
    }





    /**
     * アップロードされたかどうか
     *
     * @access     public
     * @return     boolean アップロードされたかどうか
     */
    public function isUploaded()
    {
        return $this->_uploaded;
    }


    /**
     * 配列かどうか
     *
     * @access     public
     * @return     boolean 配列かどうか
     */
    public function isArray()
    {
        return $this->_files;
    }
}

