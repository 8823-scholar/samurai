<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Spec;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class HelperSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Spec\Helper');
    }


    public function it_gets_runner()
    {
        $runner = $this->getRunner();
        $runner->shouldHaveType('Samurai\Samurai\Component\Spec\Runner\PHPSpecRunner');
    }
}

