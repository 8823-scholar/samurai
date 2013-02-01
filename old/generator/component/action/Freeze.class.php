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
 * Samuraiのバージョンをアプリに対して固定するコマンド
 *
 * つまり、PEAR領域にあるSamuraiをアプリの領域にコピーする
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Freeze extends Generator_Action
{
    /**
     * FileScannerコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $FileScanner;


    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        //Usage
        if($this->_isUsage()) return 'usage';
        //フリーズ
        if($this->_confirmFreeze()){
            $this->_backup();
            $this->_freeze();
            $this->_sendMessage('Successfuly Freezed.');
            $this->_sendMessage('*** attension ***');
            $this->_sendMessage('You should rewrite the SamuraiFW path in "index.php" and "info.php".');
            $this->_sendMessage('When you do not rewrite it, the freeze command might be equal to no success.');
        } else {
            $this->_sendMessage('aborted...');
        }
    }


    /**
     * 確認
     *
     * @access     private
     * @return     boolean フリーズしていいかどうか
     */
    private function _confirmFreeze()
    {
        return $this->_confirm('May I freeze the version at ' . Samurai::VERSION . ' ?');
    }


    /**
     * バックアップ処理
     *
     * @access     private
     */
    private function _backup()
    {
        $source_dir = Samurai_Config::get('generator.directory.samurai');
        $dest_dir   = dirname($source_dir) . DS . basename($source_dir) . '.' . date('Ymd');
        $files = $this->FileScanner->scan($source_dir);
        if(!is_dir($dest_dir)) mkdir($dest_dir);
        foreach($files as $File){
            $dest = $dest_dir . str_replace($source_dir, '', $File->path);
            try {
                $File->copy($dest, true);
            } catch(Samurai_Exception $E){}
        }
    }


    /**
     * 凍結処理
     * PEAR領域のSamuraiをアプリケーション側にコピーする。
     *
     * @access     private
     */
    private function _freeze()
    {
        $cond = $this->FileScanner->getCondition();
        $cond->reflexive = true;
        $cond->reflexive_matched_only = true;
        $cond->setRegexp('/^(\.svn|package\.xml)$/');
        $cond->negative = true;
        $files = $this->FileScanner->scan(SAMURAI_DIR, $cond);
        foreach($files as $File){
            $dest = Samurai_Config::get('generator.directory.samurai') . str_replace(SAMURAI_DIR, '', $File->path);
            if(file_exists($dest)){
                if(!is_dir($dest)) $this->_sendMessage('Already exists. -> ' . $dest . ' -> skip');
            } else {
                try {
                    $File->copy($dest);
                    if(!$File->isDirectory()){
                        $this->_rewrite($dest);
                        $this->_sendMessage('Successfuly copied. -> ' . $File->path);
                    }
                } catch(Samurai_Exception $E){}
            }
        }
    }


    /**
     * 内容の書き換えが必要なファイルはここで書き換える
     *
     * @access     private
     * @param      string  $file   対象ファイル
     */
    private function _rewrite($file)
    {
        switch(str_replace(Samurai_Config::get('generator.directory.samurai'), '', $file)){
            case '/bin/samurai.sh':
            case '/bin/samurai.bat':
                $this->_rewrite4SamuraiSh($file);
                break;
        }
    }


    /**
     * samurai.shなどの書き換え
     *
     * @access     private
     * @param      string  $file   対象ファイル
     */
    private function _rewrite4SamuraiSh($file)
    {
        $contents = file_get_contents($file);
        $contents = str_replace('@PHP-BIN@', $_SERVER['_'], $contents);
        $contents = str_replace('@PEAR-DIR@', dirname(Samurai_Config::get('generator.directory.samurai')), $contents);
        file_put_contents($file, $contents);
        chmod($file, 0755);
    }
}

