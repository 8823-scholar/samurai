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
     * @access  public
     * @var     int
     */
    public $status = 200;

    /**
     * top level body.
     *
     * @access  publi
     * @var     HttpBody
     */
    public $body;

    /**
     * status messages.
     *
     * @access  public
     * @var     array
     */
    public $status_messages = [
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
    ];

    /**
     * @dependencies
     */
    public $request;


    /**
     * constructor
     *
     * @access  public
     */
    public function __construct()
    {
        $this->body = new HttpBody();
    }


    /**
     * Set status code.
     *
     * @param   int     $code
     */
    public function setStatus($code)
    {
        $this->status = $code;
    }

    /**
     * get status code.
     *
     * @return  int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * get status message.
     *
     * @param   int|null    $status
     * @return  string
     */
    public function getStatusMessage($status = null)
    {
        if (! $status) $status = $this->getStatus();

        if (! $status || ! isset($this->status_messages[$status])) throw new \LogicException("No such status. -> {$status}");

        return $this->status_messages[$status];
    }


    /**
     * Set body.
     *
     * @access  public
     * @param   string  $body
     */
    public function setBody($body = null)
    {
        if ($body instanceof HttpBody) {
            $this->body = $body;
        } else {
            $this->body->setContent($body);
        }
        return $this->body;
    }

    /**
     * get body instance.
     *
     * @return  Samurai\Samurai\Component\Response\HttpBody
     */
    public function getBody()
    {
        return $this->body;
    }


    /**
     * Set header.
     *
     * @param   string  $key
     * @param   string  $value
     */
    public function setHeader($key, $value)
    {
        $this->body->setHeader($key, $value);
    }


    /**
     * location
     *
     * @param   string  $url
     * @param   int     $status
     */
    public function location($url, $status = 303)
    {
        $this->setStatus($status);
        $this->setHeader('location', $url);
    }



    /**
     * output contents
     *
     */
    public function execute()
    {
        // completion headers.
        $this->setHeader('date', date('r'));
        $this->setHeader('x-php-framework', 'Samurai Framework/' . Samurai::getVersion());

        $this->optimize('prepare');

        // send headers.
        $this->sendStatus();
        $this->sendHeaders();
        $this->sendBody();
    }


    /**
     * send status code.
     *
     * @access  private
     */
    private function sendStatus()
    {
        if (headers_sent()) return;

        if (isset($this->status_messages[$this->status])) {
            header(sprintf('HTTP/%s %d %s', $this->request->getHttpVersion(), $this->status, $this->status_messages[$this->status]));
        } else {
            header('Status: ' . $this->status);
        }
    }


    /**
     * send headers.
     *
     * @access  private
     */
    private function sendHeaders()
    {
        foreach ($this->body->getHeaders() as $key => $value) {
            $key = join('-', array_map('ucfirst', explode('-', $key)));
            header(sprintf('%s: %s', $key, $value));
        }
    }


    /**
     * send body content
     *
     * @access  private
     */
    private function sendBody()
    {
        $content = $this->body->render();
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

    /**
     * is https ?
     *
     * @access  public
     * @return  boolean
     */
    public function isHttps()
    {
        if (! isset($_SERVER['HTTPS'])) return false;

        return ! $_SERVER['HTTPS'] || $_SERVER['HTTPS'] == 'off' ? false : true;
    }
}

