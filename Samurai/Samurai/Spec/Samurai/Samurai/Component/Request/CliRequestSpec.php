<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Request;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class CliRequestSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Request\CliRequest');
    }

    public function it_initialize_argv_to_params()
    {
        // backup.
        $argv = $_SERVER['argv'];

        // sample
        $_SERVER['argv'] = ['./app', '--key1=value1', '--key2=value2', '--multi=1', '--multi=2'];

        $this->init();

        // get values
        $this->get('key1')->shouldBe('value1');
        $this->get('key2')->shouldBe('value2');
        $this->get('multi')->shouldBe('1');

        // get values as array
        $this->getAsArray('key1')->shouldBeArray();
        $this->getAsArray('multi')->shouldBeArray();
        $this->getAsArray('multi')->shouldBe(['1', '2']);

        // restore
        $_SERVER['argv'] = $argv;
    }

    public function it_gets_script_name()
    {
        // backup.
        $argv = $_SERVER['argv'];

        // sample
        $_SERVER['argv'] = ['./app', '--key1=value1', '--key2=value2', '--multi=1', '--multi=2'];

        $this->init();

        $this->getScriptName()->shouldBe('./app');

        // restore
        $_SERVER['argv'] = $argv;
    }
}

