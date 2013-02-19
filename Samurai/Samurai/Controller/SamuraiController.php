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

use Samurai\Raikiri;
use Samurai\Samurai\Config;

/**
 * Samurai base controller.
 *
 * @package     Samurai
 * @subpackage  Controller
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SamuraiController extends Raikiri\Object
{
    /**
     * View template.
     *
     * @const   string
     */
    const VIEW_TEMPLATE = 'template';

    /**
     * View forward action.
     *
     * @const   string
     */
    const VIEW_ACTION = 'action';

    /**
     * View location.
     *
     * @const   string
     */
    const VIEW_LOCATION = 'location';


    /**
     * @dependencies
     */
    public $Request;


    /**
     * Get filter paths
     *
     * 1. App/Controller/filter.yml
     * 2. App/Controller/foo.filter.yml
     */
    public function getFilters()
    {
        $filters = array();

        $class = get_class($this);
        $names = explode('\\', $class);

        // top 2 level is namespace.
        $dir = $this->getBaseDir();
        $dir = $dir . DS . array_shift($names);
        $dir = $dir . DS . array_shift($names);

        while ( $name = array_shift($names) ) {
            // when has rest.
            if ( count($names) > 0 ) {
                $filters[] = $dir . DS . 'filter.yml';
                $dir = $dir . DS . strtolower($name);

            // when last.
            } else {
                $name = strtolower(preg_replace('/Controller$/', '', $name));
                $filters[] = $dir . DS . 'filter.yml';
                $filters[] = $dir . DS . $name . '.filter.yml';
            }
        }

        return $filters;
    }


    /**
     * Get filter key
     *
     * @access  public
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
     * @access  public
     * @return  string
     */
    public function getBaseDir()
    {
        $class = get_class($this);
        $path = str_replace('\\', DS, $class) . '.php';

        // APP
        $app_dir = dirname(Config\APP_DIR);
        if ( $app_dir . DS . $path ) {
            return $app_dir;
        }

        return Config\ROOT_DIR;
    }
}

