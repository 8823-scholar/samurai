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

namespace Samurai\Samurai\Component\Request;

/**
 * session
 *
 * @package     Samurai
 * @subpackage  Component.Request
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Session extends Request
{
    /**
     * handler
     *
     * @var     Samurai\Samurai\Component\Request\Session\Handler
     */
    public $handler;


    /**
     * session start
     */
    public function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->import($_SESSION);
        }
    }


    /**
     * set name.
     *
     * @param   string  $name
     */
    public function setName($name)
    {
        session_name($name);
    }

    /**
     * get name.
     *
     * @return  string
     */
    public function getName()
    {
        return session_name();
    }


    /**
     * set cache_limiter
     *
     * @param   string  $limiter
     */
    public function setCacheLimiter($limiter)
    {
        session_cache_limiter($limiter);
    }


    /**
     * set cache_expire
     *
     * @param   int     $expire
     */
    public function setCacheExpire($expire)
    {
        session_cache_expire($expire);
    }


    /**
     * set use cookie.
     *
     * @param   boolean     $flag
     */
    public function setUseCookies($flag = true)
    {
        $this->setConfig('use_cookies', $flag ? 1 : 0);
    }

    /**
     * set cookie_lifetime
     *
     * @param   int     $lifetime
     */
    public function setCookieLifetime($lifetime)
    {
        $params = session_get_cookie_params();
        session_set_cookie_params($lifetime, $params['path'], $params['domain'], $params['secure']);
    }

    /**
     * set cookie_path
     *
     * @param   string  $path
     */
    public function setCookiePath($path)
    {
        $params = session_get_cookie_params();
        session_set_cookie_params($params['lifetime'], $path, $params['domain'], $params['secure']);
    }
    
    /**
     * set cookie domain
     *
     * @param   string  $domain
     */
    public function setCookieDomain($domain)
    {
        $params = session_get_cookie_params();
        session_set_cookie_params($params['lifetime'], $params['path'], $domain, $params['secure']);
    }
    
    /**
     * set cookie domain
     *
     * @param   string  $domain
     */
    public function setCookieDomain($domain)
    {
        $params = session_get_cookie_params();
        session_set_cookie_params($params['lifetime'], $params['path'], $domain, $params['secure']);
    }


    /**
     * set gc config
     *
     * @param   string  $key
     * @param   int     $value
     */
    public function setConfig($key, $value)
    {
        ini_set('session.' . $key, $value);
    }



    /**
     * set handler
     *
     * @param   Samurai\Samurai\Component\Request\Session\Handler   $handler
     */
    public function setHandler(Handler $handler)
    {
        $this->handler = $handler;
        session_set_save_handler($this->handler);
    }
}

