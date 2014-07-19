<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Driver;

use Samurai\Onikiri\Database;
use PhpSpec\Exception\Example\SkippingException;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class PgsqlDriverSpec extends PHPSpecContext
{
    public $Request;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Driver\PgsqlDriver');
    }
    
    
    public function it_connects_to_pgsql(Database $d)
    {
        $user = $this->Request->getEnv('ONIKIRI_SPEC_PGSQL_USER');
        $pass = $this->Request->getEnv('ONIKIRI_SPEC_PGSQL_PASS', '');
        $host = $this->Request->getEnv('ONIKIRI_SPEC_PGSQL_HOST', 'localhost');
        $port = $this->Request->getEnv('ONIKIRI_SPEC_PGSQL_PORT', 5432);
        $database = $this->Request->getEnv('ONIKIRI_SPEC_PGSQL_DATABASE');
        if (! $user) throw new SkippingException('Set env "ONIKIRI_SPEC_PGSQL_USER"');
        if (! $host) throw new SkippingException('Set env "ONIKIRI_SPEC_PGSQL_HOST"');
        if (! $port) throw new SkippingException('Set env "ONIKIRI_SPEC_PGSQL_PORT"');
        if (! $database) throw new SkippingException('Set env "ONIKIRI_SPEC_PGSQL_DATABASE"');

        $d->getUser()->willReturn($user);
        $d->getPassword()->willReturn($pass);
        $d->getHostName()->willReturn($host);
        $d->getPort()->willReturn($port);
        $d->getDatabaseName()->willReturn($database);
        $d->getOptions()->willReturn([]);

        $connection = $this->connect($d);
        $connection->shouldHaveType('Samurai\Onikiri\Connection');
        $connection->shouldHaveType('PDO');
    }
}

