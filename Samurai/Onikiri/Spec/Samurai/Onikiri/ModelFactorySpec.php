<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Manager;

class ModelFactorySpec extends PHPSpecContext
{
    public function let(Manager $m)
    {
        $model_spaces = [
            __DIR__ . '/Fixtures' => '\\Samurai\\Onikiri\\Spec\\Samurai\\Onikiri\\Fixtures',
        ];
        $m->getModelSpaces()->willReturn($model_spaces);

        $this->beConstructedWith($m);
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\ModelFactory');
    }


    public function it_creates_model()
    {
        $model = $this->create('Foo');
        $model->shouldHaveType('Samurai\Onikiri\Spec\Samurai\Onikiri\Fixtures\FooModel');
        $model->shouldHaveType('Samurai\Onikiri\Model');
    }

    public function it_gets_model()
    {
        $model1 = $this->get('Foo');
        $model2 = $this->get('Foo');
        $model1->shouldBe($model2);
    }
}

