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
 * task manager.
 *
 * used by "db-create", "db-drop", "db-migrate", "db-schema-load" and others.
 * 
 * @package     Samurai
 * @subpackage  Task
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Task_Manager extends Samurai_Task
{
    /**
     * task list.
     *
     * @access  private
     * @var     array
     */
    private $_list = array();

    /**
     * position of task.
     *
     * @access  private
     * @var     int
     */
    private $_pos = 0;


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
        parent::__construct();
    }


    /**
     * get task instance.
     *
     * @access  public
     * @param   string  $name
     * @return  Samurai_Database_Task
     */
    public function getInstanceByName($name)
    {
        // split by ":".
        $names = explode(':', $name);
        $name = 'Samurai_Task_' . join('_', array_map('ucfirst', $names));

        // new
        if ( ! class_exists($name) ) throw new Samurai_Exception('No such task. -> ' . $name);
        $instance = new $name();
        Samurai::getContainer()->injectDependency($instance);
        return $instance;
    }




    /**
     * execute.
     *
     * @implements
     */
    public function execute()
    {
        // execute stacked tasks.
        while ( $task = $this->current() ) {

            try {
                $task->onStart();

                $result = $task->prepare();
                if ( $result !== false ) {
                    $task->execute();
                }

                $task->onSuccess();
                $task->onFinish();
                $this->next();

            } catch(Exception $E) {
                $task->onFailed();
                $task->onFinish();
                $this->reporter->flushTaskMessage('aborted by exception.', $this);
                $this->reporter->flushTaskMessage($E->getMessage(), $this);
                break;
            }
        }
    }




    /**
     * add task.
     *
     * @access  public
     * @param   string          $name
     * @param   Samurai_Task    $parent
     */
    public function add($name, Samurai_Task $parent = NULL)
    {
        $task = $this->getInstanceByName($name);
        $task->setReporter($this->reporter);
        $task->setParent($parent ? $parent : $this);
        $task->setDepth($task->getParent()->depth + 1);
        $this->_list[] = $task;
        return $task;
    }

    /**
     * interrupt task.
     *
     * @access  public
     * @param   string          $name
     * @param   Samurai_Task    $parent
     * @param   Samurai_Task    $before
     */
    public function interrupt($name, Samurai_Task $parent = NULL, Samurai_Task $before = NULL)
    {
        $task = $this->getInstanceByName($name);
        $task->setReporter($this->reporter);
        $task->setParent($parent ? $parent : $this);
        $task->setDepth($task->getParent()->depth + 1);

        $pos = $before ? array_search($before, $this->_list, true) : $this->_pos;
        $pre = array_slice($this->_list, 0, $pos + 1);
        $pre[] = $task;
        $post = array_slice($this->_list, $pos + 1);
        $this->_list = array_merge($pre, $post);
        return $task;
    }



    /**
     * get current task.
     *
     * @access  public
     * @return  Samurai_Task
     */
    public function current()
    {
        if ( isset($this->_list[$this->_pos]) ) {
            $current = $this->_list[$this->_pos];
            return $current;
        }
        return false;
    }

    /**
     * step next seaquence.
     *
     * @access  public
     */
    public function next()
    {
        $this->_pos++;
    }
}

