<?php

namespace Samurai\Console\Spec\Samurai\Console\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Prophecy\Argument;

class AddTaskSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $loader;
    public $application;


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

class SampleSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Sample');
    }
}


EOL;
        $current = $this->getCurrentAppDir()->getWrappedObject();
        $spec_dir = $this->loader->find($current . DS . $this->application->config('directory.spec'))->first();
        $FileUtil->mkdirP($spec_dir . '/Samurai/Samurai')->willReturn(null);
        $FileUtil->putContents($spec_dir . '/Samurai/Samurai/SampleSpec.php', $contents)->willReturn(null);
        $this->setProperty('FileUtil', $FileUtil);
        
        $this->array2Options(['Samurai/Samurai/Sample']);
        $this->spec();
    }
    
    public function it_adds_spec_file_top_layer_class(\Samurai\Samurai\Component\FileSystem\Utility $FileUtil)
    {
        $contents = <<<'EOL'
<?php

namespace Samurai\Console\Spec;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SampleSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sample');
    }
}


EOL;
        $current = $this->getCurrentAppDir()->getWrappedObject();
        $spec_dir = $this->loader->find($current . DS . $this->application->config('directory.spec'))->first();
        $FileUtil->mkdirP($spec_dir->toString())->willReturn(null);
        $FileUtil->putContents($spec_dir . '/SampleSpec.php', $contents)->willReturn(null);
        $this->setProperty('FileUtil', $FileUtil);
        
        $this->array2Options(['Sample']);
        $this->spec();
    }
}

