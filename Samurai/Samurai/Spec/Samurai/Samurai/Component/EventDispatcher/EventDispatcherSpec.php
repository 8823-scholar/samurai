<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\EventDispatcher;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\EventDispatcher\Event;
use Samurai\Samurai\Component\EventDispatcher\EventSubscriberInterface;

class EventDispatcherSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\EventDispatcher\EventDispatcher');
    }


    public function it_adds_listener()
    {
        $listener = function(){};
        $this->addListener('some', $listener);

        $this->getListners('some')->shouldBe([$listener]);
    }

    public function it_adds_listener_using_priority()
    {
        $listener1 = function(){};
        $this->addListener('some', $listener1, 0);
        
        $listener2 = function(){};
        $this->addListener('some', $listener2, 10);

        $this->getListners('some')->shouldBe([$listener2, $listener1]);
    }

    public function it_dispatches()
    {
        $listener = function($event) {
        };
        $this->addListener('some', $listener);

        $this->dispatch('some');
    }


    public function it_adds_subscriber(EventSubscriberInterface $s)
    {
        $s->getSubscribedEvents()->willReturn(
            [
                'event.1' => 'onEvent1',
                'event.2' => ['onEvent2', 1],
                'event.3' => [
                    'onEvent3_1',
                    ['onEvent3_2', 20],
                    ['onEvent3_3', 10],
                ]
            ]
        );

        $this->addSubscriber($s);

        $this->getListners('event.1')->shouldBe([
            [$s, 'onEvent1']
        ]);
        $this->getListners('event.2')->shouldBe([
            [$s, 'onEvent2']
        ]);
        $this->getListners('event.3')->shouldBe([
            [$s, 'onEvent3_2'],
            [$s, 'onEvent3_3'],
            [$s, 'onEvent3_1'],
        ]);
    }
}

