<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\FileSystem\Iterator;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\FileSystem\File;
use Samurai\Samurai\Component\FileSystem\Directory;
use Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator;

class SimpleListIteratorSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\FileSystem\Iterator\SimpleListIterator');
        $this->shouldImplement('IteratorAggregate');
    }


    public function it_adds_a_file()
    {
        $file = new File(__FILE__);
        $this->add($file);

        $files_array = $this->toArray();
        $files_array->shouldHaveValue(__FILE__);
    }

    public function it_adds_some_files()
    {
        $file = new File(__FILE__);
        $this->add($file);
        $file = new Directory(__DIR__);
        $this->add($file);

        $this->size()->shouldBe(2);

        $files_array = $this->toArray();
        $files_array->shouldHaveValue(__FILE__);
        $files_array->shouldHaveValue(__DIR__);
    }


    public function it_is_foreachable()
    {
        $file = new File(__FILE__);
        $this->add($file);
        $file = new Directory(__DIR__);
        $this->add($file);

        $iterator = $this->getIterator();
        $iterator->shouldHaveType('ArrayIterator');
    }


    public function it_appends_another_iterator()
    {
        $iterator1 = new SimpleListIterator();
        $iterator1->add(new File('foo/bar/zoo/1.txt'));
        $iterator1->add(new File('foo/bar/zoo/2.txt'));
        $iterator2 = new SimpleListIterator();
        $iterator2->add(new File('foo/bar/zoo/3.txt'));
        $iterator2->add(new File('foo/bar/zoo/4.txt'));

        $this->append($iterator1);
        $this->append($iterator2);

        $files_array = $this->toArray();
        $files_array->shouldHaveValue('foo/bar/zoo/1.txt');
        $files_array->shouldHaveValue('foo/bar/zoo/2.txt');
        $files_array->shouldHaveValue('foo/bar/zoo/3.txt');
        $files_array->shouldHaveValue('foo/bar/zoo/4.txt');
    }

    public function it_gets_first_element()
    {
        $this->add(new File('foo/bar/zoo/1.txt'));
        $this->add(new File('foo/bar/zoo/2.txt'));
        $this->add(new File('foo/bar/zoo/3.txt'));

        $this->first()->toString()->shouldBe('foo/bar/zoo/1.txt');
    }
    
    public function it_gets_last_element()
    {
        $this->add(new File('foo/bar/zoo/1.txt'));
        $this->add(new File('foo/bar/zoo/2.txt'));
        $this->add(new File('foo/bar/zoo/3.txt'));

        $this->last()->toString()->shouldBe('foo/bar/zoo/3.txt');
    }

    public function it_reverses_sort_index()
    {
        $this->add(new File('foo/bar/zoo/1.txt'));
        $this->add(new File('foo/bar/zoo/2.txt'));
        $this->add(new File('foo/bar/zoo/3.txt'));

        $this->reverse()->first()->toString()->shouldBe('foo/bar/zoo/3.txt');
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

