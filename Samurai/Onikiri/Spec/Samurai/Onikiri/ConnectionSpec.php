<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri;

use Samurai\Onikiri\Spec\PHPSpecContext;

class ConnectionSpec extends PHPSpecContext
{

    public function let()
    {
        $this->_setMySQLDatabase();
        $this->beConstructedWith(
            $this->_spec_driver->makeDsn($this->_spec_database),
            $this->_spec_database->getUser(),
            $this->_spec_database->getPassword());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Connection');
        $this->shouldHaveType('PDO');
    }
}

