<?php

namespace Samurai\Samurai\Spec\Component\Core;

use Samurai\Samurai\Component\Core\Accessor as CoreAccessor;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class AccessorSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beAnInstanceOf('Samurai\Samurai\Spec\Component\Core\Accessor');
    }

    public function it_is_not_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Spec\Component\Core\Accessor');
    }

    public function it_is_enable_to_get_proptery()
    {
        $this->getFoo()->shouldBe($this->foo);
        $this->getFoo_bar()->shouldBe($this->foo_bar);
    }

    public function it_is_enable_to_get_proptery_use_camelcase()
    {
        $this->getFooBar()->shouldBe($this->foo_bar);
    }

    public function it_is_enable_to_set_proptery()
    {
        $this->setFoo(3);
        $this->getFoo()->shouldBe(3);

        $this->setFoo_bar(4);
        $this->getFooBar()->shouldBe(4);
    }

    public function it_is_enable_to_set_proptery_use_camelcase()
    {
        $this->setFooBar(5);
        $this->getFooBar()->shouldBe(5);
    }
}


/**
 * dummy accessor class.
 */
class Accessor
{
    use CoreAccessor;

    public $foo = 1;

    public $foo_bar = 2;
}

