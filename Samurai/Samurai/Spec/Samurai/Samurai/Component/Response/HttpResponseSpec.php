<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Response;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class HttpResponseSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Response\HttpResponse');
        $this->shouldImplement('Samurai\Samurai\Component\Response\Response');
    }


    public function it_is_http_request()
    {
        $this->isHttp()->shouldBe(true);
    }

    public function it_is_https_request()
    {
        $server = $_SERVER;

        $_SERVER['HTTPS'] = '';
        $this->isHttps()->shouldBe(false);
        
        $_SERVER['HTTPS'] = 'off';
        $this->isHttps()->shouldBe(false);
        
        $_SERVER['HTTPS'] = '1';
        $this->isHttps()->shouldBe(true);
        
        $_SERVER['HTTPS'] = 'on';
        $this->isHttps()->shouldBe(true);

        $_SERVER = $server;
    }

    public function it_gets_and_sets_status_code()
    {
        $this->setStatus(200);
        $this->getStatus()->shouldBe(200);
        
        $this->setStatus(404);
        $this->getStatus()->shouldBe(404);
    }

    public function it_gets_status_message()
    {
        $this->getStatusMessage(100)->shouldBe('Continue');
        $this->getStatusMessage(101)->shouldBe('Switching Protocols');
        $this->getStatusMessage(102)->shouldBe('Processing');
        $this->getStatusMessage(200)->shouldBe('OK');
        $this->getStatusMessage(201)->shouldBe('Created');
        $this->getStatusMessage(202)->shouldBe('Accepted');
        $this->getStatusMessage(203)->shouldBe('Non-Authoritative Information');
        $this->getStatusMessage(204)->shouldBe('No Content');
        $this->getStatusMessage(205)->shouldBe('Reset Content');
        $this->getStatusMessage(206)->shouldBe('Partial Content');
        $this->getStatusMessage(207)->shouldBe('Multi-Status');
        $this->getStatusMessage(226)->shouldBe('IM Used');
        $this->getStatusMessage(300)->shouldBe('Multiple Choices');
        $this->getStatusMessage(301)->shouldBe('Moved Permanently');
        $this->getStatusMessage(302)->shouldBe('Found');
        $this->getStatusMessage(303)->shouldBe('See Other');
        $this->getStatusMessage(304)->shouldBe('Not Modified');
        $this->getStatusMessage(305)->shouldBe('Use Proxy');
        $this->getStatusMessage(307)->shouldBe('Temporary Redirect');
        $this->getStatusMessage(400)->shouldBe('Bad Request');
        $this->getStatusMessage(401)->shouldBe('Unauthorized');
        $this->getStatusMessage(402)->shouldBe('Payment Required');
        $this->getStatusMessage(403)->shouldBe('Forbidden');
        $this->getStatusMessage(404)->shouldBe('Not Found');
        $this->getStatusMessage(405)->shouldBe('Method Not Allowed');
        $this->getStatusMessage(406)->shouldBe('Not Acceptable');
        $this->getStatusMessage(407)->shouldBe('Proxy Authentication Required');
        $this->getStatusMessage(408)->shouldBe('Request Timeout');
        $this->getStatusMessage(409)->shouldBe('Conflict');
        $this->getStatusMessage(410)->shouldBe('Gone');
        $this->getStatusMessage(411)->shouldBe('Length Required');
        $this->getStatusMessage(412)->shouldBe('Precondition Failed');
        $this->getStatusMessage(413)->shouldBe('Request Entity Too Large');
        $this->getStatusMessage(414)->shouldBe('Request-URI Too Long');
        $this->getStatusMessage(415)->shouldBe('Unsupported Media Type');
        $this->getStatusMessage(416)->shouldBe('Requested Range Not Satisfiable');
        $this->getStatusMessage(417)->shouldBe('Expectation Failed');
        $this->getStatusMessage(418)->shouldBe('I\'m a teapot');
        $this->getStatusMessage(422)->shouldBe('Unprocessable Entity');
        $this->getStatusMessage(423)->shouldBe('Locked');
        $this->getStatusMessage(424)->shouldBe('Failed Dependency');
        $this->getStatusMessage(426)->shouldBe('Upgrade Required');
        $this->getStatusMessage(500)->shouldBe('Internal Server Error');
        $this->getStatusMessage(501)->shouldBe('Not Implemented');
        $this->getStatusMessage(502)->shouldBe('Bad Gateway');
        $this->getStatusMessage(503)->shouldBe('Service Unavailable');
        $this->getStatusMessage(504)->shouldBe('Gateway Timeout');
        $this->getStatusMessage(505)->shouldBe('HTTP Version Not Supported');
        $this->getStatusMessage(506)->shouldBe('Variant Also Negotiates');
        $this->getStatusMessage(507)->shouldBe('Insufficient Storage');
        $this->getStatusMessage(510)->shouldBe('Not Extended');
    }

    public function it_gets_body()
    {
        $contents = <<<'EOL'
<html>
    <body>
        hoge
    </body>
</html>
EOL;
        $this->setBody($contents);
        $body = $this->getBody();
        $body->shouldHaveType('Samurai\Samurai\Component\Response\HttpBody');
        $body->getContent()->shouldBe($contents);
        $body->getHeader('content-length', strlen($contents));
    }

    public function it_sets_header()
    {
        $this->setHeader('x-spec', 'foo-bar-zoo');
        $body = $this->getBody();
        $body->getHeader('x-spec')->shouldBe('foo-bar-zoo');
    }
}

