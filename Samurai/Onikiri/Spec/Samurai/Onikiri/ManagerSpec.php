<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ManagerSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Manager');
    }


    public function it_imports_database_configuration_file()
    {
        $config = __DIR__ . '/Fixtures/databases.yml';
        $this->import($config);

        $database = $this->getDatabase('base');
        $database->getUser()->shouldBe('some');
        $database->getHostName()->shouldBe('localhost.localdomain');
    }


    public function it_gets_database()
    {
        $config = __DIR__ . '/Fixtures/databases.yml';
        $this->import($config);

        $database = $this->getDatabase('admin');
        $database->shouldHaveType('Samurai\Onikiri\Database');
    }


    public function it_gets_driver()
    {
        $driver = $this->getDriver('mysql');
        $driver->shouldHaveType('Samurai\Onikiri\Driver\MysqlDriver');
        
        $driver = $this->getDriver('sqlite');
        $driver->shouldHaveType('Samurai\Onikiri\Driver\SqliteDriver');
        
        $driver = $this->getDriver('pgsql');
        $driver->shouldHaveType('Samurai\Onikiri\Driver\PgsqlDriver');
    }

    public function it_throws_exception_when_no_exists_driver()
    {
        $this->shouldThrow('InvalidArgumentException')->duringGetDriver('whosql');
    }
}

