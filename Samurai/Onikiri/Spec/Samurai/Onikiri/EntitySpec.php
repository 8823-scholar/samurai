<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Model;

class EntitySpec extends PHPSpecContext
{
    public function let(Model $m)
    {
        $m->getPrimaryKey()->willReturn('id');
        $this->beConstructedWith($m, ['id' => 11, 'name' => 'KIUCHI Satoshinosuke', 'gender' => 'male', 'some_key' => 'foobarzoo']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Entity');
    }


    public function it_gets_each_attributes_by_property()
    {
        $this->id->shouldBe(11);
        $this->name->shouldBe('KIUCHI Satoshinosuke');
        $this->gender->shouldBe('male');
        $this->some_key->shouldBe('foobarzoo');
    }

    public function it_gets_each_attributes_by_getter()
    {
        $this->getId()->shouldBe(11);
        $this->getName()->shouldBe('KIUCHI Satoshinosuke');
        $this->getGender()->shouldBe('male');
        $this->getSomeKey()->shouldBe('foobarzoo');
    }

    public function it_sets_each_attributes_by_property()
    {
        $this->name = 'Nanashi no Gonbe';
        $this->birthday = '1983-09-07';

        $this->name->shouldBe('Nanashi no Gonbe');
        $this->birthday->shouldBe('1983-09-07');
    }

    public function it_sets_each_attributes_by_setter()
    {
        $this->setName('Minka Lee');
        $this->setGender('female');

        $this->getName()->shouldBe('Minka Lee');
        $this->getGender()->shouldBe('female');
    }


    public function it_gets_primary_value()
    {
        $this->getPrimaryValue()->shouldBe(11);
    }

    public function it_sets_primary_value(Model $m)
    {
        $this->setPrimaryValue(13);
        $this->getPrimaryValue()->shouldBe(13);
    }


    public function it_is_new(Model $m)
    {
        $this->beConstructedWith($m, $this->getAttributes()->getWrappedObject(), false);
        $this->isNew()->shouldBe(true);

        $this->beConstructedWith($m, $this->getAttributes()->getWrappedObject(), true);
        $this->isNew()->shouldBe(false);
    }


    public function it_converts_to_array()
    {
        $this->toArray()->shouldBe($this->getAttributes());
    }


    public function it_saves(Model $m)
    {
        $m->save($this, [])->shouldBeCalled();
        $this->save();
    }

    public function it_saves_with_attributes(Model $m)
    {
        $m->save($this, ['name' => 'Minka Lee'])->shouldBeCalled();
        $this->save(['name' => 'Minka Lee']);
    }


    public function it_destroies(Model $m)
    {
        $m->destroy($this)->shouldBeCalled();
        $this->destroy();
    }


    public function it_calls_no_exists_method()
    {
        $this->shouldThrow('LogicException')->duringDoSomething();
    }
}

