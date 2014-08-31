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
 * HTTP Request class.
 *
 * @package     Samurai
 * @subpackage  Component.Request
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class HttpRequest extends Request
{
    /**
     * headers
     *
     * @access  public
     * @var     array
     */
    public $headers = array();

    /**
     * path (after root dir)
     *
     * @access  public
     * @var     string
     */
    public $path = '/';

    /**
     * path full
     *
     * @access  public
     * @var     string
     */
    public $full_path = '/';

    /**
     * parent path.
     *
     * @access  public
     * @var     string
     */
    public $parent_path = '';

    /**
     * base url
     *
     * @access  public
     * @var     string
     */
    public $base_url = '';


    /**
     * init.
     *
     * @access  public
     */
    public function init()
    {
        // not contain cookie.
        $request = array_merge($_GET, $_POST);
        $this->import($request);

        // headers
        foreach($this->getHttpHeaders() as $_key => $_val){
            $this->setHeader($_key, $_val);
        }

        // path
        if (isset($_SERVER['REQUEST_URI'])) {
            // bugfix built in server, when has format, then SCRIPT_NAME is seems like REQUEST_URI.
            if (php_sapi_name() === 'cli-server') {
                $_SERVER['SCRIPT_NAME'] = preg_replace('|^' . preg_quote($_SERVER['DOCUMENT_ROOT']) .  '|', '', $_SERVER['SCRIPT_FILENAME']);
            }

            $temp = explode('?', $_SERVER['REQUEST_URI']);
            $this->parent_path = dirname($_SERVER['SCRIPT_NAME']) == '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);
            $this->path = preg_replace('|^' . preg_quote($this->parent_path, '|') . '|', '', array_shift($temp));
        }
        if ($path = $this->get('_q')) {
            $this->path = $path;
        }

        // base url
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->base_url = 'http://' . $_SERVER['HTTP_HOST'] . $this->parent_path;
        }
    }



    /**
     * get path.
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * Get HTTP version.
     *
     * @access  public
     * @return  string
     */
    public function getHttpVersion()
    {
        if (! isset($_SERVER['SERVER_PROTOCOL'])) return '1.0';

        $version = str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']);
        return $version;
    }


    /**
     * Get method
     *
     * @access  public
     * @return  string
     */
    public function getMethod()
    {
        // method in request.
        if ($method = $this->get('_method')) {
            return strtoupper($method);
        }

        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        return strtoupper($method);
    }

    /**
     * Set header
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     */
    public function setHeader($key, $value)
    {
        $key = strtolower($key);
        $this->headers[$key] = $value;
    }


    /**
     * Get header
     *
     * @access  public
     * @param   string  $key
     * @param   mixed   $default
     */
    public function getHeader($key, $default = null)
    {
        $key = strtolower($key);
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }


    /**
     * get headers from http.
     *
     * @access  public
     * @return  array
     */
    public function getHttpHeaders()
    {
        if (! function_exists('apache_request_headers')) return array();
        return apache_request_headers();
    }
}

