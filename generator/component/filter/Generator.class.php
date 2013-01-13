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
 * generator init filter.
 * 
 * @package    Samurai
 * @subpackage Generator.Filter
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Generator extends Samurai_Filter
{
    /**
     * @dependencies
     */
    public $Request;


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        if ( $this->_isDone() ) return;

        // search samurai dir
        $this->_setSamuraiDir();

        // load .samurai
        $this->_loadDotSamurai();
    }



    /**
     * set samurai dir
     *
     * @access  private
     */
    private function _setSamuraiDir()
    {
        $dir_now = getcwd();
        $dirs = explode(DS, $dir_now);
        $samurai_dir = NULL;
        while ( $dir = array_pop($dirs) ) {
            $temp_samurai_dir = join(DS, $dirs) . DS . $dir;
            $dot_samurai = $temp_samurai_dir . DS . '.samurai';
            if ( file_exists($dot_samurai) ) {
                $samurai_dir = $temp_samurai_dir;
                break;
            }
        }
        if ( ! $samurai_dir ) {
            $samurai_dir = $now_dir . DS . 'Samurai';
        }
        
        if ( $this->Request->get('samurai_dir') ) {
            $samurai_dir = $this->Request->get('samurai_dir');
        }
        
        Samurai_Config::set('generator.directory.samurai', $samurai_dir);
        Samurai_Config::set('directory.home', dirname($samurai_dir));
    }


    /**
     * load .samurai file.
     *
     * priority is
     *
     * 1. $PROJECT_DIR/.samurai
     * 2. $HOME/.samurai
     *
     * @access  private
     */
    private function _loadDotSamurai()
    {
        // 2. $HOME/.samurai
        if ( $home = getenv('HOME') ) {
            $conf_file = $home . DS . '.samurai';
            if ( Samurai_Loader::isReadable($conf_file) ) {
                Samurai_Config::import($conf_file);
            }
        }

        // 1. $PROJECT_DIR/.samurai
        $conf_file = Samurai_Config::get('generator.directory.samurai') . DS . '.samurai';
        if ( Samurai_Loader::isReadable($conf_file) ) {
            Samurai_Config::import($conf_file);
        }
    }





    /**
     * このフィルターが実行されたか
     *
     * このフィルターは初期化が目的なので、
     * Actionがフォワードされた際にも実行されたくない
     *
     * @access  private
     */
    private function _isDone()
    {
        if ( defined('_FILTER_GENERATOR_DONE') ) return true;
        
        define('_FILTER_GENERATOR_DONE', true);
        return false;
    }





    /**
     * @override
     */
    protected function _postfilter()
    {
        parent::_postfilter();
    }
}

