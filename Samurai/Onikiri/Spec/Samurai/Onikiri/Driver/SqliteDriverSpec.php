<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Driver;

use Samurai\Onikiri\Database;
use PhpSpec\Exception\Example\SkippingException;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SqliteDriverSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Driver\SqliteDriver');
    }
    
    
    public function it_connects_to_sqlite(Database $d)
    {
        $d->getDatabaseName()->willReturn(':memory:');
        $d->getOptions()->willReturn([]);

        $connection = $this->connect($d);
        $connection->shouldHaveType('Samurai\Onikiri\Connection');
        $connection->shouldHaveType('PDO');
    }
}

