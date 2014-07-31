<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Filter;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class FilterSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Filter\Filter');
    }


    public function it_sets_attribute()
    {
        $this->setAttribute('enable', true);
        $this->getAttribute('enable')->shouldBe(true);
    }

    public function it_gets_attribute()
    {
        $this->setAttribute('attribute1', 'foo');
        $this->getAttribute('attribute1')->shouldBe('foo');
        $this->getAttribute('attribute2')->shouldBe(null);
        $this->getAttribute('attribute3', 'bar')->shouldBe('bar');
    }
}

