<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Spec\Context;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class PHPSpecContextSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Spec\Context\PHPSpecContext');
        $this->shouldHaveType('PhpSpec\ObjectBehavior');
    }
}

