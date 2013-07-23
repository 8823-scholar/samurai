<?php

namespace App\Spec;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SampleSpec extends PHPSpecContext
{
    public function it_should_be_equal()
    {
        $this->sample()->shouldBe('a');
    }
}

