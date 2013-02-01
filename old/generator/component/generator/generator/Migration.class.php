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
 * Migration generator.
 * 
 * @package     Samurai
 * @subpackage  Generator
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Generator_Generator_Migration extends Generator
{
    /**
     * skeleton name.
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_MIGRATION = 'migration.skeleton.php';
    
    
    /**
     * @implements
     */
    public function generate($name, $skeleton, $params = array())
    {
        list($class_name, $file_name) = $this->_makeNames($name);

        // localize file name.
        $migration_file = sprintf('%s/%s/%s',
            Samurai_Config::get('generator.directory.samurai'),
            Samurai_Config::get('generator.directory.migration'),
            $file_name);

        // generate
        $params['class_name'] = $class_name;
        $result = $this->_generate($migration_file, $skeleton, $params);
        return array($result, $migration_file);
    }
    
    
    /**
     * split by "_" and join CamelCase.
     *
     * @access  private
     * @param   string  $name
     * @return  array   name, path
     */
    protected function _makeNames($name)
    {
        $prefix = date('YmdHis');
        $names = explode('_', $name);
        array_unshift($names, $prefix);
        $path = join('_', $names) . '.php';

        array_unshift($names, 'migration');
        $name = join('_', array_map('ucfirst', $names));

        return array($name, $path);
    }
}

