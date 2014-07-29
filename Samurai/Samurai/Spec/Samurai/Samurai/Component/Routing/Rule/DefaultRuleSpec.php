<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Routing\Rule;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class DefaultRuleSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\DefaultRule');
    }


    public function it_is_match_standard()
    {
        // /:controller/:action
        $this->match('/user/profile')->shouldBe(true);

        $this->getController()->shouldBe('user');
        $this->getAction()->shouldBe('profile');
    }

    public function it_is_match_has_id()
    {
        // /:controller/:action/:id
        $this->match('/user/profile/11')->shouldBe(true);

        $this->getController()->shouldBe('user');
        $this->getAction()->shouldBe('profile');
        $this->getParams()->shouldBe(['id' => 11]);
    }
    
    public function it_is_match_has_format()
    {
        // /:controller/:action/:id.:format
        $this->match('/user/profile/11.gif')->shouldBe(true);

        $this->getController()->shouldBe('user');
        $this->getAction()->shouldBe('profile');
        $this->getParams()->shouldBe(['format' => 'gif', 'id' => 11]);
    }

    public function it_is_match_hierarchical()
    {
        // /:controller/:subcontroller/:action
        $this->match('/user/profile/show')->shouldBe(true);

        $this->getController()->shouldBe('user_profile');
        $this->getAction()->shouldBe('show');
        
        // /:controller/:subcontroller/:nested/:action
        $this->match('/user/profile/zoom/show')->shouldBe(true);

        $this->getController()->shouldBe('user_profile_zoom');
        $this->getAction()->shouldBe('show');
        
        // /:controller/:subcontroller/:nested/:action/:id
        $this->match('/user/profile/zoom/show/12')->shouldBe(true);

        $this->getController()->shouldBe('user_profile_zoom');
        $this->getAction()->shouldBe('show');
        $this->getParams(['id' => 12]);
        
        // /:controller/:subcontroller/:nested/:action/:id.:format
        $this->match('/user/profile/zoom/show/12.jpg')->shouldBe(true);

        $this->getController()->shouldBe('user_profile_zoom');
        $this->getAction()->shouldBe('show');
        $this->getParams(['id' => 12, 'format' => 'jpg']);
    }

    public function it_is_not_match()
    {
        // top layer uri (no action information)
        $this->match('/foo')->shouldBe(false);
        $this->match('/favicon.ico')->shouldBe(false);
    }
}

