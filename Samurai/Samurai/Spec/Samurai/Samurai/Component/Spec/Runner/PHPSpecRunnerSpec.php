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

    public function it_sets_and_gets_workspace()
    {
        $this->setWorkspace('/foo/bar/zoo');
        $this->getWorkspace()->shouldBe('/foo/bar/zoo');
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

