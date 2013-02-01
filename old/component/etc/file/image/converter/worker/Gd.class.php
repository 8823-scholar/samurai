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
 * GDを使用して画像コンバートを処理するWorker
 * 
 * @package    Samurai
 * @subpackage Etc.File.Image.Converter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_File_Image_Converter_Worker_Gd extends Etc_File_Image_Converter_Worker
{
    /**
     * @implements
     */
    protected function _convert()
    {
        $new_path = $this->Converter->dirname . '/' . $this->Converter->basename;
        $old_path = $this->Converter->original('dirname') . '/' . $this->Converter->original('basename');
        
        //変換
        $image = imagecreatefromstring(file_get_contents($old_path));
        if($this->Converter->diff('width') || $this->Converter->diff('height')){
            $image = $this->_resize($image);
        }
        
        //保存
        switch($this->Converter->type){
            case IMAGETYPE_GIF:
                imagegif($image, $new_path);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($image, $new_path, 100);
                break;
            case IMAGETYPE_PNG:
                imagepng($image, $new_path);
                break;
            case IMAGETYPE_WBMP:
                imagewbmp($image, $new_path);
                break;
            default:
                throw new Samurai_Exception('unsuported convert format. -> ' . $this->Converter->type);
                break;
        }
        return $new_path;
    }


    /**
     * 画像のリサイズを行う
     *
     * @access     private
     * @param      resource $image   画像リソース
     * @return     resource リサイズされた画像リソース
     */
    private function _resize($image)
    {
        if($this->Converter->type == IMAGETYPE_GIF){
            $new_image = imagecreate($this->Converter->width, $this->Converter->height);
        } else {
            $new_image = imagecreatetruecolor($this->Converter->width, $this->Converter->height);
        }
        
        //GIF,PNGへの透過色対応
        if($this->Converter->original('type') == IMAGETYPE_GIF || $this->Converter->original('type') == IMAGETYPE_PNG){
            $transparent = imagecolortransparent($image);
            //透過色が設定されている
            if($transparent >= 0){
                imagetruecolortopalette($new_image, true, 256);
                $trans_color = @imagecolorsforindex($image, $transparent);
                if($trans_color){
                    $transparent = imagecolorallocate($new_image, $trans_color['red'], $trans_color['green'], $trans_color['blue']);
                    imagefill($new_image, 0, 0, $transparent);
                    imagecolortransparent($new_image, $transparent);
                }
            //PNG-24
            } elseif($this->Converter->original('type') == IMAGETYPE_PNG){
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagefill($new_image, 0, 0, $color);
            }
        }
        imagecopyresampled($new_image, $image, 0, 0, 0, 0,
                                $this->Converter->width, $this->Converter->height,
                                $this->Converter->original('width'), $this->Converter->original('height'));
        return $new_image;
    }
}

