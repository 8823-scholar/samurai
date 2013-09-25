<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\FileSystem;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class FileSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith(__FILE__);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\FileSystem\File');
        $this->shouldHaveType('SplFileInfo');
    }

    public function it_changes_get_path_specifications()
    {
        // "getPath" contain filename as "getPathname"
        $this->getPath()->shouldBe(__FILE__);
        $this->getPathname()->shouldBe(__FILE__);
    }
    
    public function it_gets_dirname()
    {
        // as SplFileInfo::getPath
        $this->getDirname()->shouldBe(dirname(__FILE__));
    }

    public function it_get_filename()
    {
        $this->getFilename()->shouldBe(basename(__FILE__));
    }
}

