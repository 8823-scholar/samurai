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
 * Spec Generator
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Generator_Generator_Spec extends Generator
{
    /**
     * skeleton name of spec.
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_SPEC_PHPSPEC = 'spec/spec.phpspec.skeleton.php';
    public $SKELETON_SPEC_PHPUNIT = 'spec/spec.phpunit.skeleton.php';

    /**
     * skeleton name of initialization.
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_INIT = 'spec/initialization.skeleton.php';


    /**
     * @implements
     */
    public function generate($name, $skeleton, $params = array())
    {
        list($class_name, $file_name) = $this->_makeNames($name, 'runner.' . $params['runner']);

        // localize file name.
        $spec_file = sprintf('%s/%s/%s',
            Samurai_Config::get('generator.directory.samurai'),
            Samurai_Config::get('generator.directory.spec'),
            $file_name);

        // generate
        $params['class_name'] = $class_name;
        $result = $this->_generate($spec_file, $skeleton, $params);
        return array($result, $spec_file);
    }


    /**
     * Initialization用のgenerateメソッド
     *
     * @access     public
     * @param      string  $init_file   Actionファイル
     * @param      string  $skeleton    スケルトン名
     * @param      array   $params      Rendererに渡される値
     * @return     array   結果
     */
    public function generate4Init($init_file, $skeleton, $params = array())
    {
        $result = $this->_generate($init_file, $skeleton, $params);
        return array($result, $init_file);
    }





    /**
     * split by "_" and join "/".
     *
     * @access  private
     * @param   string  $name
     * @param   string  $context
     * @return  array   name, path
     */
    protected function _makeNames($name, $context = 'runner.phpspec')
    {
        switch ( $context ) {
        case 'runner.phpspec':
            $name = join('_', array_map('ucfirst', explode('_', $name)));
            $path = Samurai_Loader::getPathByClass($name, 'spec');
            $name = $name . '_Spec_Context';
            break;
        default:
            throw new Samurai_Exception('Invalid context. -> ' . $context);
        }
        return array($name, $path);
    }


    
    
    
    /**
     * @override
     */
    public function getSkeleton($filename = NULL)
    {
        if ( ! $filename ) $filename = $this->SKELETON_SPEC_PHPSPEC;
        return parent::getSkeleton($filename);
    }
}

