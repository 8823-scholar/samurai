<?php

namespace Samurai\Samurai\Spec\Component\Core;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class NamespacerSpec extends PHPSpecContext
{
    public function it_register_namespace_with_path()
    {
        // How should I describe spec for static method ?
        /*
        static::register(__NAMESPACE__, __DIR__);
        static::$namespaces->should->be([__DIR__ => __NAMESPACE__]);
         */
    }
}

