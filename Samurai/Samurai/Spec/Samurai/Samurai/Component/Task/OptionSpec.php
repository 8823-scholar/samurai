<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\Request\CliRequest;

class OptionSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\Option');
    }

    public function it_imports_from_array()
    {
        $option = ['help' => true, 'foo' => 'bar'];
        $this->importFromArray($option);
        $this->get('help')->shouldBe(true);
        $this->get('foo')->shouldBe('bar');
    }

    public function it_imports_from_request(CliRequest $request)
    {
        $request->getAll()->willReturn(['help' => true, 'foo' => 'bar']);
        $this->importFromRequest($request);
        $this->get('help')->shouldBe(true);
        $this->get('foo')->shouldBe('bar');
    }
}

