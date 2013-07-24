<?php

namespace Samurai\Samurai\Spec;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Application;
use Samurai\Raikiri\Container;

class ApplicationSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Application');
    }

    public function it_stores_config()
    {
        $this->config('directory.some', __DIR__);
        $this->config('directory.some')->shouldReturn(__DIR__);
    }

    public function it_stores_config_as_array()
    {
        $this->config('directory.somes.', '/path/some1');
        $this->config('directory.somes.', '/path/some2');
        $this->config('directory.somes')->shouldReturn(['/path/some1', '/path/some2']);
    }

    public function it_add_path_wrapper_of_config()
    {
        $this->addAppPath(__DIR__, __NAMESPACE__, Application::PRIORITY_LOW);
        $this->config('directory.app')->shouldReturn(array(
            ['dir' => __DIR__, 'namespace' => __NAMESPACE__, 'priority' => Application::PRIORITY_LOW, 'index' => 0]
        ));
    }

    public function it_sets_timezone()
    {
        $this->setTimeZone($timezone = 'Asia/Tokyo');
        $this->config('date.timezone')->shouldReturn($timezone);
        $this->config('date.timezone')->shouldReturn(date_default_timezone_get());
    }

    public function it_sets_container(Container $c)
    {
        $this->setContainer($c);
        $this->getContainer()->shouldBe($c);
    }

    public function it_enable_bootstrap()
    {
        $this->bootstrap();
        $this->booted->shouldBe(true);
        $this->loader->shouldHaveType('Samurai\Samurai\Component\Core\Loader');
    }
}

