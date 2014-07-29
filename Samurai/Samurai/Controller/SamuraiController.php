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

namespace Samurai\Samurai\Controller;

use App\Application;
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Samurai\Config;
use Samurai\Samurai\Component\Core\Loader;

/**
 * Samurai base controller.
 *
 * @package     Samurai
 * @subpackage  Controller
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SamuraiController
{
    /**
     * name
     *
     * @var     string
     */
    public $name = '';


    /**
     * View template.
     *
     * @const   string
     */
    const VIEW_TEMPLATE = 'template';

    /**
     * Forward action.
     *
     * @const   string
     */
    const FORWARD_ACTION = 'action';

    /**
     * Location.
     *
     * @const   string
     */
    const LOCATION = 'location';

    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * set name.
     *
     * @access  public
     * @param   string
     */
    public function setName($name)
    {
        $this->name = $name;
    }



    /**
     * assign variable to renderer.
     *
     * @param   string  $name
     * @param   mixed   $value
     */
    public function assign($name, $value)
    {
        $this->renderer->set($name, $value);
    }



    /**
     * Get filter paths
     *
     * 1. Controller/filter.yml
     * 2. Controller/foo.filter.yml
     */
    public function getFilters()
    {
        $filters = array();

        $names = explode('_', $this->name);
        $base = 'Controller';
        $filters = array();
        while ($name = array_shift($names)) {
            $filter = $this->loader->find($base . DS . 'filter.yml')->first();
            if ($filter) $filters[] = $filter;

            // when has rest.
            if (count($names) > 0) {
                $base = $base . DS . ucfirst($name);

            // when last.
            } else {
                $filter = $this->loader->find($base . DS . $name . '.filter.yml')->first();
                if ($filter) $filters[] = $filter;
            }
        }

        return $filters;
    }


    /**
     * Get filter key
     *
     * @param   string  $action
     * @return  string
     */
    public function getFilterKey($action)
    {
        $class = get_class($this);
        $names = explode('\\', $class);

        // top 2 level is namespace.
        array_shift($names);
        array_shift($names);

        // controller.action
        $controller = preg_replace('/controller$/', '', strtolower(join('_', $names)));
        return $controller . '.' . $action;
    }


    /**
     * Get base dir.
     *
     * @return  string
     */
    public function getBaseDir()
    {
        $class = get_class($this);
        $path = str_replace('\\', DS, $class) . '.php';

        // APP
        $app_dir = dirname(Config\APP_DIR);
        if ($app_dir . DS . $path) {
            return $app_dir;
        }

        return Config\ROOT_DIR;
    }


    /**
     * before renderer
     */
    public function beforeRenderer()
    {
        $this->renderer->set('now', time());
    }
}

