<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Cache;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ArrayCacheSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Cache\ArrayCache');
    }


    public function it_stores_cache()
    {
        $this->cache('key', 'value');
        $this->get('key')->shouldBe('value');
    }


    public function it_unstores_cache()
    {
        $this->cache('key', 'value');
        $this->get('key')->shouldBe('value');

        $this->uncache('key');
        $this->get('key')->shouldBe(null);
    }


    public function it_has_cache()
    {
        $this->has('key')->shouldBe(false);
        
        $this->cache('key', 'value');
        $this->has('key')->shouldBe(true);
    }


    public function it_is_supported()
    {
        $this->isSupported()->shouldBe(true);
    }
}

