<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SampleTaskListSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Task\SampleTaskList');
    }


    public function it_is_usage_text_from_doc_comment()
    {
        $usage = <<<'EOL'
something do.

[usage]
    $ ./app sample:some

[options]
    --usage          show help.

EOL;
        $this->getOption('some')->usage()->shouldBe($usage);
    }
}


/**
 * dummy sample task.
 */
namespace Samurai\Samurai\Task;

use Samurai\Samurai\Component\Task\Task;

class SampleTaskList extends Task
{
    /**
     * something do.
     *
     * [usage]
     *     $ ./app sample:some
     *
     * [options]
     *     --usage          show help.
     *
     * @access  public
     */
    public function someTask()
    {
    }
}


