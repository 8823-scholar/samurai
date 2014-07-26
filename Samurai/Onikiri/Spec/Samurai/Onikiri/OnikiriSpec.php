<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class OnikiriSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Onikiri');
    }

    public function it_configurates()
    {
        $config = $this->configure();
        $config->shouldHaveType('Samurai\Onikiri\Configuration');
    }
    
    public function it_imports_database_configuration_file()
    {
        $config = __DIR__ . '/Fixtures/databases.yml';
        $this->import($config);

        $database = $this->getDatabase('base');
        $database->getUser()->shouldBe('some');
        $database->getHostName()->shouldBe('localhost.localdomain');
    }

    public function it_gets_database_instance()
    {
        $config = __DIR__ . '/Fixtures/databases.yml';
        $this->import($config);

        $database = $this->getDatabase('admin');
        $database->shouldHaveType('Samurai\Onikiri\Database');
        $database->getHostName()->shouldBe('admin.localdomain');
    }

    public function it_gets_table_instance()
    {
        $config = $this->configure();
        $config->addModelDir(__DIR__ . '/Fixtures', __NAMESPACE__ . '\\Fixtures');

        $userTable = $this->getTable('User');
        $userTable->shouldHaveType(__NAMESPACE__ . '\\Fixtures\\UserTable');
    }

    public function it_throws_entity_table_not_found_exception_when_not_exists_alias()
    {
        $config = $this->configure();
        $config->addModelDir(__DIR__ . '/Fixtures', __NAMESPACE__ . '\\Fixtures');

        $this->shouldThrow('Samurai\\Onikiri\\Exception\\EntityTableNotFoundException')->duringGetTable('Foo');
    }


    public function it_gets_transaction()
    {
        $this->getTx()->shouldHaveType('Samurai\Onikiri\Transaction');
    }
}

