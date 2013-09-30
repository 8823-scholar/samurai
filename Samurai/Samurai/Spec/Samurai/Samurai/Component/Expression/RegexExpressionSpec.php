<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Expression;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Component\Expression\Exception\InvalidExpressionException;

class RegexExpressionSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith('/^foo$/');
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Expression\RegexExpression');
    }


    public function it_is_regular_expressions()
    {
        $this->beConstructedWith('/^foo\\..*/');
        $this->isMatch('foo.bar.zoo')->shouldBe(true);
        $this->isMatch('bar.zoo.foo')->shouldBe(false);
        $this->isMatch('FOO.BAR.ZOO')->shouldBe(false);

        $this->beConstructedWith('/.*\\.zoo$/i');
        $this->isMatch('foo.bar.zoo')->shouldBe(true);
        $this->isMatch('FOO.BAR.ZOO')->shouldBe(true);
        $this->isMatch('foo.zoo.bar')->shouldBe(false);
        
        $this->beConstructedWith('/^foo\\.[0-9]+\\.zoo$/');
        $this->isMatch('foo.bar.zoo')->shouldBe(false);
        $this->isMatch('foo.8823.zoo')->shouldBe(true);
        $this->isMatch('foo..zoo')->shouldBe(false);
    }


    public function it_is_irregular_expressions()
    {
        $this->shouldThrow('Samurai\Samurai\Component\Expression\Exception\InvalidExpressionException')->during('__construct', ['f']);
        $this->shouldThrow('Samurai\Samurai\Component\Expression\Exception\InvalidExpressionException')->during('__construct', ['//']);
        $this->shouldThrow('Samurai\Samurai\Component\Expression\Exception\InvalidExpressionException')->during('__construct', ['/aaa|']);
        $this->shouldThrow('Samurai\Samurai\Component\Expression\Exception\InvalidExpressionException')->during('__construct', ['/aaa/abcdefz']);
    }
}

