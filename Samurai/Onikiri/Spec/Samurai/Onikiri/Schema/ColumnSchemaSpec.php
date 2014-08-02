<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Schema;

use Samurai\Onikiri\Spec\PHPSpecContext;
use Samurai\Onikiri\Schema\TableSchema;

class ColumnSchemaSpec extends PHPSpecContext
{
    public function let(TableSchema $t)
    {
        $this->beConstructedWith($t, 'id');
    }

    public function it_is_initializable(TableSchema $t)
    {
        $this->shouldHaveType('Samurai\Onikiri\Schema\ColumnSchema');
    }

    public function it_sets_name()
    {
        $this->name('mail')->shouldHaveType('Samurai\Onikiri\Schema\ColumnSchema');
        $this->getName()->shouldBe('mail');
    }

    public function it_sets_default_value()
    {
        $this->name('mail')->defaultValue('scholar@hayabusa-lab.jp')->shouldHaveType('Samurai\Onikiri\Schema\ColumnSchema');
        $this->getDefaultValue()->shouldBe('scholar@hayabusa-lab.jp');
    }
}

