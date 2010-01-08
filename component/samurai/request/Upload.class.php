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

Samurai_Loader::loadByClass('Samurai_Request_Upload_File');

/**
 * Requestで受け取ったファイル値を格納する
 *
 * アップロード版Request
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <schoscholar@hayabusa-lab.jp
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Request_Upload
{
    /**
     * ファイル格納
     *
     * @access   private
     * @var      object
     */
    private $Files;


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        $this->Files = new Samurai_Request_Upload_File();
        foreach($_FILES as $name => $file){
            $File = new Samurai_Request_Upload_File($file);
            $this->Files->addFile($File, $name);
        }
    }





    /**
     * 格納されたファイルを取得する
     *
     * @access     public
     * @param      string  $name   キー
     * @return     array
     */
    public function getFiles($name = NULL)
    {
        return $this->Files->getFiles($name);
    }


    /**
     * ファイルを取得する
     *
     * @access     public
     * @param      string  $name   キー
     * @return     object  Samurai_Request_Upload_File
     */
    public function getFile($name)
    {
        return $this->Files->getFile($name);
    }





    /**
     * ファイル名(主に拡張子)の調節を行う
     * jpeg=jpg、html=htmなど、同じ意味でも表記にずれがあるものを調節
     *
     * @access     private
     * @param      string   $file_name
     * @return     string
     */
    private function _regulateFileName($file_name)
    {
        $pathinfo = pathinfo($file_name);
        if(isset($pathinfo['extension'])){
            switch($pathinfo['extension']){
                case 'jpeg':
                    $pathinfo['extension'] = 'jpg'; break;
                case 'htm':
                    $pathinfo['extension'] = 'html'; break;
            }
        } else {
            $pathinfo['extension'] = '';
        }
        $pathinfo['extension'] = strtolower($pathinfo['extension']);
        return preg_replace('/\..*$/', '.' . $pathinfo['extension'], $file_name);
    }
}

