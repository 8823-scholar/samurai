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
 * task.
 * 
 * @package     Samurai
 * @subpackage  Task
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Samurai_Task
{
    /**
     * parent task.
     *
     * @access  public
     * @var     Samurai_Task
     */
    public $parent;

    /**
     * reporter
     *
     * @access  public
     * @var     object
     */
    public $reporter;

    /**
     * depth
     *
     * @access  public
     * @var     int
     */
    public $depth = 0;

    /**
     * start time.
     *
     * @access  private
     * @var     float
     */
    private $_started_at = 0;

    /**
     * finish time.
     *
     * @access  private
     * @var     float
     */
    private $_finished_at = 0;


    /**
     * @dependencies
     */
    public $TaskManager;


    /**
     * constructor.
     *
     * @access     public
     */
    public function __construct()
    {
    }


    /**
     * set parent task.
     *
     * @access  public
     * @param   Samurai_Task    $task
     */
    public function setParent(Samurai_Task $parent)
    {
        $this->parent = $parent;
    }

    /**
     * get parent task.
     *
     * @access  public
     * @return  Samurai_Task
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * set reporter
     *
     * @access  public
     * @param   object
     */
    public function setReporter($reporter)
    {
        if ( ! is_object($reporter) ) throw new Samurai_Exception('Invalid reporter.');
        if ( ! method_exists($reporter, 'flushTaskMessage') ) throw new Samurai_Exception('Implements method "flushTaskMessage".');
        $this->reporter = $reporter;
    }

    /**
     * set depth.
     *
     * @access  public
     * @param   int     $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }



    /**
     * add task.
     *
     * @access  public
     * @param   string  $name
     */
    public function add($name)
    {
        return $this->TaskManager->interrupt($name, $this);
    }



    /**
     * prepare before execute.
     *
     * @access  public
     */
    public function prepare()
    {
    }


    /**
     * execute.
     *
     * @access  public
     */
    abstract public function execute();




    /**
     * event handler on start.
     *
     * @access  public
     */
    public function onStart()
    {
        $this->_started_at = microtime(true);
    }

    /**
     * event handler on success finished.
     *
     * @access  public
     */
    public function onSuccess()
    {
    }

    /**
     * event handler on failed finished.
     *
     * @access  public
     */
    public function onFailed()
    {
    }

    /**
     * event handler on finished.
     *
     * @access  public
     */
    public function onFinish()
    {
        $this->_finished_at = microtime(true);
    }


    /**
     * get past sec as float.
     *
     * @access  public
     * @return  float
     */
    public function getPastSec()
    {
        $diff = $this->_finished_at - $this->_started_at;
        return $diff;
    }
}

