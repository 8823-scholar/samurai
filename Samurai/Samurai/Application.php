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

namespace Samurai\Samurai;

use Samurai\Samurai\Component\Core\Loader;

/**
 * Application class.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Application
{
    /**
     * environment.
     *
     * @access  public
     * @var     string
     */
    public static $env = self::ENV_DEVELOPMENT;

    /**
     * class contain paths
     *
     * @access  public
     * @var     array
     */
    public static $paths = array();

    /**
     * controller contain spaces.
     *
     * @access  public
     * @var     array
     */
    public static $controller_spaces = array();

    /**
     * config data
     *
     * @access  public
     * @var     array
     */
    public static $config = array();

    /**
     * ENV: development
     *
     * @const   string
     */
    const ENV_DEVELOPMENT = 'development';

    /**
     * ENV: staging
     *
     * @const   string
     */
    const ENV_STAGING = 'staging';

    /**
     * ENV: production
     *
     * @const   string
     */
    const ENV_PRODUCTION = 'production';


    /**
     * bootstrap.
     *
     * @access  public
     */
    public static function bootstrap()
    {
        // common constants.
        defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
        
        // autoload by composer
        $autoload_file = __DIR__ . '/vendor/autoload.php';
        if ( file_exists($autoload_file) ) {
            require_once $autoload_file;
        }

        // add path.
        self::addPath(dirname(dirname(__DIR__)));
        self::addControllerSpace(__NAMESPACE__);
        
        // autoload by samurai
        spl_autoload_register('Samurai\Samurai\Component\Core\Loader::autoload');

        // timezone.
        self::setTimezone('Asia/Tokyo');
    }
    
    
    
    /**
     * add class contain path.
     *
     * @access  public
     * @param   string  $path
     */
    public static function addPath($path)
    {
        if ( ! in_array($path, self::$paths) ) {
            self::$paths[] = $path;
        }
    }


    /**
     * get class contain path.
     *
     * @access  public
     * @return  array
     */
    public function getPaths()
    {
        return self::$paths;
    }



    /**
     * add controller contain namespace.
     *
     * @access  public
     * @param   string  $namespace
     */
    public static function addControllerSpace($namespace)
    {
        if ( ! in_array($namespace, self::$controller_spaces) ) {
            self::$controller_spaces[] = $namespace;
        }
    }


    /**
     * get controller contain spaces.
     *
     * @access  public
     * @return  array
     */
    public static function getControllerSpaces()
    {
        return self::$controller_spaces;
    }


    /**
     * clear controller contain spaces.
     *
     * @access  public
     */
    public static function clearControllerSpaces()
    {
        self::$controller_spaces = array();
    }




    /**
     * accessor config.
     *
     * @access  public
     * @param   string  $key
     * @param   mixed   $value
     */
    public function config($key, $value = null)
    {
        // when value is not null, then set to config.
        if ( $value !== null || ! isset(self::$config[$key])) {
            self::$config[$key] = $value;
        }

        return self::$config[$key];
    }


    /**
     * set timezone.
     *
     * @access  public
     * @param   string  $zone
     */
    public function setTimezone($zone)
    {
        self::config('date.timezone', $zone);
        date_default_timezone_set($zone);
    }
}

