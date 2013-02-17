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

use Samurai\Samurai\Component\Core\Parameters;

/**
 * HTTP Request class.
 *
 * @package     Samurai
 * @subpackage  Component.Request
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class HttpRequest extends Parameters
{
    /**
     * @dependencies
     */
    public $Config;

    /**
     * headers
     *
     * @access  private
     * @var     array
     */
    private $_headers = array();

    /**
     * path (after root dir)
     *
     * @access  private
     * @var     string
     */
    private $_path = '/';

    /**
     * path full
     *
     * @access  private
     * @var     string
     */
    private $_full_path = '/';

    /**
     * parent path.
     *
     * @access  private
     * @var     string
     */
    private $_parent_path = '';

    /**
     * base url
     *
     * @access  private
     * @var     string
     */
    private $_base_url = '';


    /**
     * init.
     *
     * @access  public
     */
    public function init()
    {
        // no cookie.
        $request = array_merge($_GET, $_POST);
        $this->_import($request);

        // headers
        if ( function_exists('apache_request_headers') ) {
            foreach(apache_request_headers() as $_key => $_val){
                $this->setHeader($_key, $_val);
            }
        }

        // path
        if ( isset($_SERVER['REQUEST_URI']) ) {
            $this->_parent_path = dirname($_SERVER['SCRIPT_NAME']) == '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);
            $this->_path = preg_replace('|^' . preg_quote($this->_parent_path, '|') . '|', '', array_shift(explode('?', $_SERVER['REQUEST_URI'])));
        }

        // base url
        if ( isset($_SERVER['HTTP_HOST']) ) {
            $this->_base_url = 'http://' . $_SERVER['HTTP_HOST'] . $this->_parent_path;
        }
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
        if ( $method = $this->get('_method') ) {
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
        $this->_headers[$key] = $value;
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
        return $this->_headers[$key];
    }


    /**
     * Get path
     *
     * @access  public
     * @return  string
     */
    public function getPath()
    {
        return $this->_path;
    }
}

