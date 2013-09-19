<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SampleTaskSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Task\SampleTask');
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
        $this->getUsage('some')->shouldBe($usage);
    }
}


/**
 * dummy sample task.
 */
namespace Samurai\Samurai\Task;

use Samurai\Samurai\Component\Task\Task;

class SampleTask extends Task
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
    public function some()
    {
    }
}


