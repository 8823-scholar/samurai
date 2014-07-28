<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Core;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Samurai\Controller\SamuraiController;
use Samurai\Samurai\Component\Core\YAML;

class FilterChainSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $loader;


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Core\FilterChain');
    }


    public function it_sets_controller(SamuraiController $c)
    {
        $this->setAction($c, 'execute');
        $this->getController()->shouldBe($c);
        $this->getAction()->shouldBe('execute');
    }


    public function it_adds_filter()
    {
        $this->addFilter('Some', ['attr1' => 1, 'attr2' => 2]);
        
        $filters = $this->getFilters();
        $filters->shouldHaveKey('Some');
        $filters['Some']->shouldBe(['name' => 'Some', 'attributes' => ['attr1' => 1, 'attr2' => 2]]);
    }


    public function it_build_filter_chain(SamuraiController $c)
    {
        $filters = [];
        foreach ($this->loader->find('Controller/filter.yml') as $filter) {
            $filters[] = $filter;
        }
        $c->getFilters()->willReturn($filters);
        $c->getFilterKey('execute')->willReturn('foo.execute');

        $this->setAction($c, 'execute');
        $this->build();

        $this->getFilters()->shouldHaveKey('View');
        $this->getFilters()->shouldHaveKey('Action');
    }


    public function it_loads_filter(SamuraiController $c)
    {
        $filter = $this->loader->findFirst('Controller/filter.yml');
        if (is_null($filter)) throw new \Exception('Not found filter.');

        $c->getFilterKey('execute')->willReturn('foo.execute');

        $this->setAction($c, 'execute');
        $this->loadFilter($filter);
        
        foreach (YAML::load($filter) as $values) {
            foreach ($values as $key => $value) {
                $this->getFilters()->shouldHaveKey($key);
            }
        }
    }


    public function it_gets_current_filter(SamuraiController $c)
    {
        $filter = $this->loader->findFirst('Controller/filter.yml');
        if (is_null($filter)) throw new \Exception('Not found filter.');

        $c->getFilterKey('execute')->willReturn('foo.execute');

        $this->setAction($c, 'execute');
        $this->loadFilter($filter);

        $current = $this->getCurrentFilter();
        $current->shouldHaveType('Samurai\Samurai\Filter\Filter');

        $filters = YAML::load($filter);
        $filter_names = array_keys($filters['*']);
        $current->getName()->shouldBe(array_shift($filter_names));

        $this->next();
        $current = $this->getCurrentFilter();
        $current->shouldHaveType('Samurai\Samurai\Filter\Filter');
        $current->getName()->shouldBe(array_shift($filter_names));
    }


    public function it_is_filterchain(SamuraiController $c)
    {
        $filter = $this->loader->findFirst('Controller/filter.yml');
        if (is_null($filter)) throw new \Exception('Not found filter.');

        $c->getFilterKey('execute')->willReturn('foo.execute');

        $this->setAction($c, 'execute');
        $this->loadFilter($filter);

        // loop
        while ($this->has()->getWrappedObject()) {
            $current = $this->getCurrentFilter();
            $current->shouldHaveType('Samurai\Samurai\Filter\Filter');
            $this->next();
        }
    }
}

