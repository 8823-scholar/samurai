<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Criteria;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\EntityTable;

class CriteriaSpec extends PHPSpecContext
{
    public function let(EntityTable $t)
    {
        $t->getTableName()->willReturn('foo');
        $this->beConstructedWith($t);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Criteria\Criteria');
    }

    public function it_set_entity_table(EntityTable $t)
    {
        $this->setTable($t);
        $this->getTable()->shouldBe($t);
    }


    public function it_converts_to_insert_sql()
    {
        $this->toInsertSQL(['name' => 'Satoshinosuke', 'lover' => 'Minka'])
            ->shouldBe("INSERT INTO foo (name, lover) VALUES (?, ?);");
        $this->getParams()->shouldBe(['Satoshinosuke', 'Minka']);
    }
}

