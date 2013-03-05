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
 * Response for Cli.
 *
 * @package     Samurai
 * @subpackage  Component.Response
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class CliResponse extends Response
{
    /**
     * body.
     *
     * @access  private
     * @var     HttpBody
     */
    private $_body;


    /**
     * @dependencies
     */
    public $Request;


    /**
     * constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->_body = new HttpBody();
    }


    /**
     * Set body.
     *
     * @access  public
     * @param   string  $body
     */
    public function setBody($body = null)
    {
        $this->_body->setContent($body);
        return $this->_body;
    }


    /**
     * output contents
     *
     * @access  public
     */
    public function execute()
    {
        $this->_sendBody();
    }


    /**
     * send body content
     *
     * @access  private
     */
    private function _sendBody()
    {
        $content = $this->_body->getContent();
        echo $content;
    }
}

