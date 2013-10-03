<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Renderer;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class TwigRendererSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Renderer\TwigRenderer');
        $this->shouldHaveType('Samurai\Samurai\Component\Renderer\Renderer');
    }

    public function it_is_template_suffix()
    {
        $this->template_suffix->shouldBe('html.twig');
    }

    public function it_initialize_twig_engine()
    {
        $engine = $this->initEngine();
        $engine->shouldHaveType('Twig_Environment');
    }

    public function it_assign_variables()
    {
        $this->set('var1', 'abc');
        $this->set('var2', 123);

        $this->getVariables()->shouldBe(['var1' => 'abc', 'var2' => 123]);
    }
}

