<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Driver;

use Samurai\Onikiri\Database;
use PhpSpec\Exception\Example\SkippingException;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class PgsqlDriverSpec extends PHPSpecContext
{
    public $application;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Driver\PgsqlDriver');
    }
    
    
    public function it_connects_to_pgsql(Database $d)
    {
        if (! $this->application->config('spec.pgsql.database.defined'))
            throw new SkippingException('Set env "ONIKIRI_SPEC_PGSQL_(USER|PASS|HOST|PORT|DATABASE)"');

        $d->getUser()->willReturn($this->application->config('spec.pgsql.database.user'));
        $d->getPassword()->willReturn($this->application->config('spec.pgsql.database.pass'));
        $d->getHostName()->willReturn($this->application->config('spec.pgsql.database.host'));
        $d->getPort()->willReturn($this->application->config('spec.pgsql.database.port'));
        $d->getDatabaseName()->willReturn($this->application->config('spec.pgsql.database.name'));
        $d->getOptions()->willReturn([]);

        $connection = $this->connect($d);
        $connection->shouldHaveType('Samurai\Onikiri\Connection');
    }
}

