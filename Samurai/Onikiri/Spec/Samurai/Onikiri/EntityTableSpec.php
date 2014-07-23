<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Onikiri;

class EntityTableSpec extends PHPSpecContext
{
    public function let(Onikiri $oni)
    {
        //$this->beConstructedWith($oni);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\EntityTable');
    }

    public function it_default_primary_key_is_id()
    {
        $this->getPrimaryKey()->shouldBe('id');
    }

    public function it_is_enable_to_change_primary_key()
    {
        $this->setPrimaryKey('hoge');
        $this->getPrimaryKey()->shouldBe('hoge');
    }

    public function it_default_table_name_is_generated_by_class_name()
    {
        $this->getTableName()->shouldBe('entity');
    }

    public function it_is_enable_to_change_table_name()
    {
        $this->setTableName('hoge');
        $this->getTableName()->shouldBe('hoge');
    }

    public function it_gets_entity_class_name()
    {
        $this->getEntityClass()->shouldBe('Samurai\Onikiri\Entity');
    }

    public function it_is_enable_to_change_entity_class()
    {
        $this->setEntityClass('Samurai\Onikiri\Spec\Samurai\Onikiri\Hoge');
        $this->getEntityClass()->shouldBe('Samurai\Onikiri\Spec\Samurai\Onikiri\Hoge');
    }

    /*
    public function it_finds_first_record()
    {
    }
     */
}

