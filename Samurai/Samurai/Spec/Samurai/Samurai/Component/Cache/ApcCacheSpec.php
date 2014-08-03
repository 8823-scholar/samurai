<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Cache;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use PhpSpec\Exception\Example\SkippingException;

class ApcCacheSpec extends PHPSpecContext
{
    public function let()
    {
        // has apc extension ?
        if (! extension_loaded('apc')) throw new SkippingException();
    }

    public function letgo()
    {
        $this->uncache('samurai_spec_key');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Cache\ApcCache');
    }


    public function it_stores_cache()
    {
        $this->cache('samurai_spec_key', 'value');
        $this->get('samurai_spec_key')->shouldBe('value');
    }


    public function it_unstores_cache()
    {
        $this->cache('samurai_spec_key', 'value');
        $this->get('samurai_spec_key')->shouldBe('value');

        $this->uncache('samurai_spec_key');
        $this->get('samurai_spec_key')->shouldBe(null);
    }


    public function it_has_cache()
    {
        $this->has('samurai_spec_key')->shouldBe(false);
        
        $this->cache('samurai_spec_key', 'value');
        $this->has('samurai_spec_key')->shouldBe(true);
    }


    public function it_is_supported()
    {
        $this->isSupported()->shouldBe(true);
    }
}

