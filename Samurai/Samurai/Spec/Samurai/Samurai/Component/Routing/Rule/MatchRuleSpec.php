<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Routing\Rule;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class MatchRuleSpec extends PHPSpecContext
{
    public function let()
    {
        $this->beConstructedWith([]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\MatchRule');
    }


    /**
     * match: { /login: user.login, as login }
     */
    public function it_is_match_standard()
    {
        $this->beConstructedWith([
            '/login' => 'user.login',
            'as' => 'login',
        ]);

        $this->match('/login')->shouldBe(true);

        $this->getName()->shouldBe('login');
        $this->getController()->shouldBe('user');
        $this->getAction()->shouldBe('login');
    }
}

