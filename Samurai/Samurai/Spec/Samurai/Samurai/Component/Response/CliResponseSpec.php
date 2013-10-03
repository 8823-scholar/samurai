<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Response;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class CliResponseSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Response\CliResponse');
        $this->shouldHaveType('Samurai\Samurai\Component\Response\HttpResponse');
        $this->shouldImplement('Samurai\Samurai\Component\Response\Response');
    }
}

