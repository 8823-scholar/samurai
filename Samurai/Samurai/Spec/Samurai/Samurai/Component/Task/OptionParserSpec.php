<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\Task\Option;

class OptionParserSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\OptionParser');
    }


    public function it_is_supports()
    {
        $this->isSupports('@option      long-name               description')->shouldBe(true);
        $this->isSupports('@option      long-name=default       description')->shouldBe(true);
        $this->isSupports('@option      long-name,v=default     description')->shouldBe(true);
        $this->isSupports('@require     long-name               description')->shouldBe(true);
        $this->isSupports('@param       string                  description')->shouldBe(false);
    }


    public function it_supports_basic_syntax()
    {
        $def = $this->parse('@option long-name description');
        $def->shouldHaveType('Samurai\Samurai\Component\Task\OptionDefine');
        $def->isRequired()->shouldBe(false);
        $def->getName()->shouldBe('long-name');
        $def->getDefault()->shouldBe(true);
        $def->getDescription()->shouldBe('description');
    }

    public function it_supports_has_default_value()
    {
        $def = $this->parse('@option long-name=value description');
        $def->getName()->shouldBe('long-name');
        $def->getDefault()->shouldBe('value');
        $def->getDescription()->shouldBe('description');
    }

    public function it_supports_has_short_name()
    {
        $def = $this->parse('@option long-name,l=value description');
        $def->getName()->shouldBe('long-name');
        $def->getShortName()->shouldBe('l');
        $def->hasShortName()->shouldBe(true);
        $def->getDefault()->shouldBe('value');
        $def->getDescription()->shouldBe('description');
    }

    public function it_supports_required_option()
    {
        $def = $this->parse('@require long-name description');
        $def->isRequired()->shouldBe(true);
        $def->getDefault()->shouldBe(null);
    }


    public function it_makes_usage_format(Option $option)
    {
        $option->getDefinitions()->willReturn([
            $this->parse('@option key1 description')->getWrappedObject(),
            $this->parse('@option key2=value2 description')->getWrappedObject(),
            $this->parse('@option key3,k3=value3 description')->getWrappedObject(),
            $this->parse('@require key4 description')->getWrappedObject(),
        ]);
        $expect = <<<EOL
--key1                          description
--key2=value2                   description
--key3,-k3=value3               description
--key4                          (required) description
EOL;
        $this->formatter($option)->shouldBe($expect);
    }
}

