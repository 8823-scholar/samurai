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
 * @package     Raikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Raikiri;

use Samurai\Samurai\Component\DataSource\YAML;
use Prophecy\Prophecy\ProphecySubjectInterface;

/**
 * Container class.
 *
 * @package     Raikiri
 * @subpackage  Container
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Container
{
    /**
     * name.
     *
     * @var     string
     */
    public $name;

    /**
     * components
     *
     * @var     array
     */
    private $components = [];


    /**
     * construct
     *
     * @param   string  $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->register('yaml', new YAML());
    }


    /**
     * Get name.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * import dicon file.
     *
     * @param   string  $dicon
     */
    public function import($dicon)
    {
        $defines = $this->get('yaml')->load($dicon);
        foreach ($defines as $name => $def) {
            $define = $this->getComponentDefine($def);
            $this->register($name, $define);
        }
    }


    /**
     * has component ?
     *
     * @param   string  $name
     * @return  boolean
     */
    public function has($name)
    {
        return isset($this->components[$name]);
    }


    /**
     * register component to container.
     *
     * @param   string  $name
     * @param   object  $component
     */
    public function register($name, $component)
    {
        // Closure
        if ($component instanceof \Closure) {
            $component = new ComponentDefine($component);
        }

        if ($component instanceof ComponentDefine) {
            $component->setContainer($this);
        }
        elseif (method_exists($component, 'raikiri') && ! $component instanceof ProphecySubjectInterface) {
            $component->setContainer($this);
        }

        $this->components[$name] = $component;
    }


    /**
     * get component
     *
     * @param   string  $name
     * @return  object
     */
    public function get($name)
    {
        if (! $this->has($name)) return null;

        $def = $this->components[$name];

        // already initialized.
        if (! $def instanceof ComponentDefine) {
            return $def;
        }

        // initialize.
        return $def->getInstance();
    }


    /**
     * get define instance
     *
     * @param   mixed   $setting
     * @return  Samurai\Raikiri\ComponentDefine
     */
    public function getComponentDefine($setting = array())
    {
        if (is_string($setting)) {
            $setting = array('class' => $setting);
        }
        $define = new ComponentDefine($setting);
        return $define;
    }


    /**
     * Inject dependency to component
     *
     * @param   object  $component
     */
    public function injectDependency($component)
    {
        $nullFilter = function($var) {
            return $var === null;
        };
        $members = array_keys(array_filter(get_object_vars($component), $nullFilter));
        $names = array_keys($this->components);
        $members = array_intersect($members, $names);
        foreach ($members as $key) {
            $component->$key = $this->get($key);
        }
    }
}

