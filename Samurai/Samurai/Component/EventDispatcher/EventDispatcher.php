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

namespace Samurai\Samurai\Component\EventDispatcher;

use Samurai\Samurai\Component\EventDispatcher\EventSubscriberInterface;

/**
 * event dispacher.
 *
 * @package     Samurai
 * @subpackage  Component.EventDispatcher
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class EventDispatcher
{
    /**
     * listeners
     *
     * @var     array
     */
    public $listeners = [];


    /**
     * add event listener
     *
     * @param   string  $name
     * @param   mixed   $listener
     * @param   int     $priority
     */
    public function addListener($name, $listener, $priority = 0)
    {
        if (! isset($this->listeners[$name])) $this->listeners[$name] = [];
        $this->listeners[$name][$priority][] = $listener;
    }


    /**
     * has listener ?
     *
     * @param   string  $name
     * @return  boolean
     */
    public function hasListener($name)
    {
        return isset($this->listeners[$name]);
    }

    /**
     * remove listener
     *
     * @param   string  $name
     * @param   mixed   $listener
     */
    public function removeListener($name, $listener)
    {
        if (! $this->hasListener($name)) return;

        foreach ($this->listeners as $priority => $listeners) {
            if (($key = array_search($listener, $listeners, true)) !== false) {
                unset($this->listeners[$name][$priority][$key]);
            }
        }
    }

    /**
     * get listeners
     *
     * @param   string  $name
     * @return  array
     */
    public function getListners($name)
    {
        if (! $this->hasListener($name)) return [];

        $listeners = [];
        krsort($this->listeners[$name]);
        foreach ($this->listeners[$name] as $_listeners) {
            $listeners = array_merge($listeners, $_listeners);
        }

        return $listeners;
    }


    /**
     * subscriber
     *
     * @param   Samurai\Samurai\Component\EventDispatcher\EventSubscriberInterface  $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $name => $params) {
            if (is_string($params)) {
                $this->addListener($name, [$subscriber, $params]);
            } elseif(is_string($params[0]) && is_numeric($params[1])) {
                $this->addListener($name, [$subscriber, $params[0]], $params[1]);
            } else {
                foreach ($params as $param) {
                    if (is_string($param)) {
                        $this->addListener($name, [$subscriber, $param]);
                    } else {
                        $this->addListener($name, [$subscriber, $param[0]], isset($param[1]) ? $param[1] : 0);
                    }
                }
            }
        }
    }


    /**
     * dispatch event
     *
     * @param   string  $name
     */
    public function dispatch($name, Event $event = null)
    {
        if (! $this->hasListener($name)) return;

        if (! $event) $event = new Event();
        $event->setDispatcher($this);
        $event->setName($name);

        foreach ($this->getListners($name) as $listener) {
            call_user_func($listener, $event);
            if ($event->isStop()) break;
        }
    }
}

