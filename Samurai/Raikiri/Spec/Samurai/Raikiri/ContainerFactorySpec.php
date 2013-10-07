<?php

namespace Samurai\Raikiri\Spec\Samurai\Raikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ContainerFactorySpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Raikiri\ContainerFactory');
    }
}

