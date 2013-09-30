<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Expression;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class GlobExpressionSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith('*');
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Expression\GlobExpression');
    }


    public function it_matches_asterisk()
    {
        $this->beConstructedWith('*');
        $this->isMatch('foo.bar.zoo')->shouldBe(true);
        $this->isMatch('zoo.bar.foo')->shouldBe(true);

        // multi bytes string (japanese)
        $this->isMatch('ストロボナイツ')->shouldBe(true);
    }

    public function it_matches_asterisk_in_last()
    {
        $this->beConstructedWith('foo.bar.*');
        $this->isMatch('foo.bar.zoo')->shouldBe(true);
        $this->isMatch('zoo.bar.foo')->shouldBe(false);
        $this->isMatch('foo.zoo.bar')->shouldBe(false);

        // multi bytes string (japanese)
        $this->isMatch('foo.bar.ストロボナイツ')->shouldBe(true);
    }
    
    public function it_matches_asterisk_in_middle()
    {
        $this->beConstructedWith('foo.*.zoo');
        $this->isMatch('foo.bar.zoo')->shouldBe(true);
        $this->isMatch('zoo.bar.foo')->shouldBe(false);
        $this->isMatch('foo.zoo.bar')->shouldBe(false);

        // multi bytes string (japanese)
        $this->isMatch('foo.ストロボナイツ.zoo')->shouldBe(true);
    }


    public function it_gets_regex_pattern()
    {
        $this->beConstructedWith('*');
        $this->getRegexPattern()->shouldBe('/.*?/');

        $this->beConstructedWith('foo.bar.*');
        $this->getRegexPattern()->shouldBe('/foo\\.bar\\..*?/');
        
        $this->beConstructedWith('foo.*.zoo');
        $this->getRegexPattern()->shouldBe('/foo\\..*?\\.zoo$/');
        
        $this->beConstructedWith('*.bar.zoo');
        $this->getRegexPattern()->shouldBe('/.*?\\.bar\\.zoo$/');
    }
}

