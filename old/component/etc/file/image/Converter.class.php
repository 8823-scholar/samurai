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
 * 画像のコンバートをサポートするコンポーネント
 *
 * 具体的なコンバート処理は、それぞれのWorkerが担当する
 * 
 * @package    Samurai
 * @subpackage Etc.File.Image.Converter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_File_Image_Converter
{
    /**
     * ディレクトリ
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
     * 拡張子
     *
     * @access   public
     * @var      string
     */
    public $extension;

    /**
     * ファイル名(拡張子なし)
     *
     * @access   public
     * @var      string
     */
    public $filename;

    /**
     * ファイルサイズ
     *
     * @access   public
     * @var      int
     */
    public $size;

    /**
     * 画像幅
     *
     * @access   public
     * @var      int
     */
    public $width;

    /**
     * 画像高さ
     *
     * @access   public
     * @var      int
     */
    public $height;

    /**
     * 種類
     *
     * @access   public
     * @var      string
     */
    public $type;

    /**
     * 色の深度
     *
     * @access   public
     * @var      int
     */
    public $bits;

    /**
     * mime
     *
     * @access   public
     * @var      string
     */
    public $mime;

    /**
     * オリジナル情報
     *
     * @access   private
     * @var      object
     */
    public $original;

    /**
     * デフォルトの変換worker
     * 
     * @access   public
     * @var      string
     */
    public $default_worker = '';


    /**
     * コンストラクタ
     *
     * @access     public
     * @param      string  $default_worker
     */
    public function __construct($default_worker = 'gd')
    {
        $this->default_worker = $default_worker;
    }



    /**
     * コンバート
     *
     * @access     public
     * @param      mixed   $worker
     */
    public function convert($worker = NULL)
    {
        if(!$worker || ! $worker instanceof Etc_File_Image_Converter_Worker){
            $worker = $this->getWorker($this->default_worker);
        }
        return $worker->convert($this);
    }


    /**
     * コンバート対象の画像のロード
     *
     * @access     public
     * @param      string  $image_path
     */
    public function loadImage($image_path)
    {
        $image_path = Samurai_Loader::getPath($image_path);
        if(Samurai_Loader::isReadable($image_path)){
            //現在の情報
            $path_info = pathinfo($image_path);
            $image_info = getimagesize($image_path);
            $this->dirname   = $path_info['dirname'];
            $this->basename  = $path_info['basename'];
            $this->extension = $path_info['extension'];
            $this->filename  = $path_info['filename'];
            $this->size      = filesize($image_path);
            $this->width     = $image_info[0];
            $this->height    = $image_info[1];
            $this->type      = $image_info[2];
            if(isset($image_info['bits'])) $this->bits = $image_info['bits'];
            if(isset($image_info['mime'])) $this->mime = $image_info['mime'];
            //オリジナルの値にセット
            $this->original->dirname   = $this->dirname;
            $this->original->basename  = $this->basename;
            $this->original->extension = $this->extension;
            $this->original->filename  = $this->filename;
            $this->original->size      = $this->size;
            $this->original->width     = $this->width;
            $this->original->height    = $this->height;
            $this->original->type      = $this->type;
            $this->original->bits      = $this->bits;
            $this->original->mime      = $this->mime;
        } else {
            throw new Samurai_Exception('image not found. -> ' . $image_path);
        }
    }


    /**
     * dirname設定する
     *
     * @access     public
     * @param      string  $dirname
     */
    public function setDirname($dirname)
    {
        $this->dirname = $dirname;
    }


    /**
     * basenameを設定する
     *
     * @access     public
     * @param      string  $basename
     */
    public function setBasename($basename)
    {
        $info = pathinfo($basename);
        $this->basename = $basename;
        $this->filename = $info['filename'];
        $this->setExtension(isset($info['extension']) ? $info['extension'] : '');
    }


    /**
     * extensionを設定する
     *
     * @access     public
     * @param      string  $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        switch(strtolower($this->extension)){
            case 'gif':
                $this->type = IMAGETYPE_GIF;
                break;
            case 'jpg':
            case 'jpeg':
                $this->type = IMAGETYPE_JPEG;
                break;
            case 'png':
                $this->type = IMAGETYPE_PNG;
                break;
            case 'wbmp':
                $this->type = IMAGETYPE_WBMP;
                break;
            default:
                break;
        }
        $this->mime = image_type_to_mime_type($this->type);
    }


    /**
     * 幅・高さの最大値を比率を保ったまま設定する
     *
     * @access     public
     * @param      int     $scale
     * @param      boolean $force   最大値より画像が小さい場合でも指定サイズに変更するかどうか
     */
    public function maxScale($scale, $force = false)
    {
        if($scale <= 0) return false;
        if($scale >= $this->width && $scale >= $this->height && !$force) return false;
        if($this->width > $this->height){
            $this->height = floor($scale * $this->height / $this->width);
            $this->width  = $scale;
        } elseif($this->width < $this->height) {
            $this->width  = floor($scale * $this->width / $this->height);
            $this->height = $scale;
        } else {
            $this->width  = $scale;
            $this->height = $scale;
        }
    }


    /**
     * 幅の最大値を比率を保ったまま設定する
     *
     * @access     public
     * @param      int     $width
     * @param      boolean $force   最大値より画像が小さい場合でも指定サイズに変更するかどうか
     */
    public function maxWidth($width, $force = false)
    {
        if($width <= 0) return false;
        if($width >= $this->width && !$force) return false;
        $this->height = floor($width * $this->height / $this->width);
        $this->width  = $width;
    }


    /**
     * 高さの最大値を比率を保ったまま設定する
     *
     * @access     public
     * @param      int     $height
     * @param      boolean $force   最大値より画像が小さい場合でも指定サイズに変更するかどうか
     */
    public function maxHeight($height, $force = false)
    {
        if($height <= 0) return false;
        if($height >= $this->height && !$force) return false;
        $this->width = floor($height * $this->width / $this->height);
        $this->height  = $height;
    }



    /**
     * ワーカーを取得する
     *
     * @access     public
     * @param      string  $worker_name
     * @return     object  Etc_File_Image_Converter_Worker
     */
    public function getWorker($worker_name)
    {
        $worker_name = 'Etc_File_Image_Converter_Worker_' . ucfirst($worker_name);
        Samurai_Loader::loadByClass($worker_name);
        $worker = new $worker_name();
        return $worker;
    }


    /**
     * オリジナルの値を取得
     *
     * @access     public
     * @param      string  $property
     * @return     mixed
     */
    public function original($property)
    {
        $value = isset($this->original->$property) ? $this->original->$property : NULL ;
        return $value;
    }


    /**
     * オリジナルの値と違いがあるかどうか
     *
     * @access     public
     * @param      string  $property
     * @return     boolean
     */
    public function diff($property)
    {
        return isset($this->original->$property) && $this->$property !== $this->original->$property;
    }
}

