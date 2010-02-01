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

require_once 'PEAR.php';
require_once 'PEAR/PackageFileManager2.php';
require_once 'PEAR/PackageFileManager/File.php';
require_once 'PEAR/PackageFile/v2/Validator.php';
require_once 'PEAR/ChannelFile.php';
require_once 'PEAR/Frontend.php';
require_once 'PEAR/PackageFile/Generator/v2.php';

/**
 * SamuraiFWのPEARパッケージを作成します
 *
 * Samuraiに限定せずに、他のパッケージの作成にも利用できます。
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Make_Package extends Generator_Action
{
    /**
     * 状態(stable|beta|alpha)
     *
     * @access   public
     * @var      string
     */
    public $state = 'stable';

    /**
     * 付加
     *
     * @access   public
     * @var      string
     */
    public $postfix = '';

    /**
     * 一時ディレクトリ
     *
     * @access   private
     * @var      string
     */
    private $_tmp_dir = '/tmp/samurai';

    /**
     * 作業ディレクトリ
     *
     * @access   private
     * @var      string
     */
    private $_work_dir = '/tmp/samurai/work';

    /**
     * リリースディレクトリ
     *
     * @access   private
     * @var      string
     */
    private $_release_dir = '/tmp/samurai/release';

    /**
     * ソースディレクトリ
     *
     * @access   private
     * @var      string
     */
    private $_src_dir = '';

    /**
     * PackageFileManager2インスタンス
     *
     * @access   private
     * @var      object
     */
    private $Packager;


    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        //usage
        if($this->_isUsage()) return 'usage';
        //初期化
        $this->_init4make();
        
        //生成
        if(!$this->Request->get('customfile')){
            $this->_setOptions();
        } else {
            include($this->Request->get('customfile'));
        }
        $this->_generate();
    }


    /**
     * 生成
     *
     * @access     private
     */
    private function _generate()
    {
        $result = $this->Packager->generateContents();
        if(PEAR::isError($result)) exit($result->getMessage());
        if($this->Request->get('make')){
            $result = $this->Packager->writePackageFile();
            if(PEAR::isError($result)) exit($result->getMessage());
            $package = $this->_getPackageName() . '-' . $this->_getVersion();
            $archive = $package.'.tgz';
            chdir($this->_work_dir);
            shell_exec(sprintf('mv -f %s/package.xml ./', $this->_src_dir));
            shell_exec(sprintf('tar cvfz %s package.xml %s', $archive, $package));
            shell_exec(sprintf('cp -f %s %s/%s', $archive, $this->_release_dir, $archive));
            echo 'Successfuly generated ! -> '.$this->_release_dir.DS.$archive;
            echo "\n";
        } else {
            $result = $this->Packager->debugPackageFile();
            if(PEAR::isError($result)) exit($result->getMessage());
        }
    }


    /**
     * 設定
     *
     * @access     private
     */
    private function _setOptions()
    {
        $options = array(
            'baseinstalldir' => 'Samurai',
            'packagedirectory' => SAMURAI_DIR,
            'filelistgenerator' => 'file',
            'license' => 'The BSD License',
            'changelogoldtonew' => false,
            'roles' => array(
                '*' => 'php',
            ),
            'exceptions' => array(
                'bin/samurai.sh' => 'script',
                'bin/samurai.bat' => 'script',
            ),
            'installexceptions' => array(
                'bin/samurai.sh' => '/',
                'bin/samurai.bat' => '/'
            ),
            'ignore' => array(
                '.svn/',
                'package.xml',
            ),
            'addhiddenfiles' => true,
        );
        $result = $this->Packager->setOptions($options);
        if(PEAR::isError($result)) exit($result->getMessage());
        
        $this->Packager->setPackage('Samurai');
        $this->Packager->setSummary('Samurai PHP Framework Package');
        $this->Packager->setDescription('Samurai is PHP Web Application Framework extends Maple3.');
        $this->Packager->setChannel('pear.befool.co.jp');
        $this->Packager->setAPIVersion($this->_getVersion());
        $this->Packager->setAPIStability($this->state);
        $this->Packager->setReleaseVersion($this->_getVersion());
        $this->Packager->setReleaseStability($this->state);
        $this->Packager->setNotes(file_get_contents($this->_src_dir . '/CHANGELOG'));
        $this->Packager->setLicense('The BSD License', 'http://www.opensource.org/licenses/bsd-license.php');
        
        $this->Packager->setPackageType('php');
        $this->Packager->addRole('*', 'php');
        $this->Packager->addReplacement('bin/samurai.sh', 'pear-config', '@PEAR-DIR@', 'php_dir');
        $this->Packager->addReplacement('bin/samurai.sh', 'pear-config', '@PHP-BIN@',  'php_bin');
        $this->Packager->addReplacement('bin/samurai.bat', 'pear-config', '@PEAR-DIR@', 'php_dir');
        $this->Packager->addReplacement('bin/samurai.bat', 'pear-config', '@PHP-BIN@',  'php_bin');
        
        $this->Packager->addMaintainer('lead', 'hayabusa', 'KIUCHI Satoshinosuke', 'scholar@hayabusa-lab.jp');
        
        $this->Packager->addRelease();
        $this->Packager->setOSInstallCondition('windows');
        $this->Packager->addInstallAs('bin/samurai.bat', 'samurai.bat');
        $this->Packager->addIgnoreToRelease('bin/samurai.sh');
        $this->Packager->addRelease();
        $this->Packager->addInstallAs('bin/samurai.sh', 'samurai');
        $this->Packager->addIgnoreToRelease('bin/samurai.bat');
        
        //依存関係解消
        $this->Packager->setPhpDep('5.1.0');
        $this->Packager->setPearinstallerDep('1.4.11');
        $this->Packager->addPackageDepWithChannel('required', 'Crypt_Blowfish', 'pear.php.net');
        $this->Packager->addPackageDepWithChannel('required', 'Smarty', 'pear.samurai-fw.org');
        $this->Packager->addPackageDepWithChannel('required', 'PHPSpec', 'pear.samurai-fw.org');
    }





    /**
     * パッケージ名を取得
     *
     * @access     private
     * @return     string  パッケージ名
     */
    private function _getPackageName()
    {
        return $this->Request->get('package', 'Samurai');
    }


    /**
     * バージョンを取得する
     *
     * @access     private
     * @return     string
     */
    private function _getVersion()
    {
        $version = $this->Request->get('version', Samurai::VERSION);
        return $version . $this->postfix;
    }


    /**
     * 初期化
     *
     * @access     private
     */
    private function _init4make()
    {
        //補完
        if(!is_dir($this->_tmp_dir)){
            mkdir($this->_tmp_dir);
        }
        if(!is_dir($this->_work_dir)){
            mkdir($this->_work_dir);
        }
        if(!is_dir($this->_release_dir)){
            mkdir($this->_release_dir);
        }
        //移動
        $this->_src_dir = $this->Request->get('src_dir', SAMURAI_DIR);
        chdir($this->_src_dir);
        //削除
        $dir = $this->_getPackageName() . '-' . $this->_getVersion();
        @unlink($this->_work_dir . '/package.xml');
        if(is_dir($this->_work_dir . DS . $dir)){
            shell_exec(sprintf('rm -rf %s', $this->_work_dir . DS . $dir));
        }
        shell_exec(sprintf('cp -a %s %s', $this->_src_dir, $this->_work_dir . DS . $dir));
        //FileManager
        $this->Packager = new PEAR_PackageFileManager2();
    }
}

