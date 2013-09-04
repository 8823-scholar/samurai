<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ProcessorSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\Processor');
    }


    public function it_gets_a_task()
    {
        $task = $this->get('add:spec');
        $task->shouldHaveType('Samurai\Console\Task\AddTask');
    }
}

