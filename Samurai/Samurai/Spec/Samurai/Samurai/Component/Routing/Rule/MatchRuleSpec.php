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

    /**
     * match use *
     */
    public function it_is_match_ast()
    {
        $this->beConstructedWith(['/foo/*' => 'foo.compile', 'as' => 'foo']);

        $this->match('/foo/bar')->shouldBe(true);
        $this->match('/foo/bar/zoo')->shouldBe(true);
        $this->getController()->shouldBe('foo');
        $this->getAction()->shouldBe('compile');
    }
    
    public function it_is_match_with_params()
    {
        $this->beConstructedWith(['/foo/:bar/:zoo' => 'foo.compile', 'as' => 'foo']);

        $this->match('/foo/1/2')->shouldBe(true);
        $this->getController()->shouldBe('foo');
        $this->getAction()->shouldBe('compile');
        $this->getParams()->shouldBe(['bar' => '1', 'zoo' => '2']);
    }

    public function it_is_match_with_suffix()
    {
        $this->beConstructedWith(['/photo/:id.:format' => 'photo.show', 'as' => 'photo_show']);

        $this->match('/photo/123.jpg')->shouldBe(true);
        $this->getController()->shouldBe('photo');
        $this->getAction()->shouldBe('show');
        $this->getParams()->shouldBe(['format' => 'jpg', 'id' => '123']);
    }
}

