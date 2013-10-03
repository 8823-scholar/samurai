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

namespace Samurai\Samurai\Component\Response;

/**
 * Response body for HTTP.
 *
 * @package     Samurai
 * @subpackage  Component.Response
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class HttpBody
{
    /**
     * content
     *
     * @access  public
     * @var     string
     */
    public $content = '';

    /**
     * headers
     *
     * @access  public
     * @var     array
     */
    public $headers = array();


    /**
     * constructor
     *
     * @access  public
     * @param   string  $content
     */
    public function __construct($content = null)
    {
        if ($content) {
            $this->setContent($content);
        }
    }


    /**
     * Set content.
     *
     * @access  public
     * @param   string  $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->setHeader('content-length', strlen($this->content));
    }


    /**
     * Get content.
     *
     * @access  public
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }




    /**
     * Set header.
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
     * Get header.
     *
     * @access  public
     * @param   string  $key
     * @param   string  $default
     * @return  string
     */
    public function getHeader($key, $default = null)
    {
        $key = strtolower($key);
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }


    /**
     * Get all headers.
     *
     * @access  public
     * @return  array
     */
    public function getHeaders()
    {
        return $this->headers;
    }



    /**
     * build and return content string.
     *
     * @access  public
     * @return  string
     */
    public function render($with_headers = false)
    {
        $contents = array();

        // headers
        $headers = $this->getHeaders();
        if ($with_headers && $headers) {
            foreach ($headers as $key => $value) {
                $key = join('-', array_map('ucfirst', explode('-', $key)));
                $contents[] = sprintf('%s: %s', $key, $value);
            }
            $contents[] = '';
        }

        // content
        $contents[] = $this->content;

        return join("\n", $contents);
    }
}

