<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\DataStore;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use PhpSpec\Exception\Example\SkippingException;

class RedisSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $request;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\DataStore\Redis');
    }


    public function it_connects()
    {
        $host = $this->request->getEnv('SAMURAI_SPEC_REDIS_HOST');
        if (! $host) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_HOST".');
        
        $port = $this->request->getEnv('SAMURAI_SPEC_REDIS_PORT', 6379);
        if (! $port) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_PORT".');

        $this->connect($host, $port)->shouldBe(true);
    }


    public function it_sets_key_value_data()
    {
        $this->_connect();
        $this->set('samurai.spec.foo', 'bar');
        $this->get('samurai.spec.foo')->shouldBe('bar');
    }

    public function it_gets_key_value_data()
    {
        $this->_connect();
        $this->set('samurai.spec.foo', 'bar2');
        $this->get('samurai.spec.foo')->shouldBe('bar2');
    }

    public function it_deletes()
    {
        $this->_connect();
        $this->get('samurai.spec.foo')->shouldNotBe(null);

        $this->delete('samurai.spec.foo');
        
        $this->get('samurai.spec.foo')->shouldBe(null);
    }

    public function it_adds_to_list_and_get_list()
    {
        $this->_connect();

        $this->delete('samurai.spec.foo');
        $this->addList('samurai.spec.foo', 'value1');
        $this->addList('samurai.spec.foo', 'value2');
        $this->addList('samurai.spec.foo', 'value1');

        $this->getList('samurai.spec.foo')->shouldBe(['value1', 'value2', 'value1']);
    }
    
    public function it_adds_to_set_and_get_set()
    {
        $this->_connect();

        $this->delete('samurai.spec.foo');
        $this->addSet('samurai.spec.foo', 'value1');
        $this->addSet('samurai.spec.foo', 'value2');
        $this->addSet('samurai.spec.foo', 'value1');

        $this->getSet('samurai.spec.foo')->shouldNotHaveDiff(['value2', 'value1']);
    }


    public function it_adds_to_sorted_set_and_get()
    {
        $this->_connect();

        $this->delete('samurai.spec.foo');
        $this->addSortedSet('samurai.spec.foo', 'user1', 1);
        $this->addSortedSet('samurai.spec.foo', 'user2', 2);
        $this->addSortedSet('samurai.spec.foo', 'user3', 3);
        $this->addSortedSet('samurai.spec.foo', 'user4', 3);
        $this->addSortedSet('samurai.spec.foo', 'user1', 4);

        $this->getSortedRank('samurai.spec.foo', 'user1')->shouldBe(3);
        $this->getSortedRankAsc('samurai.spec.foo', 'user1')->shouldBe(3);
        $this->getSortedRankDesc('samurai.spec.foo', 'user1')->shouldBe(0);

        // same score
        $this->getSortedRank('samurai.spec.foo', 'user3')->shouldBe($this->getSortedRank('samurai.spec.foo', 'user4'));

        // not entried
        $this->getSortedRank('samurai.spec.foo', 'user99')->shouldBe(null);
    }



    /**
     * connect to redis
     */
    private function _connect()
    {
        $host = $this->request->getEnv('SAMURAI_SPEC_REDIS_HOST');
        if (! $host) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_HOST".');
        
        $port = $this->request->getEnv('SAMURAI_SPEC_REDIS_PORT', 6379);
        if (! $port) throw new SkippingException('Set env "SAMURAI_SPEC_REDIS_PORT".');

        $this->connect($host, $port);
    }


    /**
     * matchers
     */
    public function getMatchers()
    {
        return [
            'haveDiff' => function($subject, $expect) {
                return array_diff($subject, $expect) || count($subject) !== count($expect);
            }
        ];
    }
}

