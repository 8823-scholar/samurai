<?php

namespace Samurai\Console\Spec\Samurai\Console\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class AddTaskSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Console\Task\AddTask');
    }


    public function it_adds_spec_file()
    {
        $contents = <<<EOL
<?php

namespace Samurai\Console\Spec\Samurai\Samurai;

use Samurai\Samurai\Component\Spec\Contexts\PHPSpecContext;

class Sample extends PHPSpecContext
{
}
EOL;
        $this->task('add:spec', ['Samurai/Samurai/Sample']);
    }
}

