<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\Core\Loader;

class ProcessorSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $loader;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\Processor');
    }


    public function it_gets_a_task(Loader $l)
    {
        $l->getPathByClass('Task\\AddTaskList', false)->willReturn($this->loader->getPathByClass('Task\\AddTaskList', false));
        $this->raikiri()->register('loader', $l);

        $task = $this->get('add:spec');
        $task->shouldHaveType('Samurai\Console\Task\AddTaskList');
    }
}

