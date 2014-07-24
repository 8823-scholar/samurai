<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Onikiri;
use Samurai\Onikiri\Database;
use Samurai\Onikiri\Connection;

class EntityTableSpec extends PHPSpecContext
{
    public function let(Onikiri $oni)
    {
        $this->beConstructedWith($oni);
        $oni->establishConnection()->willReturn('Samurai\Onikiri\Connection');
    }

    public function it_gets_onikiri_instance()
    {
        $this->getOnikiri()->shouldHaveType('Samurai\Onikiri\Onikiri');
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

    public function it_gets_database()
    {
        $this->getDatabase()->shouldBe('base');
    }

    public function it_is_enable_to_change_database()
    {
        $this->setDatabase('world1');
        $this->getDatabase()->shouldBe('world1');
    }


    public function it_gets_criteria()
    {
        $criteria = $this->criteria();
        $criteria->shouldHaveType('Samurai\Onikiri\Criteria\Criteria');
        $criteria->getTable()->shouldBe($this);
    }


    public function it_builds_blank_entity()
    {
        $entity = $this->build();
        $entity->shouldHaveType('Samurai\Onikiri\Entity');
        
        $entity = $this->build(['name' => 'foo', 'mail' => 'foo@example.com']);
        $entity->getName()->shouldBe('foo');
        $entity->getMail()->shouldBe('foo@example.com');
    }


    public function it_establishes_conection_to_database(Onikiri $oni, Connection $c)
    {
        $oni->establishConnection($this->getDatabase(), Database::TARGET_AUTO)->shouldBeCalled()->willReturn($c);

        $con = $this->establishConnection();
        $con->shouldHaveType('Samurai\Onikiri\Connection');
    }

    public function it_is_most_standard_simple_query_execute()
    {
    }


    /*
    public function it_creates_record()
    {
    }
     */


    /*
    public function it_finds_first_record()
    {
    }
     */
}

