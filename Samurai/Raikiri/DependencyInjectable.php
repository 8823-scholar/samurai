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

namespace Samurai\Raikiri;

use Samurai\Samurai\Exception\MemberNotFoundException;
use Samurai\Onikiri\Exception\EntityTableNotFoundException;

/**
 * Support dependency injection methods.
 *
 * @package     Samurai.Raikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
trait DependencyInjectable
{
    /**
     * container
     *
     * @var     Samurai\Raikiri\Container
     */
    protected $container;

    /**
     * set container.
     *
     * @param   Samurai\Raikiri\Container   $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * get container.
     *
     * @return  Samurai\Raikiri\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * get container alias.
     *
     * @return  Samurai\Raikiri\Container
     */
    public function raikiri()
    {
        return $this->getContainer();
    }

    /**
     * onikiri bredge.
     *
     * @return  Samurai\Onikiri\Onikiri
     */
    public function onikiri()
    {
        return $this->raikiri()->get('onikiri');
    }


    /**
     * initialize method
     */
    public function initialize()
    {
    }


    /**
     * auto injection resolve.
     */
    public function __get($name)
    {
        // has container
        $container = $this->getContainer();
        if ($container && $container->has($name)) return $container->get($name);

        // has model table.
        try {
            $onikiri = $this->onikiri();
            $name = ucfirst(preg_replace('/Table$/', '', $name));
            if ($onikiri && $table = $onikiri->getTable($name)) return $table;
        } catch (EntityTableNotFoundException $e) {
        }

        throw new MemberNotFoundException(sprintf('member not found. -> %s::$%s', get_class($this), $name));
    }
}

