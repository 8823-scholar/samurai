<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class DatabaseSpec extends PHPSpecContext
{
    public function let()
    {
        $config = [
            'driver' => 'mysql',
            'user' => 'who',
            'pass' => 'am i.',
            'host' => 'localhost.localdomain',
            'database' => 'bar',
            'port' => 3306,
            'slaves' => [
                [
                    'host' => 'slave1.localdomain',
                    'weight' => 2,
                ],
                [
                    'host' => 'slave2.localdomain',
                    'weight' => 1,
                ],
            ],
        ];
        $this->beConstructedWith($config);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Database');
    }


    public function it_gets_user()
    {
        $this->getUser()->shouldBe('who');
    }

    public function it_sets_user()
    {
        $this->setUser('who fighters');
        $this->getUser()->shouldBe('who fighters');
    }

    public function it_gets_password()
    {
        $this->getPassword()->shouldBe('am i.');
    }

    public function it_sets_password()
    {
        $this->setPassword('gogogogo');
        $this->getPassword()->shouldBe('gogogogo');
    }

    public function it_gets_hostname()
    {
        $this->getHostName()->shouldBe('localhost.localdomain');
    }

    public function it_sets_hostname()
    {
        $this->setHostName('localhost');
        $this->getHostName()->shouldBe('localhost');
    }

    public function it_gets_port()
    {
        $this->getPort()->shouldBe(3306);
    }

    public function it_sets_port()
    {
        $this->setPort(3307);
        $this->getPort()->shouldBe(3307);
    }

    public function it_gets_database_name()
    {
        $this->getDatabaseName()->shouldBe('bar');
    }

    public function it_sets_database_name()
    {
        $this->setDatabaseName('zoo');
        $this->getDatabaseName()->shouldBe('zoo');
    }

    public function it_adds_slave()
    {
        $this->addSlave(['host' => 'localhost2.localdomain']);
        $this->getSlaves()->shouldHaveCount(3);
    }

    public function it_gets_all_slaves()
    {
        $slaves = $this->getSlaves();
        $slaves[0]->getHostName()->shouldBe('slave1.localdomain');
        $slaves[1]->getHostName()->shouldBe('slave2.localdomain');
    }

    public function it_picks_slave()
    {
        $slave = $this->pickSlave();
        $slave->shouldHaveType('Samurai\Onikiri\Database');
        $slave->isSlave()->shouldBe(true);
    }

    public function it_picks_slave_but_not_have_slaves()
    {
        $this->clearSlaves();

        $slave = $this->pickSlave();
        $slave->shouldBe($this);
    }

    public function it_picked_slave_inherit_from_master()
    {
        $slave = $this->pickSlave();

        $slave->getUser()->shouldBe('who');
        $slave->getPassword()->shouldBe('am i.');
        $slave->getPort()->shouldBe(3306);
        $slave->getDatabaseName()->shouldBe('bar');
    }

    public function it_has_slaves()
    {
        $this->hasSlave()->shouldBe(true);
    }

    public function it_is_slave()
    {
        $slave = $this->pickSlave();
        $slave->isSlave()->shouldBe(true);
    }
}

