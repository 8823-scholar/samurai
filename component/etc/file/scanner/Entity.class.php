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
 * ファイルの実体クラス
 *
 * ファイルやディレクトリを体現する。
 * 
 * @package     Samurai
 * @subpackage  Etc.File
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_File_Scanner_Entity
{
    /**
     * ファイルパス
     *
     * @access   public
     * @var      string
     */
    public $path;

    /**
     * 格納ディレクトリ
     *
     * @access   public
     * @var      string
     */
    public $dirname;

    /**
     * ファイル名
     *
     * @access   public
     * @var      string
     */
    public $basename;

    /**
     * ファイル名(拡張子なし)
     *
     * @access   public
     * @var      string
     */
    public $filename;

    /**
     * 拡張子
     *
     * @access   public
     * @var      string
     */
    public $extension;

    /**
     * パーミッション
     *
     * @access   public
     * @var      string
     */
    public $permission;

    /**
     * 正規表現で格納される値
     *
     * @access   public
     * @var      array
     */
    public $matches;


    /**
     * コンストラクタ
     *
     * @access     public
     * @param      string  $path   ファイルパス
     */
    public function __construct($path)
    {
        $this->init($path);
    }


    /**
     * 初期化
     *
     * @access     public
     * @param      string  $path   ファイルパス
     */
    public function init($path)
    {
        $info = pathinfo($path);
        $this->dirname = $info['dirname'];
        $this->basename = $info['basename'];
        $this->filename = isset($info['filename']) ? $info['filename'] : '' ;
        $this->extension = isset($info['extension']) ? $info['extension'] : '' ;
        $this->path = $this->dirname . DS . $this->basename;
        $this->matches = array();
        if ( $this->isExists() ) {
            $this->permission = fileperms($this->path);
        }
    }


    /**
     * ファイルの中身を取得
     *
     * @access     public
     * @return     string  ファイルの中身
     */
    public function getContents()
    {
        if(!$this->isExists()) throw new Samurai_Exception('No such file or directory. -> ' . $this->path);
        if($this->isDirectory()) throw new Samurai_Exception('No file. -> ' . $this->path);
        return file_get_contents($this->path);
    }


    /**
     * コピー
     *
     * @access     public
     * @param      string  $dest    コピー先
     * @param      boolean $force   無理矢理上書きするか
     */
    public function copy($dest, $force = false)
    {
        if(!$this->isExists()) throw new Samurai_Exception('No such file or directory. -> ' . $this->path);
        if(file_exists($dest) && !$force) throw new Samurai_Exception('Already exists. -> ' . $dest);
        if($this->isDirectory()){
            @mkdir($dest, $this->permission);
            chmod($dest, $this->permission);
        } else {
            copy($this->path, $dest);
            chmod($dest, $this->permission);
        }
    }


    /**
     * 存在するかどうかを確認
     *
     * @access     public
     * @return     boolean 存在するかどうか
     */
    public function isExists()
    {
        return file_exists($this->path);
    }


    /**
     * この実体がディレクトリなのかどうかチェックする
     *
     * @access     public
     * @return     boolean
     */
    public function isDirectory()
    {
        return is_dir($this->path);
    }
}

