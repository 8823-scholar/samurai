<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Routing;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Raikiri\Container;
use Samurai\Samurai\Component\Request\HttpRequest;
use Samurai\Samurai\Component\Core\ActionChain;

class RouterSpec extends PHPSpecContext
{
    public function let(HttpRequest $r, ActionChain $a)
    {
        $this->setRoot('default.index');

        $c = $this->getContainer();
        $c->register('request', $r);
        $c->register('actionChain', $a);

        $r->getAll()->willReturn([]);    
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Routing\Router');
    }


    public function it_is_requested_root(HttpRequest $r, ActionChain $a)
    {
        $r->getPath()->willReturn('/');
        $a->existsController('default', 'index')->willReturn(true);

        $route = $this->routing();
        $route->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\RootRule');

        $route->getController()->shouldBe('default');
        $route->getAction()->shouldBe('index');
    }

    public function it_is_requested_no_match(HttpRequest $r)
    {
        $r->getPath()->willReturn('/favicon.ico');

        $route = $this->routing();
        $route->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\NotFoundRule');

        $route->getController()->shouldBe('error');
        $route->getAction()->shouldBe('notFound');
    }
    
    public function it_is_requested_controller_not_exists(HttpRequest $r, ActionChain $a)
    {
        $r->getPath()->willReturn('/user/show');
        $a->existsController('user', 'show')->willReturn(false);

        $route = $this->routing();
        $route->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\Rule');

        $route->getController()->shouldBe('error');
        $route->getAction()->shouldBe('notFound');
    }
}

