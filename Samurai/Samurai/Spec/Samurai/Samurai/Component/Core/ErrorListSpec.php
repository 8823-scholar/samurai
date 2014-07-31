<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Core;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ErrorListSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Core\ErrorList');
    }


    public function it_sets_types()
    {
        $this->setType('auth');
        $this->setType('invalidInput');
        $this->hasType('auth')->shouldBe(true);
        $this->hasType('invalidInput')->shouldBe(true);
        $this->hasType('invalidDevice')->shouldBe(false);
    }

    public function it_gets_type()
    {
        $this->setType('auth');
        $this->setType('invalidInput');
        $this->getType()->shouldBe('invalidInput');
    }

    public function it_adds_error()
    {
        $this->add('foo', 'yes, i am.');
        $this->getMessage('foo')->shouldBe('yes, i am.');
    }

    public function it_gets_some_messages()
    {
        $this->add('foo', 'yes, i am.');
        $this->add('foo', 'hail 2 u.');
        $this->getMessages('foo')->shouldBe([
            'yes, i am.',
            'hail 2 u.'
        ]);
    }

    public function it_gets_all_message()
    {
        $this->add('foo', 'yes, i am.');
        $this->add('foo', 'hail 2 u.');
        $this->add('bar', 'ora ora ora!');
        $this->getAllMessage()->shouldBe([
            'foo' => 'yes, i am.',
            'bar' => 'ora ora ora!',
        ]);
    }
    
    public function it_gets_all_messages()
    {
        $this->add('foo', 'yes, i am.');
        $this->add('foo', 'hail 2 u.');
        $this->add('bar', 'ora ora ora!');
        $this->getAllMessages()->shouldBe([
            'yes, i am.',
            'hail 2 u.',
            'ora ora ora!',
        ]);
    }
}

