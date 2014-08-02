<?php

namespace Samurai\Onikiri\Spec\Samurai\Onikiri\Schema;

use Samurai\Onikiri\Spec\PHPSpecContext;

class TableSchemaSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith('foo', [
            'id' => ['name' => 'id', 'default' => null],
            'name' => ['name' => 'name', 'default' => 'who'],
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Schema\TableSchema');
    }


    public function it_sets_name()
    {
        $this->name('bar')->shouldHaveType('Samurai\Onikiri\Schema\TableSchema');
        $this->getName()->shouldBe('bar');
    }


    public function it_adds_column()
    {
        $column = $this->column('mail');
        $column->shouldHaveType('Samurai\Onikiri\Schema\ColumnSchema');
        $this->getColumn('mail')->shouldBe($column);
    }

    public function it_gets_columns()
    {
        $columns = $this->getColumns();
        $columns->shouldBeArray();
    }


    public function it_gets_default_values()
    {
        $values = $this->getDefaultValues();
        $values->shouldBe([
            'id' => null,
            'name' => 'who',
        ]);
    }
}

