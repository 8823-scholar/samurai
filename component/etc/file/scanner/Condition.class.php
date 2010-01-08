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
 * ファイルの走査時の条件クラス
 * 
 * @package    Samurai
 * @subpackage Etc.File
 * @copyright  2009-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  The BSD License
 */
class Etc_File_Scanner_Condition
{
    /**
     * 種類
     *
     * @access   public
     * @var      string
     */
    public $type = '';

    /**
     * 値
     *
     * @access   public
     * @var      string
     */
    public $value = '';

    /**
     * ネガティブ判断をするかどうか
     *
     * @access   public
     * @var      boolean
     */
    public $negative = false;

    /**
     * 対象
     *
     * @access   public
     * @var      string
     */
    public $target = 'basename';

    /**
     * 再帰的に走査するかどうか
     *
     * @access   public
     * @var      boolean
     */
    public $reflexive = false;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * 正規表現の条件を設定する
     *
     * @access     public
     * @param      string   $regexp   正規表現
     */
    public function setRegexp($regexp)
    {
        $this->type = 'regexp';
        $this->value = $regexp;
    }


    /**
     * 拡張子が一致するかどうか
     *
     * @access     public
     * @param      string   $extension
     */
    public function setExtension($extension)
    {
        if(is_array($extension)) $extension = join('|', $extension);
        $this->setRegexp('/\.(' . $extension . ')$/');
    }



    /**
     * 比較する
     *
     * @access     public
     * @param      object   $file   Etc_File_Scanner_Entity
     * @return     boolean
     */
    public function match(Etc_File_Scanner_Entity $file)
    {
        $target = $file->{$this->target};
        switch($this->type){
            case 'regexp':
                $result = preg_match($this->value, $target, $file->matches);
                break;
            default:
                $result = true;
                break;
        }
        return $this->negative ? !$result : $result;
    }
}

