<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Onikiri;
use Samurai\Onikiri\EntityTable;
use Samurai\Onikiri\Transaction;

class EntitySpec extends PHPSpecContext
{
    public function let(EntityTable $t, Onikiri $o)
    {
        $t->getPrimaryKey()->willReturn('id');
        $this->beConstructedWith($t, ['id' => 11, 'name' => 'KIUCHI Satoshinosuke', 'gender' => 'male',
                                        'some_key' => 'foobarzoo', 'address1_name' => 'Meguroku']);

        $t->onikiri()->willReturn($o);
        $o->getTable('User')->willReturn(new Fixtures\UserTable($o->getWrappedObject()));
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
        $this->address1_name->shouldBe('Meguroku');
    }

    public function it_gets_each_attributes_by_getter()
    {
        $this->getId()->shouldBe(11);
        $this->getName()->shouldBe('KIUCHI Satoshinosuke');
        $this->getGender()->shouldBe('male');
        $this->getSomeKey()->shouldBe('foobarzoo');
        $this->getAddress1Name()->shouldBe('Meguroku');
    }

    public function it_gets_each_attributes_by_name_method()
    {
        $this->id()->shouldBe(11);
        $this->name()->shouldBe('KIUCHI Satoshinosuke');
        $this->gender()->shouldBe('male');
        $this->some_key()->shouldBe('foobarzoo');
        $this->address1_name()->shouldBe('Meguroku');
    }

    public function it_sets_each_attributes_by_property()
    {
        $this->name = 'Nanashi no Gonbe';
        $this->birthday = '1983-09-07';
        $this->address1_name = 'Suginamiku';

        $this->name->shouldBe('Nanashi no Gonbe');
        $this->birthday->shouldBe('1983-09-07');
        $this->address1_name->shouldBe('Suginamiku');
    }

    public function it_sets_each_attributes_by_setter()
    {
        $this->setName('Minka Lee');
        $this->setGender('female');
        $this->setAddress1Name('Suginamiku');

        $this->getName()->shouldBe('Minka Lee');
        $this->getGender()->shouldBe('female');
        $this->getAddress1Name()->shouldBe('Suginamiku');
    }

    public function it_has_attribute()
    {
        $this->hasAttribute('name')->shouldBe(true);
        $this->hasAttribute('address1_name')->shouldBe(true);
        $this->hasAttribute('aaaaaaa')->shouldBe(false);
    }


    public function it_gets_primary_value()
    {
        $this->getPrimaryValue()->shouldBe(11);
    }

    public function it_sets_primary_value()
    {
        $this->setPrimaryValue(13);
        $this->getPrimaryValue()->shouldBe(13);
    }


    public function it_is_exists(EntityTable $t)
    {
        $this->beConstructedWith($t, $this->getAttributes()->getWrappedObject(), false);
        $this->isExists()->shouldBe(false);

        $this->beConstructedWith($t, $this->getAttributes()->getWrappedObject(), true);
        $this->isExists()->shouldBe(true);
    }


    public function it_converts_to_array()
    {
        $this->toArray()->shouldBe($this->getAttributes());
    }

    public function it_saves(EntityTable $t)
    {
        $t->save($this, [])->shouldBeCalled();
        $this->save();
    }

    public function it_saves_with_attributes(EntityTable $t)
    {
        $t->save($this, ['name' => 'Minka Lee'])->shouldBeCalled();
        $this->save(['name' => 'Minka Lee']);
    }


    public function it_destroies(EntityTable $t)
    {
        $t->destroy($this)->shouldBeCalled();
        $this->destroy();
    }


    public function it_calls_no_exists_method()
    {
        $this->shouldThrow('LogicException')->duringDoSomething();
    }


    public function it_gets_table()
    {
        $this->getTable()->shouldHaveType('Samurai\Onikiri\EntityTable');
    }

    public function it_gets_table_tagetted_name()
    {
        $this->getTable('User')->shouldHaveType('Samurai\Onikiri\Spec\Samurai\Onikiri\Fixtures\UserTable');
    }


    public function it_gets_and_sets_transaction(EntityTable $t, Transaction $tx)
    {
        $t->setTx($tx)->shouldBeCalled();
        $t->getTx()->willReturn($tx);

        $this->setTx($tx);
        $this->getTx()->shouldBe($tx);
    }
}

