<?php

namespace Samurai\Raikiri\Spec\Samurai\Raikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ContainerFactorySpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Raikiri\ContainerFactory');
    }


    public function it_creates_container()
    {
        $container = $this->create();
        $container->shouldHaveType('Samurai\Raikiri\Container');
    }

    public function it_creates_container_with_name()
    {
        $container = $this->create('spec');
        $container->getName()->shouldBe('spec');
    }

    public function it_gets_registed_container()
    {
        $container = $this->create('spec');
        $container2 = $this->get('spec');
        $container->shouldBe($container2);
    }
}

