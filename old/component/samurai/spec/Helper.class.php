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
 * helper of spec.
 * 
 * @package     Samurai
 * @subpackage  Spec
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Spec_Helper
{
    /**
     * @dependencies
     */


    /**
     * constructor.
     *
     * @access     public
     */
    public function __construct()
    {
    }



    /**
     * get runner.
     *
     * @access  public
     * @param   string  $name
     * @return  Samurai_Spec_Runner
     */
    public function getRunner($name)
    {
        switch ( $name ) {
        case 'phpspec':
            $name = 'PHPSpec';
            break;
        case 'phpunit':
            $name = 'PHPUnit';
            break;
        }
        $class = 'Samurai_Spec_Runner_' . ucfirst($name);
        $runner = new $class();
        Samurai::getContainer()->injectDependency($runner);
        return $runner;
    }



    /**
     * get source class name by spec source file.
     *
     * @access  public
     * @param   string  $source
     * @return  string
     */
    public function getSourceClassName($source)
    {
        // remove unrelated directory. ("spec")
        if ( $source[0] === '/' ) {
            $source = str_replace(Samurai_Config::get('generator.directory.samurai') . DS, '', $source);
        } else {
            $source = str_replace(array('../', './'), '', $source);
        }
        $names = explode(DS, $source);
        array_shift($names);

        // remove suffix. (".spec.php")
        $basename = array_pop($names);
        $temp = explode('.', $basename);
        array_push($names, array_shift($temp));

        // convert class name
        $names[] = 'Spec_Context';
        $class_name = join('_', array_map('ucfirst', $names));

        return $class_name;
    }
}

