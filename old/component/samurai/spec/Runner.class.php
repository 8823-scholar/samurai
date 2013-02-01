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
 * spec runner abstract class.
 * 
 * @package     Samurai
 * @subpackage  Spec
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Spec_Runner
{
    /**
     * target spec.
     *
     * @access  protected
     * @var     string
     */
    protected $_target;

    /**
     * workspace.
     *
     * @access  protected
     * @var     string
     */
    protected $_workspace;


    /**
     * @dependencies
     */
    public $FileScanner;


    /**
     * constructor.
     *
     * @access     public
     */
    public function __construct()
    {
    }


    /**
     * set a target.
     * enable file and directory.
     *
     * @access  public
     * @param   string  $path
     */
    public function setTarget($path)
    {
        $this->_target = $path;
    }

    /**
     * get target.
     *
     * @access  public
     * @return  string
     */
    public function getTarget()
    {
        return $this->_target;
    }


    /**
     * set workspace.
     *
     * @access  public
     * @param   string  $path
     */
    public function setWorkspace($path)
    {
        $this->_workspace = $path;
    }

    /**
     * get workspace.
     *
     * @access  public
     * @return  string
     */
    public function getWorkspace()
    {
        return $this->_workspace;
    }




    /**
     * search target spec files.
     *
     * @access  public
     */
    abstract public function searchSpecFiles();


    /**
     * validate spec class name.
     *
     * @access  public
     * @param   string  $class
     * @return  string
     */
    abstract public function validateClassName($class);

    /**
     * validate spec class file.
     *
     * @access  public
     * @param   string  $class
     * @return  string
     */
    abstract public function validateClassFile($class);

    /**
     * run spec.
     *
     * @access  public
     */
    abstract public function run();
}

