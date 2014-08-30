<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Entity;
use Samurai\Onikiri\EntityTable;
use Samurai\Onikiri\Connection;

class EntitiesSpec extends PHPSpecContext
{
    public function let(EntityTable $e)
    {
        $fixtures = [
            ['id' => 1, 'name' => 'KIUCHI Satoshinosuke'],
            ['id' => 2, 'name' => 'Minka Lee'],
            ['id' => 3, 'name' => 'HUKADA Kyoko'],
            ['id' => 4, 'name' => 'HINAMI Kyoko'],
            ['id' => 5, 'name' => 'HATSUNE Miku'],
        ];
        foreach ($fixtures as $position => $fixture) {
            $this->add(new Entity($e->getWrappedObject(), $fixture, true));
        }
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Entities');
    }


    public function it_gets_entity_by_position()
    {
        $entity = $this->getByPosition(2);
        $entity->shouldHaveType('Samurai\Onikiri\Entity');
        $entity->getName()->shouldBe('HUKADA Kyoko');
        
        $entity = $this->getByPosition(10);
        $entity->shouldBe(null);
    }

    public function it_gets_first_entity()
    {
        $entity = $this->first();
        $entity->shouldHaveType('Samurai\Onikiri\Entity');
        $entity->getName()->shouldBe('KIUCHI Satoshinosuke');
    }

    public function it_is_fetch()
    {
        $entity = $this->fetch();
        $entity->getName()->shouldBe('KIUCHI Satoshinosuke');

        $entity = $this->fetch();
        $entity->getName()->shouldBe('Minka Lee');
    }


    public function it_is_foreachable()
    {
        $this->shouldImplement('Iterator');
        $current = $this->current();
        $current->shouldHaveType('Samurai\Onikiri\Entity');
    }


    public function it_is_enable_to_access_entity_method(Entity $e1, Entity $e2)
    {
        $e1->save()->shouldBeCalled();
        $e2->save()->shouldBeCalled();
        $this->add($e1);
        $this->add($e2);
        $this->save();
    }


    public function it_gets_col()
    {
        $this->col('id')->shouldBe([1,2,3,4,5]);
    }
}

