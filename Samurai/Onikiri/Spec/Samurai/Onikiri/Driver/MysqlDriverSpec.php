<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Driver;

use Samurai\Onikiri\Database;
use PhpSpec\Exception\Example\SkippingException;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class MysqlDriverSpec extends PHPSpecContext
{
    public function it_is_initializable(Database $d)
    {
        $this->shouldHaveType('Samurai\Onikiri\Driver\MysqlDriver');
    }


    public function it_connects_to_mysql(Database $d)
    {
        $this->_attachDatabase($d);
        $connection = $this->connect($d);
        $connection->shouldHaveType('Samurai\Onikiri\Connection');
    }


    public function it_gets_table_describe(Database $d)
    {
        $this->_attachDatabase($d);
        $connection = $this->connect($d);
        $connection = $connection->getWrappedObject();
        
        $sql = file_get_contents(__DIR__ . '/../Fixtures/create.tables.entitytable.sql');
        $stmt = $connection->query($sql);
        unset($stmt);

        $table_name = 'spec_samurai_onikiri_fullstack_table';
        $describe = $this->getTableDescribe($connection, $table_name);
        $describe['id']->shouldBe([
            'table' => $table_name,
            'name' => 'id',
            'type' => 'int',
            'length' => '11',
            'attribute' => 'unsigned',
            'null' => false,
            'primary_key' => true,
            'default' => null,
            'extras' => ['auto_increment'],
        ]);
        $describe['name']->shouldBe([
            'table' => $table_name,
            'name' => 'name',
            'type' => 'varchar',
            'length' => '256',
            'attribute' => null,
            'null' => false,
            'primary_key' => false,
            'default' => 'who',
            'extras' => [],
        ]);
        $describe['introduction']->shouldBe([
            'table' => $table_name,
            'name' => 'introduction',
            'type' => 'text',
            'length' => null,
            'attribute' => null,
            'null' => false,
            'primary_key' => false,
            'default' => null,
            'extras' => [],
        ]);
        $describe['gender']->shouldBe([
            'table' => $table_name,
            'name' => 'gender',
            'type' => 'enum',
            'length' => "'male','female'",
            'attribute' => null,
            'null' => false,
            'primary_key' => false,
            'default' => 'male',
            'extras' => [],
        ]);
    }


    private function _attachDatabase(Database $d)
    {
        $request = $this->__getContainer()->get('request');

        $user = $request->getEnv('ONIKIRI_SPEC_MYSQL_USER');
        $pass = $request->getEnv('ONIKIRI_SPEC_MYSQL_PASS', '');
        $host = $request->getEnv('ONIKIRI_SPEC_MYSQL_HOST', 'localhost');
        $port = $request->getEnv('ONIKIRI_SPEC_MYSQL_PORT', 3306);
        $database = $request->getEnv('ONIKIRI_SPEC_MYSQL_DATABASE');
        if (! $user) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_USER"');
        if (! $host) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_HOST"');
        if (! $port) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_PORT"');
        if (! $database) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_DATABASE"');

        $d->getUser()->willReturn($user);
        $d->getPassword()->willReturn($pass);
        $d->getHostName()->willReturn($host);
        $d->getPort()->willReturn($port);
        $d->getDatabaseName()->willReturn($database);
        $d->getOptions()->willReturn([]);
    }
}

