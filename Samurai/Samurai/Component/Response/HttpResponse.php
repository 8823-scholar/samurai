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

use Samurai\Samurai\Samurai;

/**
 * Response for HTTP.
 *
 * @package     Samurai
 * @subpackage  Component.Response
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class HttpResponse extends Response
{
    /**
     * status code
     *
     * @access  private
     * @var     int
     */
    private $_status = 200;

    /**
     * top level body.
     *
     * @access  private
     * @var     HttpBody
     */
    private $_body;

    /**
     * status messages.
     *
     * @access  public
     * @var     array
     */
    public $status_messages = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        510 => 'Not Extended',
    );

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
     * Set status code.
     *
     * @access  public
     * @param   int     $code
     */
    public function setStatus($code)
    {
        $this->_status = $code;
    }

    /**
     * Set body.
     *
     * @access  public
     * @param   string  $body
     */
    public function setBody($body = null)
    {
        if ( $body instanceof HttpBody ) {
            $this->_body = $body;
        } else {
            $this->_body->setContent($body);
        }
        return $this->_body;
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
        $this->_body->setHeader($key, $value);
    }



    /**
     * output contents
     *
     * @access  public
     */
    public function execute()
    {
        // completion headers.
        $this->setHeader('date', date('r'));
        $this->setHeader('x-php-framework', 'Samurai Framework/' . Samurai::getVersion());

        // send headers.
        $this->_sendStatus();
        $this->_sendHeaders();
        $this->_sendBody();
    }


    /**
     * send status code.
     *
     * @access  private
     */
    private function _sendStatus()
    {
        if ( headers_sent() ) return;

        if ( isset($this->status_messages[$this->_status]) ) {
            header(sprintf('HTTP/%s %d %s', $this->Request->getHttpVersion(), $this->_status, $this->status_messages[$this->_status]));
        } else {
            header('Status: ' . $this->_status);
        }
    }


    /**
     * send headers.
     *
     * @access  private
     */
    private function _sendHeaders()
    {
        foreach ( $this->_body->getHeaders() as $key => $value ) {
            $key = join('-', array_map('ucfirst', explode('-', $key)));
            header(sprintf('%s: %s', $key, $value));
        }
    }


    /**
     * send body content
     *
     * @access  private
     */
    private function _sendBody()
    {
        $content = $this->_body->render();
        echo $content;
    }




    /**
     * is http ?
     *
     * @access  public
     * @return  boolean
     */
    public function isHttp()
    {
        return true;
    }
}

