<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Application;
use Samurai\Samurai\Component\Core\Loader;
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

    public function it_removes_config()
    {
        $this->config('some', 1);
        $this->config('some')->shouldBe(1);
        $this->removeConfig('some');
        $this->config('some')->shouldBe(null);
    }

    public function it_gets_config_as_array()
    {
        $this->config('directory.foo', '/path/to/foo');
        $this->config('directory.bar', '/path/to/bar');
        $this->config('directory.zoo', '/path/to/zoo');
        $this->config('directory.*')->shouldBe([
            'directory.foo' => '/path/to/foo',
            'directory.bar' => '/path/to/bar',
            'directory.zoo' => '/path/to/zoo',
        ]);
    }

    public function it_gets_config_as_hierarchical()
    {
        $this->config('app.title', 'samurai 7');
        $this->config('app.phrase.param1', 'attack');
        $this->config('app.phrase.param2', 'deffence');
        $this->config('directory.foo', '/path/to/foo');
        $this->config('directory.bar', '/path/to/bar');
        $this->config('directory.zoo', '/path/to/zoo');
        $this->configHierarchical()->shouldBe([
            'app' => [
                'title' => 'samurai 7',
                'phrase' => ['param1' => 'attack', 'param2' => 'deffence'],
            ],
            'directory' => [
                'foo' => '/path/to/foo',
                'bar' => '/path/to/bar',
                'zoo' => '/path/to/zoo',
            ],
        ]);
        $this->configHierarchical('app.*')->shouldBe([
            'app' => [
                'title' => 'samurai 7',
                'phrase' => ['param1' => 'attack', 'param2' => 'deffence'],
            ]
        ]);
    }

    public function it_add_path_wrapper_of_config()
    {
        $this->addAppPath(__DIR__, __NAMESPACE__, Application::PRIORITY_LOW);
        $this->config('directory.apps')->shouldReturn(array(
            ['dir' => __DIR__, 'root' => substr(__DIR__, 0, -1 - strlen(__NAMESPACE__)),
                'namespace' => __NAMESPACE__, 'priority' => Application::PRIORITY_LOW, 'index' => 0]
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
    }

    public function it_get_loader_after_bootstrap()
    {
        $this->bootstrap();
        $this->getLoader()->shouldHaveType('Samurai\Samurai\Component\Core\Loader');
    }


    public function it_is_production()
    {
        $this->setEnv('production');
        $this->isProduction()->shouldBe(true);
        
        $this->setEnv('development');
        $this->isProduction()->shouldBe(false);
    }
}

