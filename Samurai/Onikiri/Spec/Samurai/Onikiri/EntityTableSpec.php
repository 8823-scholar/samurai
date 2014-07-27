<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Onikiri\Spec\PHPSpecContext;
use Samurai\Onikiri\Onikiri;
use Samurai\Onikiri\Database;
use Samurai\Onikiri\Connection;
use Samurai\Onikiri\Statement;
use Samurai\Onikiri\Transaction;

class EntityTableSpec extends PHPSpecContext
{
    public function let(Onikiri $oni, Connection $con)
    {
        $this->beConstructedWith($oni);
        $oni->establishConnection($this->getDatabase(), Database::TARGET_MASTER)->willReturn($con);
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
        $oni->establishConnection($this->getDatabase(), Database::TARGET_MASTER)->shouldBeCalled()->willReturn($c);

        $con = $this->establishConnection();
        $con->shouldHaveType('Samurai\Onikiri\Connection');
    }

    public function it_is_most_standard_simple_query_execute(Onikiri $oni)
    {
        $this->_setMySQLDatabase();
        $con = new Connection(
            $this->_spec_driver->makeDsn($this->_spec_database),
            $this->_spec_database->getUser(),
            $this->_spec_database->getPassword()
        );
        $oni->establishConnection($this->getDatabase(), Database::TARGET_MASTER)->willReturn($con);

        $sql = file_get_contents(__DIR__ . '/Fixtures/create.tables.entitytable.sql');
        $result = $con->query($sql);
        unset($result);

        $sql = "SELECT * FROM spec_samurai_onikiri_entity_table WHERE name = :name;";
        $params = [':name' => 'Satoshinosuke'];
        $result = $this->query($sql, $params);
        $result->shouldHaveType('Samurai\Onikiri\Statement');
        $result->fetch(\PDO::FETCH_OBJ)->name->shouldBe('Satoshinosuke');
        unset($con);
    }
    
    
    public function it_finds_first_record(Connection $con, Statement $stm)
    {
        $con->prepare('SELECT * FROM entity WHERE (id = ?) LIMIT ?')->willReturn($stm);
        $stm->execute()->shouldBeCalled();
        $stm->bindValue(0, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->bindValue(1, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->fetchAll(Connection::FETCH_ASSOC)->willReturn([
            ['name' => 'kaneda', 'mail' => 'kaneda@akira.jp']
        ]);

        $entity = $this->find(1);
        $entity->shouldHaveType('Samurai\Onikiri\Entity');
        $entity->getName()->shouldBe('kaneda');
    }

    public function it_finds_first_record_by_magicmethod(Connection $con, Statement $stm)
    {
        $con->prepare('SELECT * FROM entity WHERE (name = ?) LIMIT ?')->willReturn($stm);
        $stm->execute()->shouldBeCalled();
        $stm->bindValue(0, 'kaneda', Connection::PARAM_STR)->shouldBeCalled();
        $stm->bindValue(1, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->fetchAll(Connection::FETCH_ASSOC)->willReturn([
            ['name' => 'kaneda', 'mail' => 'kaneda@akira.jp']
        ]);

        $entity = $this->findByName('kaneda');
        $entity->shouldHaveType('Samurai\Onikiri\Entity');
        $entity->getName()->shouldBe('kaneda');
    }

    public function it_finds_all_records(Connection $con, Statement $stm)
    {
        $con->prepare('SELECT * FROM entity WHERE 1')->willReturn($stm);
        $stm->execute()->shouldBeCalled();
        $stm->fetchAll(Connection::FETCH_ASSOC)->willReturn([
            ['name' => 'kaneda', 'mail' => 'kaneda@akira.jp'],
            ['name' => 'tetsuo', 'mail' => 'tetsuo@akira.jp'],
        ]);

        $entities = $this->findAll();
        $entities->shouldHaveType('Samurai\Onikiri\Entities');
        $entities->size()->shouldBe(2);

        $entity = $entities->fetch();
        $entity->getName()->shouldBe('kaneda');
        $entity = $entities->fetch();
        $entity->getName()->shouldBe('tetsuo');
    }
    
    public function it_finds_all_records_by_magicmethod(Connection $con, Statement $stm)
    {
        $con->prepare('SELECT * FROM entity WHERE (name = ?)')->willReturn($stm);
        $stm->execute()->shouldBeCalled();
        $stm->bindValue(0, 'kaneda', Connection::PARAM_STR)->shouldBeCalled();
        $stm->fetchAll(Connection::FETCH_ASSOC)->willReturn([
            ['name' => 'kaneda', 'mail' => 'kaneda1@akira.jp'],
            ['name' => 'kaneda', 'mail' => 'kaneda2@akira.jp'],
        ]);

        $entities = $this->findAllByName('kaneda');
        $entities->shouldHaveType('Samurai\Onikiri\Entities');
        $entities->size()->shouldBe(2);

        $entity = $entities->fetch();
        $entity->getMail()->shouldBe('kaneda1@akira.jp');
        $entity = $entities->fetch();
        $entity->getMail()->shouldBe('kaneda2@akira.jp');
    }


    public function it_saves_exists_entity(Connection $con, Statement $stm)
    {
        $con->prepare('SELECT * FROM entity WHERE (id = ?) LIMIT ?')->willReturn($stm);
        $stm->execute()->shouldBeCalled();
        $stm->bindValue(0, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->bindValue(1, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->fetchAll(Connection::FETCH_ASSOC)->willReturn([
            ['id' => 1, 'name' => 'kaneda', 'mail' => 'kaneda1@akira.jp'],
        ]);

        $entity = $this->find(1);
        $entity->getName()->shouldBe('kaneda');


        $con->prepare('UPDATE entity SET name = ? WHERE (id = ?)')->willReturn($stm);
        $stm->bindValue(0, 'kaneda shotaro', Connection::PARAM_STR)->shouldBeCalled();
        $stm->bindValue(1, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->isSuccess()->willReturn(true);
        $this->save($entity, ['name' => 'kaneda shotaro']);
        $entity->getName()->shouldBe('kaneda shotaro');
    }
    
    public function it_saves_notexists_entity(Connection $con, Statement $stm)
    {
        $entity = $this->build(['name' => 'kaneda', 'mail' => 'kaneda@akira.jp']);
        $entity->getName()->shouldBe('kaneda');

        $con->prepare('INSERT INTO entity (name, mail) VALUES (?, ?)')->willReturn($stm);
        $stm->bindValue(0, 'kaneda', Connection::PARAM_STR)->shouldBeCalled();
        $stm->bindValue(1, 'kaneda@akira.jp', Connection::PARAM_STR)->shouldBeCalled();
        $stm->execute()->shouldBeCalled();
        $stm->isSuccess()->willReturn(true);
        $stm->lastInsertId()->willReturn(2);
        $this->save($entity);
        $entity->isExists()->shouldBe(true);
    }
    

    public function it_deletes_entity(Connection $con, Statement $stm)
    {
        $con->prepare('SELECT * FROM entity WHERE (id = ?) LIMIT ?')->willReturn($stm);
        $stm->execute()->shouldBeCalled();
        $stm->bindValue(0, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->bindValue(1, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->fetchAll(Connection::FETCH_ASSOC)->willReturn([
            ['id' => 1, 'name' => 'kaneda', 'mail' => 'kaneda1@akira.jp'],
        ]);

        $entity = $this->find(1);
        $entity->getName()->shouldBe('kaneda');


        $con->prepare('DELETE FROM entity WHERE (id = ?)')->willReturn($stm);
        $stm->bindValue(0, 1, Connection::PARAM_INT)->shouldBeCalled();
        $stm->isSuccess()->willReturn(true);
        $this->destroy($entity);
    }


    public function it_is_transaction_commit(Onikiri $oni)
    {
        $this->_setMySQLDatabase();
        $con = new Connection(
            $this->_spec_driver->makeDsn($this->_spec_database),
            $this->_spec_database->getUser(),
            $this->_spec_database->getPassword()
        );
        $oni->establishConnection($this->getDatabase(), Database::TARGET_MASTER)->willReturn($con);

        $sql = file_get_contents(__DIR__ . '/Fixtures/create.tables.entitytable.sql');
        $result = $con->query($sql);
        unset($result);

        $tx = new Transaction();
        $this->setTx($tx);
        $sql = "INSERT INTO spec_samurai_onikiri_entity_table (name, mail) VALUES (?, ?);";
        $params = ['Kaneda', 'kaneda@akira.jp'];
        $this->query($sql, $params);
        $tx->commit();

        $sql = "SELECT * FROM spec_samurai_onikiri_entity_table WHERE name = :name;";
        $params = [':name' => 'Kaneda'];
        $result = $this->query($sql, $params);
        $result->shouldHaveType('Samurai\Onikiri\Statement');
        $result->fetch(\PDO::FETCH_OBJ)->name->shouldBe('Kaneda');

        unset($con);
    }

    public function it_is_transaction_rollback(Onikiri $oni)
    {
        $this->_setMySQLDatabase();
        $con = new Connection(
            $this->_spec_driver->makeDsn($this->_spec_database),
            $this->_spec_database->getUser(),
            $this->_spec_database->getPassword()
        );
        $oni->establishConnection($this->getDatabase(), Database::TARGET_MASTER)->willReturn($con);

        $sql = file_get_contents(__DIR__ . '/Fixtures/create.tables.entitytable.sql');
        $result = $con->query($sql);
        unset($result);

        try {
            $tx = new Transaction();
            $this->setTx($tx);
            $sql = "INSERT INTO spec_samurai_onikiri_entity_table (name, mail) VALUES (?, ?);";
            $params = ['Kaneda', 'kaneda@akira.jp'];
            $this->query($sql, $params);

            $tx->rollback();
        } catch (\Samurai\Onikiri\Exception\TransactionFailedException $e) {
            $sql = "SELECT * FROM spec_samurai_onikiri_entity_table WHERE name = :name;";
            $params = [':name' => 'Kaneda'];
            $result = $this->query($sql, $params);
            $result->shouldHaveType('Samurai\Onikiri\Statement');
            $result->fetch(\PDO::FETCH_OBJ)->shouldBe(false);
        }
        unset($con);
    }
}

