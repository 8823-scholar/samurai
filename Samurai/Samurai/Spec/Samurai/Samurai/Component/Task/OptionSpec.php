<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\Request\CliRequest;
use Samurai\Samurai\Component\Task\OptionDefine;

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

    public function it_get_args()
    {
        $option = ['help' => true, 'foo' => 'bar', 'arg1', 'arg2'];
        $this->importFromArray($option);

        $this->getArgs()->shouldBeLike(['arg1', 'arg2']);
        $this->getArg(0)->shouldBe('arg1');
    }


    public function it_bredge_long_from_short_option()
    {
        $def = new OptionDefine();
        $def->name = 'help';
        $def->short_name = 'h';
        $this->addDefinition($def);

        $option = ['h' => true];
        $this->importFromArray($option);
        $this->get('help')->shouldBe(true);
    }


    public function it_validates()
    {
        $def1 = new OptionDefine();
        $def1->name = 'option1';
        
        $def2 = new OptionDefine();
        $def2->name = 'option2';
        $def2->short_name = 'op2';
        $def2->required();

        $this->addDefinition($def1);
        $this->addDefinition($def2);

        $this->importFromArray(['option1' => 'value1']);
        $this->shouldThrow('Samurai\Samurai\Component\Task\OptionRequiredException')->duringValidate();
        
        $this->importFromArray(['option1' => 'value1', 'op2' => true]);
        $this->shouldNotThrow('Samurai\Samurai\Component\Task\OptionRequiredException')->duringValidate();
    }
}

