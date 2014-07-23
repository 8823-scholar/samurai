<?php

namespace Samurai\Samurai\Component\Spec\PHPSpec;

use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\SpecificationInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;

class DIContainerMaintainer implements MaintainerInterface
{
    /**
     * samurai di container.
     *
     * @access  public
     * @var     Samurai\Raikiri/Container
     */
    public $Container;

    /**
     * {@inheritdoc}
     */
    public function supports(ExampleNode $example)
    {
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function prepare(ExampleNode $example, SpecificationInterface $context, MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        // di container injection.
        $context->__setContainer($this->Container);

        $obj = $context->getWrappedObject();
        if (method_exists($obj, 'setContainer')) {
            $obj->setContainer($this->Container);
        }
        $this->Container->injectDependency($obj);
        $this->Container->injectDependency($context);
    }


    /**
     * {@inheritdoc}
     */
    public function teardown(ExampleNode $example, SpecificationInterface $context, MatcherManager $matchers, CollaboratorManager $collaborators)
    {
    }


    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 5;
    }
}

