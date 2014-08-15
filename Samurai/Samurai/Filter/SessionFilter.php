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

namespace Samurai\Samurai\Filter;

use Samurai\Samurai\Component\Request\Session;
use Samurai\Samurai\Component\Request\Session\Handler\MemcacheHandler;

/**
 * session filter
 *
 * some.action:
 *     Session:
 *         name: foo
 *         cache_limiter: private_no_expire
 *         cache_expire: 60
 *
 * @package     Samurai
 * @subpackage  Filter
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SessionFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function prefilter()
    {
        parent::prefilter();
        if ($this->raikiri()->has('session')) return;

        $session = new Session();
        $this->raikiri()->register('session', $session);

        foreach ($this->getAttributes() as $key => $value) {
            switch ($key) {
                case 'name':
                    $session->setName($value);
                    break;
                case 'cache_limiter':
                    $session->setCacheLimiter($value);
                    break;
                case 'cache_expire':
                    $session->setCacheExpire($value);
                    break;
                case 'use_cookies':
                    $session->setUseCookies($value ? true : false);
                    break;
                case 'cookie_lifetime':
                    $session->setCookieLifetime($value);
                    break;
                case 'cookie_path':
                    $session->setCookiePath($value);
                    break;
                case 'cookie_domain':
                    $session->setCookieDomain($value);
                    break;
                case 'gc_maxlifetime':
                    $session->setConfig($key, $value);
                    break;
                case 'handler':
                    $handler = $this->getHandler($value);
                    $session->setHandler($handler);
                    break;
            }
        }

        // start
        $session->init();
    }


    /**
     * get session handler
     *
     * @param   string  $name
     * @return  Samurai\Samurai\Component\Request\Session\Handler
     */
    public function getHandler($name)
    {
        if (class_exists($name)) {
            $handler = new $name();
        } else {
            throw new NotFoundException('no such handler. -> ' . $name);
        }
        return $handler;
    }
}

