<?php

namespace Samurai\Samurai\Spec\Component\Core;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Application;
use Samurai\Samurai\Component\Core\Loader;
use Samurai\Samurai\Component\FileSystem\File;
use Samurai\Raikiri\Container;

use Prophecy\Argument;

class FrameworkSpec extends PHPSpecContext
{
    public function let(Application $app)
    {
        $this->beConstructedWith($app);
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Core\Framework');
        $this->app->shouldHaveType('Samurai\Samurai\Application');
    }


    public function it_get_application(Application $app)
    {
        $this->getApplication()->shouldReturn($app);
    }
}

