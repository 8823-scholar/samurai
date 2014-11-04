<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class TaskSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\Task');
    }
}

