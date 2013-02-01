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
 * DeviceChangerデバッガ
 *
 * Filter_Front_DeviceChangerを簡単に利用できるようになる
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Debug_Debugger_DeviceChanger extends Filter_Debug_Debugger
{
    /**
     * @override
     */
    public $position = 'menu';
    public $icon = 'DeviceChanger';
    public $heading = 'DeviceChanger';
    protected $_escape = false;

    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * FileScannerコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $FileScanner;

    /**
     * 検索ディレクトリ
     *
     * @access   private
     * @var      array
     */
    private $_dirs = array();

    /**
     * 切り替え可能端末
     *
     * @access   private
     * @var      array
     */
    private $_devices = array();


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        
    }


    /**
     * @implements
     */
    public function setup()
    {
        //FileScannerのセット
        $this->_setFileScanner();
        //デバイス一覧の取得
        foreach(Samurai::getSamuraiDirs() as $dir){
            //component/filter/front/.device
            $dir = sprintf('%s/%s/front/.device', $dir, Samurai_Config::get('directory.filter'));
            try {
                $condition = $this->FileScanner->getCondition();
                $condition->setRegexp('|/\.svn|');
                $condition->negative = true;
                $condition->target = 'path';
                $condition->reflexive = true;
                $files = $this->FileScanner->scan($dir, $condition);
                foreach($files as $file){
                    if($file->isDirectory()){
                        $this->_dirs[] = $file;
                    } else {
                        $this->_devices[] = $file;
                    }
                }
            } catch(Samurai_Exception $E){}
        }
        //それを展開する
        $this->_content = array();
        $this->_content[] = '<A href="?samurai_device_changer=reset">元に戻す</A>';
        foreach($this->_dirs as $dir){
            $this->_content[$dir->basename] = array();
        }
        foreach($this->_devices as $file){
            $dirname = basename($file->dirname);
            $this->_content[$dirname][] = sprintf('<A href="?samurai_device_changer=%s/%s">%s</A>',
                                                        $dirname, $file->basename, $file->basename);
        }
    }


    /**
     * fileScannerのセット
     *
     * @access     private
     */
    private function _setFileScanner()
    {
        if(!$this->FileScanner){
            $Container = Samurai::getContainer();
            $Def = $Container->getContainerDef();
            $Def->class = 'Etc_File_Scanner';
            $this->FileScanner = $Container->getComponentByDef('FileScanner', $Def);
        }
    }
}

