<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\Request\CliRequest;

class TaskSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\Task');
    }


    public function it_converts_options_from_request(CliRequest $request)
    {
        $request->getAll()->willReturn(['help' => true, 'foo' => 'bar']);
        $this->request2Options($request);
        $this->getOption('help')->shouldBe(true);
        $this->getOption('foo')->shouldBe('bar');
    }
}

