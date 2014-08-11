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
     * view.
     *
     * @const   string
     */
    const VIEW_TEMPLATE = 'template';
    const TEMPLATE = self::VIEW_TEMPLATE;
    const FORWARD_ACTION = 'action';
    const ACTION = self::FORWARD_ACTION;
    const OUTPUT_JSON = 'json';
    const JSON = self::OUTPUT_JSON;
    const LOCATION_URL = 'location';
    const LOCATION = self::LOCATION_URL;

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
        $filters = [];

        $names = explode('_', $this->name);
        $base = '';
        $filters = [];
        while ($name = array_shift($names)) {
            $filter = $this->_searchInControllerDir($base, 'filter.yml');
            if ($filter) $filters[] = $filter;

            // when has rest.
            if (count($names) > 0) {
                $base = $base . DS . ucfirst($name);

            // when last.
            } else {
                $filter = $this->_searchInControllerDir($base, "{$name}.filter.yml");
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
     * get onikiri entity table.
     *
     * @param   string  $name
     * @return  Samurai\Onikiri\EntityTable
     */
    public function getTable($name)
    {
        return $this->onikiri()->getTable($name);
    }

    /**
     * get model instance.
     *
     * @param   string  $name
     * @return  object
     */
    public function getModel($name)
    {
    }


    /**
     * get raikiri container.
     *
     * @return  Samurai\Raikiri\Container
     */
    public function raikiri()
    {
        return $this->getContainer();
    }

    /**
     * get onikiri
     *
     * @return  Samurai\Onikiri\Onikiri
     */
    public function onikiri()
    {
        return $this->raikiri()->get('onikiri');
    }


    /**
     * before renderer
     */
    public function beforeRenderer()
    {
        $this->assign('now', time());
        $this->assign('request', $this->request->getAll());
    }


    /**
     * search in controller dirs
     *
     * @param   string  $base_dir
     * @param   string  $file_name
     * @return  Samurai\Samurai\Component\FileSystem\Finder\Iterator\Iterator
     */
    private function _searchInControllerDir($base_dir, $file_name)
    {
        $dirs = $this->application->getControllerDirectories();
        foreach ($dirs as $dir) {
            $filter = $this->finder->path($dir . $base_dir)->name($file_name)->find()->first();
            if ($filter) return $filter;
        }
    }
}

