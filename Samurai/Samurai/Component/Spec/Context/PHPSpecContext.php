<?php
/**
 * The MIT License
 *
 * Copyright (c) 2007-2013, Samurai Framework Project, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Samurai\Component\Spec\Context;

use PhpSpec\ObjectBehavior;
use Samurai\Raikiri\Container;
use Samurai\Raikiri\DependencyInjectable;

/**
 * PHPSpec text cace context.
 *
 * @package     Samurai
 * @subpackage  Component.Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class PHPSpecContext extends ObjectBehavior
{
    /**
     * samurai di-container
     *
     * @var     Samurai\Raikiri\Container
     */
    protected $__container;

    /**
     * set container.
     *
     * @param   Samurai\Raikiri\Container   $container
     */
    public function __setContainer(Container $container)
    {
        $this->__container = $container;
    }

    /**
     * get container
     *
     * @return  Samurai\Raikiri\Container
     */
    public function __getContainer()
    {
        return $this->__container;
    }


    /**
     * useable raikiri ?
     *
     * @param   object  $object
     * @return  boolean
     */
    public function isUseableRaikiri($object)
    {
        if (! is_object($object)) return false;

        $traits = [];
        $class = get_class($object);
        do {
            $traits = array_merge($traits, class_uses($class, false));
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait) {
            $traits = array_merge($traits, class_uses($trait, false));
        }

        return in_array('Samurai\\Raikiri\\DependencyInjectable', $traits);
    }


    /**
     * {@inheritdoc}
     */
    public function getWrappedObject()
    {
        $object = $this->object->getWrappedObject();
        if ($this->isUseableRaikiri($object) && ! $object->getContainer()) {
            $object->setContainer(new Container('spec'));
            //$object->setContainer($this->__getContainer());
        }
        return $object;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __call($method, array $arguments = array())
    {
        if (! in_array(strtolower($method), ['beconstructedwith', 'beconstructedthrough'])) {
            try {
                $this->getWrappedObject();
            } catch (\Exception $e) {
                //var_dump($e->getMessage());
            }
        }
        return parent::__call($method, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($property, $value)
    {
        $this->getWrappedObject();
        parent::__set($property, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($property)
    {
        $this->getWrappedObject();
        return parent::__get($property);
    }
    
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $this->getWrappedObject();
        return call_user_func_array('parent::__invoke', func_get_args());
    }
}

