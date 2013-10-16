<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Model;
use Samurai\Onikiri\Statement;
use Samurai\Onikiri\Connection;
use Samurai\Onikiri\Entity;

class EntitiesSpec extends PHPSpecContext
{
    public function let(Model $m, Statement $s)
    {
        $this->beConstructedWith($m, $s);
        
        $fixtures = [
            ['id' => 1, 'name' => 'KIUCHI Satoshinosuke'],
            ['id' => 2, 'name' => 'Minka Lee'],
            ['id' => 3, 'name' => 'HUKADA Kyoko'],
            ['id' => 4, 'name' => 'HINAMI Kyoko'],
            ['id' => 5, 'name' => 'HATSUNE Miku'],
        ];
        foreach ($fixtures as $position => $fixture) {
            $s->fetch(Connection::FETCH_ASSOC, Connection::FETCH_ORI_ABS, $position)->willReturn($fixture);
            $m->build($fixture, true)->willReturn(new Entity($m->getWrappedObject(), $fixture, true));
        }
        $s->fetch(Connection::FETCH_ASSOC, Connection::FETCH_ORI_ABS, $position + 1)->willReturn(null);
        $s->fetch(Connection::FETCH_ASSOC, Connection::FETCH_ORI_ABS, 10)->willReturn(null);
    }


    public function it_is_initializable(Model $m)
    {
        $this->shouldHaveType('Samurai\Onikiri\Entities');
        $this->model->shouldBe($m);
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


    public function it_is_foreachable()
    {
        $this->shouldImplement('Iterator');
        $current = $this->current();
        $current->shouldHaveType('Samurai\Onikiri\Entity');
    }
}

