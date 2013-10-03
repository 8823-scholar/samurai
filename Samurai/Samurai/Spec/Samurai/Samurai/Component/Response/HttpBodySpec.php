<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Response;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class HttpBodySpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Response\HttpBody');
    }

    public function it_is_initializable_with_content()
    {
        $contents = <<<'EOL'
<html>
    <body>
        hoge
    </body>
</html>
EOL;
        $this->beConstructedWith($contents);
        $this->getContent()->shouldBe($contents);
    }


    public function it_sets_and_gets_content()
    {
        $contents = <<<'EOL'
<html>
    <body>
        hoge
    </body>
</html>
EOL;
        $this->setContent($contents);
        $this->getContent()->shouldBe($contents);
    }


    public function it_sets_and_gets_header()
    {
        $this->setHeader('content-type', 'text/plain');
        $this->setHeader('content-length', 123);
        $this->getHeader('content-type')->shouldBe('text/plain');

        // caps is ignore
        $this->getHeader('CONTENT-LENGTH')->shouldBe(123);

        // 2nd argument is default value.
        $this->getHeader('some-header', 'foo.bar.zoo')->shouldBe('foo.bar.zoo');
    }

    public function it_gets_all_headers()
    {
        $this->setHeader('content-type', 'text/plain');
        $this->setHeader('content-length', 123);

        $this->getHeaders()->shouldBe(['content-type' => 'text/plain', 'content-length' => 123]);
    }


    public function it_renders()
    {
        $contents = <<<'EOL'
<html>
    <body>
        hoge
    </body>
</html>
EOL;
        $this->setContent($contents);
        $this->setHeader('content-type', 'text/html');

        $body = $this->render();
        $body->shouldBe($contents);
    }
    
    public function it_renders_with_headers()
    {
        $contents = <<<'EOL'
<html>
    <body>
        hoge
    </body>
</html>
EOL;
        $this->setContent($contents);
        $this->setHeader('content-type', 'text/html');

        $length = strlen($contents);
        $expected = <<<EOL
Content-Length: {$length}
Content-Type: text/html

{$contents}
EOL;

        $body = $this->render(true);
        $body->shouldBe($expected);
    }
}

