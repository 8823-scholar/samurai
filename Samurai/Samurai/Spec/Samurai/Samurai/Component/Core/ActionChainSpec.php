<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Core;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Application;

class ActionChainSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Core\ActionChain');
    }


    public function it_add_controller()
    {
        $this->addAction('example', 'foo');
        $this->actions->shouldBe(array(
            ['controller' => null, 'controller_name' => 'example', 'action' => 'foo', 'result' => null]
        ));
    }

    public function it_add_controller_contain_action()
    {
        $this->addAction('example.bar');
        $this->actions->shouldBe(array(
            ['controller' => null, 'controller_name' => 'example', 'action' => 'bar', 'result' => null]
        ));
    }

    public function it_gets_controller(Application $a)
    {
        $a->config('controller.namespaces')->willReturn(['Samurai\\Console', 'Samurai\\Samurai']);
        $this->getContainer()->register('application', $a);

        $controller = $this->getController('spec');
        $controller->shouldHaveType('Samurai\Console\Controller\SpecController');
    }

    public function it_throws_notfound_exception_when_unexisting()
    {
        $this->shouldThrow('Samurai\Samurai\Exception\NotFoundException')->duringGetController('unexisting');
    }


    public function it_gets_current_action(Application $a)
    {
        $a->config('controller.namespaces')->willReturn(['Samurai\\Console', 'Samurai\\Samurai']);
        $this->getContainer()->register('application', $a);

        $this->addAction('spec.execute');
        $this->getCurrentAction()->shouldBe(
            ['controller' => $this->actions[0]['controller'], 'controller_name' => 'spec', 'action' => 'execute', 'result' => null]
        );
    }


    public function it_stepups_next_seaquence()
    {
        $this->addAction('spec.execute');
        $this->addAction('spec.execute2');
        $this->position->shouldBe(0);
        $this->next();
        $this->position->shouldBe(1);
    }


    public function it_loops_action_chain(Application $a)
    {
        $a->config('controller.namespaces')->willReturn(['Samurai\\Console', 'Samurai\\Samurai']);
        $this->getContainer()->register('application', $a);

        $this->addAction('utility.execute');
        $this->addAction('spec.execute');
        while (($action = $this->getCurrentAction()) && $action->getWrappedObject()) {
            $this->next();
        }
    }


    public function it_gets_error_list_by_action_name()
    {
        $this->addAction('utility.execute');
        $this->getErrorListByName('utility.execute')->shouldHaveType('Samurai\Samurai\Component\Core\ErrorList');
    }
    
    public function it_gets_error_list_by_current_action()
    {
        $this->addAction('utility.execute');
        $this->addAction('spec.execute');

        $error1 = $this->getCurrentErrorList();
        $error2 = $this->getErrorListByName('utility.execute');
        $error1->shouldBe($error2);
    }


    public function it_is_action_name_strategy()
    {
        $this->actionNameStrategy('index')->shouldBe('index');
        $this->actionNameStrategy('foo_bar')->shouldBe('fooBar');
    }
}

