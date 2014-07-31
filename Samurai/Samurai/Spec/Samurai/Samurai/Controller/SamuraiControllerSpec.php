<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Controller;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Application;
use Samurai\Samurai\Component\Core\Loader;
use Samurai\Raikiri\Container;
use Samurai\Onikiri\Onikiri;

class SamuraiControllerSpec extends PHPSpecContext
{
    public function let(Container $c, Application $a, Onikiri $o, Loader $l)
    {
        $this->setContainer($c);
        $c->has('application')->willReturn(true);
        $c->get('application')->willReturn($a);
        $c->has('onikiri')->willReturn(true);
        $c->get('onikiri')->willReturn($o);
        $c->has('loader')->willReturn(true);
        $c->get('loader')->willReturn($l);
        $o->getTable('User')->willReturn(new Fixtures\UserTable($o->getWrappedObject()));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Controller\SamuraiController');
    }


    public function it_gets_entity_table()
    {
        $model = $this->getTable('User');
        $model->shouldHaveType('Samurai\Samurai\Spec\Samurai\Samurai\Controller\Fixtures\UserTable');
    }

    public function it_gets_onikiri()
    {
        $this->onikiri()->shouldHaveType('Samurai\Onikiri\Onikiri');
    }

    public function it_gets_raikiri_container()
    {
        $this->raikiri()->shouldHaveType('Samurai\Raikiri\Container');
    }


    public function it_gets_filters(Loader $l)
    {
        /**
         * Controller/SomeController.php
         *   - Controller/filter.yml
         *   - Controller/some.filter.yml
         *
         * Controller/Foo/BarController.php
         *   - Controller/filter.yml
         *   - Controller/Foo/filter.yml
         *   - Controller/Foo/bar.filter.yml
         */
        $this->setName('some');
    }
}

