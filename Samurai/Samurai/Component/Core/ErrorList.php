<?php
/**
 * The MIT License
 *
 * Copyright (c) 2007-2013, Samurai Framework Project, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Samurai\Component\Core;

/**
 * error list.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ErrorList
{
    /**
     * error types
     *
     * @var     array
     */
    public $types = [];

    /**
     * errors
     *
     * @var     array
     */
    public $errors = [];


    /**
     * set type
     *
     * @param   string  $type
     */
    public function setType($type)
    {
        $this->types[] = $type;
    }

    /**
     * get type.
     *
     * if multipule return last type.
     *
     * @return  string
     */
    public function getType()
    {
        return $this->types ? $this->types[count($this->types) - 1] : null;
    }

    /**
     * has type ?
     *
     * @param   string  $type
     * @return  boolean
     */
    public function hasType($type)
    {
        return in_array($type, $this->types);
    }


    /**
     * add error message
     *
     * @param   string  $key
     * @param   string  $message
     */
    public function add($key, $message)
    {
        if (! isset($this->errors[$key])) $this->errors[$key] = [];
        $this->errors[$key][] = $message;
    }

    /**
     * clear
     */
    public function clear()
    {
        $this->types = [];
        $this->errors = [];
    }


    /**
     * get message
     *
     * @param   string  $key
     * @return  string
     */
    public function getMessage($key)
    {
        return isset($this->errors[$key]) ? $this->errors[$key][0] : '';
    }

    /**
     * get messages in key
     *
     * @param   string  $key
     * @return  array
     */
    public function getMessages($key)
    {
        return isset($this->errors[$key]) ? $this->errors[$key] : [];
    }

    /**
     * get all each key
     *
     * @return array
     */
    public function getAllMessage()
    {
        $messages = [];
        foreach (array_keys($this->errors) as $key) {
            $messages[$key] = $this->getMessage($key);
        }
        return $messages;
    }

    /**
     * get all messages
     *
     * @return  array
     */
    public function getAllMessages()
    {
        $messages = [];
        foreach (array_keys($this->errors) as $key) {
            $messages = array_merge($messages, $this->getMessages($key));
        }
        return $messages;
    }


    /**
     * is exists
     *
     * @return  boolean
     */
    public function isExists()
    {
        return (boolean)$this->errors;
    }

    /**
     * alias of isExists
     *
     * @return  boolean
     */
    public function has()
    {
        return $this->isExists();
    }
}

