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
        $request = $this->__getContainer()->get('Request');

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

        $connection = $this->connect($d);
        $connection->shouldHaveType('Samurai\Onikiri\Connection');
    }
}

