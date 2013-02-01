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
 * @package    Samurai
 * @copyright  Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * ComponentGenerator
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Generator_Generator_Component extends Generator
{
    /**
     * スケルトン名
     *
     * @access   public
     * @var      string
     */
    public $SKELETON = 'component.skeleton.php';



    /**
     * @implements
     */
    public function generate($component_name, $skeleton, $params = array())
    {
        //ローカライズ
        list($class_name, $file_name) = $this->_makeNames($component_name);
        $component_dir = sprintf('%s/%s', Samurai_Config::get('generator.directory.samurai'), Samurai_Config::get('generator.directory.component'));
        $component_file = sprintf('%s/%s', $component_dir, $file_name);
        //ジェネレイト
        $params['class_name'] = $class_name;
        $result = $this->_generate($component_file, $skeleton, $params);
        return array($result, $component_file);
    }


    /**
     * 「_」つながりで記述されたコンポーネント名を、実際のクラス名とファイル名に変換する。
     *
     * @access     protected
     * @param      string  $component_name   コンポーネント名
     * @return     array   ファイル名など
     */
    protected function _makeNames($component_name)
    {
        $name = join('_', array_map('ucfirst', explode('_', $component_name)));
        $path = Samurai_Loader::getPathByClass($name);
        return array($name, $path);
    }



    /**
     * スケルトンの取得
     *
     * @access     public
     * @return     string
     */
    public function getSkeleton($filename = NULL)
    {
        $filename = $this->SKELETON;
        return parent::getSkeleton($filename);
    }
}

