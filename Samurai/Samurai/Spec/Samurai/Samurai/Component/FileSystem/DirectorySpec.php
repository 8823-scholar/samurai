<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\FileSystem;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class DirectorySpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith(dirname(__FILE__));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\FileSystem\Directory');
        $this->shouldHaveType('Samurai\Samurai\Component\FileSystem\File');
        $this->shouldHaveType('SplFileInfo');
    }
}

