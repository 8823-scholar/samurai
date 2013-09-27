<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\FileSystem\Finder;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class FinderSpec extends PHPSpecContext
{
    /**
     * put in test cace files.
     *
     * @var     string
     */
    public $fixtures_dir;

    /**
     * now pwd.
     *
     * @var     string
     */
    public $pwd;


    public function let()
    {
        $this->pwd = getcwd();
        chdir(__DIR__);
        $this->fixtures_dir = 'Fixtures';
    }

    public function letgo()
    {
        chdir($this->pwd);
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\FileSystem\Finder\Finder');
    }

    public function it_creates_a_instance()
    {
        $finder = $this->create();
        $finder->shouldHaveType('Samurai\Samurai\Component\FileSystem\Finder\Finder');

        $finder2 = $this->create();
        $finder->shouldNotBe($finder2);
    }

    public function it_searches_no_condition()
    {
        $files = $this->find($this->fixtures_dir);
        $files->shouldHaveType('Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator');

        $files_array = $files->toArray();
        $files_array->shouldBeArray();
        $files_array->shouldHaveCount(12);
        $files_array->shouldHaveValue('Fixtures');
        $files_array->shouldHaveValue('Fixtures/file1.txt');
        $files_array->shouldHaveValue('Fixtures/file2.txt');
        $files_array->shouldHaveValue('Fixtures/sub1');
        $files_array->shouldHaveValue('Fixtures/sub1/file1.txt');
        $files_array->shouldHaveValue('Fixtures/sub2');
        $files_array->shouldHaveValue('Fixtures/sub2/file2.txt');
    }

    public function it_searches_simple_blob()
    {
        $files = $this->find($this->fixtures_dir . '/*.txt');

        $files_array = $files->toArray();
        $files_array->shouldHaveCount(5);
        $files_array->shouldHaveValue('Fixtures/file1.txt');
        $files_array->shouldHaveValue('Fixtures/file2.txt');
        $files_array->shouldHaveValue('Fixtures/file3.txt');
        $files_array->shouldHaveValue('Fixtures/file4.a.txt');
        $files_array->shouldHaveValue('Fixtures/file5.b.txt');
    }

    public function it_searches_a_little_complicated_blob()
    {
        $files = $this->find($this->fixtures_dir . '/*.a.txt');
        $files_array = $files->toArray();
        $files_array->shouldHaveCount(1);
        $files_array->shouldHaveValue('Fixtures/file4.a.txt');
        
        
        $files = $this->find($this->fixtures_dir . '/*/*.txt');
        $files_array = $files->toArray();
        $files_array->shouldHaveCount(4);
        $files_array->shouldHaveValue('Fixtures/sub1/file1.txt');
        $files_array->shouldHaveValue('Fixtures/sub1/file2.txt');
        $files_array->shouldHaveValue('Fixtures/sub2/file1.txt');
        $files_array->shouldHaveValue('Fixtures/sub2/file2.txt');
    }

    public function it_searches_only_files()
    {
        $files = $this->path($this->fixtures_dir)->fileOnly()->find();
        $files_array = $files->toArray();
        $files_array->shouldHaveCount(9);
        $files_array->shouldHaveValue('Fixtures/file1.txt');
        $files_array->shouldHaveValue('Fixtures/file2.txt');
        $files_array->shouldHaveValue('Fixtures/sub1/file1.txt');
        $files_array->shouldHaveValue('Fixtures/sub2/file2.txt');
        $files_array->shouldNotHaveValue('Fixtures/sub1');
        $files_array->shouldNotHaveValue('Fixtures/sub2');
    }

    public function it_searches_only_directories()
    {
        $files = $this->path($this->fixtures_dir)->directoryOnly()->find();
        $files_array = $files->toArray();
        $files_array->shouldHaveCount(3);
        $files_array->shouldHaveValue('Fixtures');
        $files_array->shouldHaveValue('Fixtures/sub1');
        $files_array->shouldHaveValue('Fixtures/sub2');
        $files_array->shouldNotHaveValue('Fixtures/file1.txt');
        $files_array->shouldNotHaveValue('Fixtures/file2.txt');
        $files_array->shouldNotHaveValue('Fixtures/sub1/file1.txt');
        $files_array->shouldNotHaveValue('Fixtures/sub2/file2.txt');
    }

    public function it_searches_by_simple_name()
    {
        $files = $this->path($this->fixtures_dir)->name('file1.txt')->find();
        $files_array = $files->toArray();
        $files_array->shouldHaveCount(3);
        $files_array->shouldHaveValue('Fixtures/file1.txt');
        $files_array->shouldHaveValue('Fixtures/sub1/file1.txt');
        $files_array->shouldHaveValue('Fixtures/sub2/file1.txt');
    }
    
    public function it_searches_by_a_little_complicated_name()
    {
        $files = $this->path($this->fixtures_dir)->name('file*.*.txt')->find();
        $files_array = $files->toArray();
        $files_array->shouldHaveCount(2);
        $files_array->shouldHaveValue('Fixtures/file4.a.txt');
        $files_array->shouldHaveValue('Fixtures/file5.b.txt');
    }

    public function it_searches_not_recursive()
    {
        $files = $this->path($this->fixtures_dir)->notRecursive()->find();
        $files_array = $files->toArray();
        $files_array->shouldHaveCount(8);
        $files_array->shouldHaveValue('Fixtures');
        $files_array->shouldHaveValue('Fixtures/file1.txt');
        $files_array->shouldHaveValue('Fixtures/file4.a.txt');
        $files_array->shouldHaveValue('Fixtures/file5.b.txt');
        $files_array->shouldHaveValue('Fixtures/sub1');
        $files_array->shouldHaveValue('Fixtures/sub2');
        $files_array->shouldNotHaveValue('Fixtures/sub1/file1.txt');
        $files_array->shouldNotHaveValue('Fixtures/sub2/file1.txt');
    }

    public function it_searches_by_absoluted()
    {
        $files = $this->find(__FILE__);
        $files_array = $files->toArray();
        $files_array->shouldHaveValue(__FILE__);
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

