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

namespace Samurai\Samurai\Component\Renderer;

use Samurai\Samurai\Component\Core\Accessor;
use Samurai\Raikiri\DependencyInjectable;

/**
 * Renderer abstract class.
 *
 * @package     Samurai
 * @subpackage  Component.Renderer
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Renderer
{
    /**
     * renderer engine.
     *
     * @var     object
     */
    protected $engine;

    /**
     * @traits
     */
    use Accessor;
    use DependencyInjectable;


    /**
     * initialize method.
     */
    public function initialize()
    {
        $this->engine = $this->initEngine();

        // bootstap callback.
        foreach ($this->application->config('renderer.initializers.*') as $initializer) {
            $initializer($this->application, $this);
        }
    }



    /**
     * get engine.
     *
     * @return  object
     */
    public function getEngine()
    {
        return $this->engine;
    }


    /**
     * initialize engine.
     *
     * @return  object
     */
    abstract public function initEngine();


    /**
     * get suffix (template extension)
     *
     * @return  string
     */
    abstract public function getSuffix();


    /**
     * variable assign to engine.
     *
     * @param   string  $name
     * @param   mixed   $value
     */
    abstract public function set($name, $value);

    /**
     * helper object register to engine.
     *
     * @param   string  $name
     * @param   object  $component
     */
    abstract public function setComponent($name, $component);


    /**
     * rendering template trigger.
     *
     * @param   string  $template
     * @return  string
     */
    abstract public function render($template);
}

