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
 * ファイルの走査をするクラス
 * 
 * @package     Samurai
 * @subpackage  Etc.File
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Etc_File_Scanner
{
    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * 走査トリガー
     *
     * @access     public
     * @param      string  $directory   ディレクトリパス
     * @return     Etc_File_Scanner_Entity
     */
    public function scan($directory, $condition = NULL)
    {
        $Iterator = new Samurai_Iterator();
        $Entity = new Etc_File_Scanner_Entity($directory);
        if ( $Entity->isDirectory() ) {
            if ( ! $condition ) {
                $condition = $this->getCondition();
            }
            $this->_scan($Iterator, $Entity, $condition);
        } else {
            throw new Samurai_Exception('No directory. -> '.$directory);
        }
        return $Iterator;
    }


    /**
     * 最もスタンダードな走査
     *
     * @access     private
     * @param      Samurai_Iterator             $Iterator
     * @param      Etc_File_Scanner_Entity      $DirEntity
     * @param      Etc_File_Scanner_Condition   $condition
     */
    private function _scan($Iterator, $DirEntity, $condition = NULL)
    {
        $files = scandir($DirEntity->path);
        foreach ( $files as $file ) {
            if ( $file != '.' && $file != '..' ) {
                $File = new Etc_File_Scanner_Entity($DirEntity->path . DS . $file);
                if ( is_bool($condition) ) {
                    $reflexive = $condition;
                    $condition = Etc_File_Scanner_Condition();
                    $condition->reflexive = $reflexive;
                }
                if ( $matched = $this->_match($File, $condition) ) {
                    $Iterator->addElement($File);
                    
                }
                if ( $condition->reflexive && $File->isDirectory()
                    && ( $matched || !$condition->reflexive_matched_only ) ) {
                    $this->_scan($Iterator, $File, $condition);
                }
            }
        }
    }


    /**
     * 条件との比較
     *
     * @access     private
     * @param      object  $file
     * @param      object  $condition
     * @return     boolean
     */
    private function _match($file, $condition = NULL)
    {
        if(!$condition){
            return true;
        } else {
            return $condition->match($file);
        }
    }


    /**
     * 条件を取得する
     *
     * @return     object  Etc_File_Scanner_Condition
     */
    public function getCondition()
    {
        return new Etc_File_Scanner_Condition();
    }
}

