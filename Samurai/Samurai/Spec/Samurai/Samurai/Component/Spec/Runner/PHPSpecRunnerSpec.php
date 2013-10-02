<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Spec\Runner;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\FileSystem\Utility;
use Samurai\Samurai\Component\FileSystem\File;

class PHPSpecRunnerSpec extends PHPSpecContext
{
    /**
     * put in fixtures.
     */
    private $fixtures_dir = 'Fixtures';


    public function let()
    {
        $this->fixtures_dir = dirname(__DIR__) . DS . 'Fixtures';
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Spec\Runner\PHPSpecRunner');
        $this->shouldHaveType('Samurai\Samurai\Component\Spec\Runner\Runner');
    }

    public function it_validates_name_space()
    {
        $this->validateNameSpace('Samurai\Samurai', 'Samurai\Samurai\Spec\Samurai\Samurai\Foo\Bar\ZooSpec')->shouldBe('spec\Samurai\Samurai\Foo\Bar');
        $this->validateNameSpace('Samurai\Samurai', 'Samurai\Samurai\Spec\Foo\Bar\ZooSpec')->shouldBe('spec\Foo\Bar');
    }

    public function it_validates_class_name()
    {
        $this->validateClassName('Samurai\Samurai', 'Samurai\Samurai\Spec\Samurai\Samurai\Foo\Bar\ZooSpec')->shouldBe('ZooSpec');
        $this->validateClassName('Samurai\Samurai', 'Samurai\Samurai\Spec\Foo\Bar\ZooSpec')->shouldBe('ZooSpec');
    }

    public function it_validates_class_file()
    {
        $workspace = $this->getWorkspace()->getWrappedObject();
        $this->validateClassFile('spec\Samurai\Samurai\Foo\Bar', 'ZooSpec')->shouldBe($workspace . DS . 'spec/Samurai/Samurai/Foo/Bar/ZooSpec.php');
    }

    public function it_generates_configuration_file_for_phpspec(Utility $FileUtil)
    {
        $workspace = $this->getWorkspace()->getWrappedObject();
        $file = $workspace . DS . 'phpspec.yml';
        $contents = <<<'EOL'
---
suites:
  main:
    namespace:
    spec_prefix: spec
    src_path: src
    spec_path: .

EOL;
        $FileUtil->putContents($file, $contents)->willReturn(null);
        $this->setProperty('FileUtil', $FileUtil);

        $this->generateConfigurationFile();
    }

    public function it_searches_spec_all_files()
    {
        $this->addTarget($this->fixtures_dir);

        $files = $this->searchSpecFiles();
        $files->shouldHaveType('Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator');

        $files_array = $files->toArray();
        $files_array->shouldHaveValue($this->fixtures_dir . DS . 'Spec/HogeSpec.php');
        $files_array->shouldHaveValue($this->fixtures_dir . DS . 'Spec/HageSpec.php');
        $files_array->shouldHaveValue($this->fixtures_dir . DS . 'Spec/Foo/BarSpec.php');
        $files_array->shouldNotHaveValue($this->fixtures_dir . DS . 'Spec/Foo/Zoo.php');
    }
    
    
    
    
    /**
     * for abstract runner class specs.
     */
    public function it_adds_target_dir()
    {
        $this->addTarget('/foo/bar/zoo');
        $this->addTarget('/zoo/bar/foo');

        $this->getTargets()->shouldBeArray();
        $this->getTargets()->shouldHaveValue('/foo/bar/zoo');
        $this->getTargets()->shouldHaveValue('/zoo/bar/foo');
    }

    public function it_sets_and_gets_workspace()
    {
        $this->setWorkspace('/foo/bar/zoo');
        $this->getWorkspace()->shouldBe('/foo/bar/zoo');
    }

    public function it_is_match_no_query()
    {
        $file = new File('/foo/bar/zoo');
        $this->isMatch($file)->shouldBe(true);
    }
    
    public function it_is_match_has_namespace_query()
    {
        $file = new File($this->fixtures_dir . DS . 'Spec/Foo/BarSpec.php');
        $this->isMatch($file, ['samurai'])->shouldBe(true);
        $this->isMatch($file, ['samurai:samurai:spec'])->shouldBe(true);
        $this->isMatch($file, ['samurai:samurai:spec:samurai:samurai'])->shouldBe(true);
        $this->isMatch($file, ['samurai:console'])->shouldBe(false);
    }

    public function it_is_match_has_filepath_query()
    {
        // relational
        $pwd = getcwd();
        chdir($this->fixtures_dir);
        $file = new File('Spec/Foo/BarSpec.php');
        $this->isMatch($file, ['Spec/Foo'])->shouldBe(true);
        $this->isMatch($file, ['Spec/Foo/BarSpec.php'])->shouldBe(true);
        $this->isMatch($file, ['Spec/Foo/Bar'])->shouldBe(false);
        $this->isMatch($file, ['Spec/Bar'])->shouldBe(false);
        $this->isMatch($file, ['../Fixtures/Spec'])->shouldBe(true);
        chdir($pwd);

        // absolute
        $file = new File($this->fixtures_dir . DS . 'Spec/Foo/BarSpec.php');
        $this->isMatch($file, [$this->fixtures_dir])->shouldBe(true);
        $this->isMatch($file, ['/foo/bar'])->shouldBe(false);
    }
    
    
    
    
    public function getMatchers()
    {
        return [
            'haveValue' => function($subject, $key) {
                return in_array($key, $subject);
            }
        ];
    }
}

