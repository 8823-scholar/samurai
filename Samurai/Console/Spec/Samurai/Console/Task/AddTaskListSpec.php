<?php

namespace Samurai\Console\Spec\Samurai\Console\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\FileSystem\Utility as FileUtility;
use Samurai\Samurai\Component\Task\Option;
use Prophecy\Argument;

class AddTaskListSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $loader;
    public $application;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Console\Task\AddTaskList');
    }


    public function it_adds_class_file(FileUtility $fileUtil)
    {
        $contents = <<<'EOL'
<?php

namespace Samurai\Samurai;

/**
 * [class description]
 *
 * @package     package
 * @subpackage  subpackage
 * @author      name <foo@example.jp>
 */
class Sample
{
    /**
     * construct
     */
    public function __construct()
    {
    }
}


EOL;
        $option = new Option();
        $option->importFromArray(['Samurai\\Samurai\\Sample']);
        $current = $this->getRootAppDir($option)->getWrappedObject();
        $base_dir = $current;
        $fileUtil->mkdirP($base_dir . '/Samurai/Samurai')->shouldBeCalled();
        $fileUtil->putContents($base_dir . '/Samurai/Samurai/Sample.php', $contents)->shouldBeCalled();
        $this->setProperty('fileUtil', $fileUtil);
        $this->classTask($option);
    }


    public function it_adds_spec_file(FileUtility $fileUtil)
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
        $option = new Option();
        $option->importFromArray(['Samurai/Samurai/Sample']);
        $current = $this->getCurrentAppDir($option)->getWrappedObject();
        $spec_dir = $this->loader->find($current . DS . $this->application->config('directory.spec'))->first();
        $fileUtil->mkdirP($spec_dir . '/Samurai/Samurai')->willReturn(null);
        $fileUtil->putContents($spec_dir . '/Samurai/Samurai/SampleSpec.php', $contents)->willReturn(null);
        $this->setProperty('fileUtil', $fileUtil);
        $this->specTask($option);
    }
    
    public function it_adds_spec_file_top_layer_class(FileUtility $fileUtil)
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
        $option = new Option();
        $option->importFromArray(['Sample']);
        $current = $this->getCurrentAppDir($option)->getWrappedObject();
        $spec_dir = $this->loader->find($current . DS . $this->application->config('directory.spec'))->first();
        $fileUtil->mkdirP($spec_dir->toString())->willReturn(null);
        $fileUtil->putContents($spec_dir . '/SampleSpec.php', $contents)->willReturn(null);
        $this->setProperty('fileUtil', $fileUtil);
        $this->specTask($option);
    }
}

