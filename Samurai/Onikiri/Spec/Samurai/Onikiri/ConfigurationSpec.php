<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Mapping\DefaultNamingStrategy;

class ConfigurationSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Configuration');
    }

    public function it_adds_model_directory()
    {
        $this->addModelDir(__DIR__ . '/Fixtures', __NAMESPACE__ . '\\Fixtures');
        $this->addModelDir(__DIR__ . '/Fixtures2', __NAMESPACE__ . '\\Fixtures2');
        $this->getModelDirs()->shouldBe([
            ['dir' => __DIR__ . '/Fixtures', 'namespace' => __NAMESPACE__ . '\\Fixtures'],
            ['dir' => __DIR__ . '/Fixtures2', 'namespace' => __NAMESPACE__ . '\\Fixtures2'],
        ]);
    }


    public function it_sets_naming_strategy()
    {
        $this->setNamingStrategy(new DefaultNamingStrategy());
        $this->getNamingStrategy()->shouldHaveType('Samurai\Onikiri\Mapping\DefaultNamingStrategy');
    }
}

