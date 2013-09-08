<?php

namespace Samurai\Console\Spec\Samurai\Console\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Prophecy\Argument;

class AddTaskSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $Loader;
    public $Application;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Console\Task\AddTask');
    }


    public function it_adds_spec_file(\Samurai\Samurai\Component\FileSystem\Utility $FileUtil)
    {
        $contents = <<<'EOL'
<?php

namespace Samurai\Console\Spec\Samurai\Samurai;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class Sample extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Console\Spec\Samurai\Samurai\Sample');
    }
}


EOL;
        $current = $this->getCurrentAppDir()->getWrappedObject();
        $spec_dir = $this->Loader->find($current . DS . $this->Application->config('directory.spec'))->first();
        $FileUtil->mkdirP($spec_dir . '/Samurai/Samurai')->willReturn(null);
        $FileUtil->putContents($spec_dir . '/Samurai/Samurai/SampleSpec.php', $contents)->willReturn(null);
        $this->setProperty('FileUtil', $FileUtil);
        
        $this->array2Options(['Samurai/Samurai/Sample']);
        $this->spec();
    }
}

