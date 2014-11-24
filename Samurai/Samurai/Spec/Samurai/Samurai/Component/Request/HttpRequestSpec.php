<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Request;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\Utility\ArrayUtility;

class HttpRequestSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Request\HttpRequest');
    }

    public function it_initialize_get_and_post_to_params(ArrayUtility $u)
    {
        $this->raikiri()->register('arrayUtil', $u);

        // backup.
        $get = $_GET;
        $post = $_POST;
        $cookie = $_COOKIE;

        // sample
        $_GET = ['key1' => 'value1', 'key2' => 'value2', 'array' => [1,2,3,'hoge' => 'hoge']];
        $_POST = ['post_key1' => 'post_value1', 'post_key2' => 'post_value2'];
        $_COOKIE = ['cookie_key1' => 'cookie_value1'];

        $request = array_merge($_GET, $_POST);
        $u->merge([], $request)->willReturn($request);

        $this->init();

        // get values
        $this->get('key1')->shouldBe('value1');
        $this->get('array')->shouldBeArray();
        $this->get('array.hoge')->shouldBe('hoge');

        // post values
        $this->get('post_key1')->shouldBe('post_value1');

        // not contain cookie
        $this->get('cookie_key1')->shouldBeNull();

        // restore
        $_GET = $get;
        $_POST = $post;
        $_COOKIE = $cookie;
    }

    public function it_gets_header_value()
    {
        $this->setHeader('User-Agent', 'samurai/spec');
        $this->getHeader('user-agent')->shouldBe('samurai/spec');
    }

    public function it_gets_http_method()
    {
        $this->getMethod()->shouldBe('GET');

        // request override
        $this->set('_method', 'DELETE');
        $this->getMethod()->shouldBe('DELETE');
    }

    public function it_gets_http_version()
    {
        $this->getHttpVersion()->shouldBe('1.0');

        // backup
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : null;

        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $this->getHttpVersion()->shouldBe('1.1');

        // restore.
        $_SERVER['SERVER_PROTOCOL'] = $protocol;
    }

    /**
     * path is "/foo/bar/zoo" of "http://example.jp/foo/bar/zoo?a=b&c=d"
     */
    public function it_get_path(ArrayUtility $u)
    {
        $this->raikiri()->register('arrayUtil', $u);
        $u->merge([], [])->willReturn([]);

        // backup
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

        $_SERVER['REQUEST_URI'] = '/foo/bar/zoo?a=b&c=d';
        $this->init();
        $this->getPath()->shouldBe('/foo/bar/zoo');

        // restore.
        $_SERVER['REQUEST_URI'] = $uri;
    }
}

