<?php

namespace Samurai\Onikiri\TamaHagane\Spec\Samurai\Onikiri\TamaHagane\Driver;

use Samurai\Onikiri\TamaHagane\Database;
use PhpSpec\Exception\Example\SkippingException;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SqliteDriverSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\TamaHagane\Driver\SqliteDriver');
    }
    
    
    public function it_connects_to_sqlite(Database $d)
    {
        $d->getDatabaseName()->willReturn(':memory:');
        $d->getOptions()->willReturn([]);

        $connection = $this->connect($d);
        $connection->shouldHaveType('Samurai\Onikiri\TamaHagane\Connection');
        $connection->shouldHaveType('PDO');
    }
}

