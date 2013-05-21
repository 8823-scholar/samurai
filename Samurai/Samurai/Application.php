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

use Samurai\Raikiri;
use Samurai\Samurai\Component\Core\Loader;

/**
 * Application class.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Application extends Raikiri\Object
{
    /**
     * environment.
     *
     * @access  public
     * @var     string
     */
    public $env = self::ENV_DEVELOPMENT;

    /**
     * class contain paths
     *
     * @access  public
     * @var     array
     */
    public $paths = array();

    /**
     * controller contain spaces.
     *
     * @access  public
     * @var     array
     */
    public $controller_spaces = array();

    /**
     * config data
     *
     * @access  public
     * @var     array
     */
    public $config = array();

    /**
     * booted ?
     *
     * @access  protected
     * @var     boolean
     */
    protected $_booted = false;

    /**
     * loader
     *
     * @access  public
     * @var     Samurai\Samurai\Component\Core\Loader
     */
    public $loader;

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
    public function bootstrap()
    {
        // booted ?
        if ( $this->_booted ) return;
        $this->_booted = true;

        // configure
        $this->configure();

        // include environment
        $this->_includeEnvironment();
    }


    /**
     * configure
     *
     * @access  public
     */
    public function configure()
    {
        // common constants.
        defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
        
        // autoload by composer
        $autoload_file = __DIR__ . '/vendor/autoload.php';
        if ( file_exists($autoload_file) ) {
            require_once $autoload_file;
        }

        // environment
        if ( $env = $this->_getEnvFromEnvironmentVariables() ) {
            $this->setEnv($env);
        }

        // add path.
        $this->addPath(dirname(dirname(__DIR__)));
        $this->addControllerSpace(__NAMESPACE__);

        // set directory names.
        $this->config('directory.config.samurai', 'Config/Samurai');
        $this->config('directory.config.routing', 'Config/Routing');
        $this->config('directory.config.database', 'Config/Database');
        $this->config('directory.config.renderer', 'Config/Renderer');
        $this->config('directory.layout', 'View/Layout');
        $this->config('directory.template', 'View/Content');
        $this->config('directory.locale', 'Locale');
        $this->config('directory.spec', 'Spec');
        $this->config('directory.skeleton', 'Skeleton');
        $this->config('directory.log', 'Log');
        $this->config('directory.temp', 'Temp');

        // set encodings.
        $this->config('encoding.input', 'UTF-8');
        $this->config('encoding.output', 'UTF-8');
        $this->config('encoding.internal', 'UTF-8');
        $this->config('encoding.text.html', 'UTF-8');

        // set caches.
        $this->config('cache.yaml.enable', true);
        $this->config('cache.yaml.expire', 60 * 60 * 24);    // 1 day
        
        // timezone.
        $this->setTimezone('Asia/Tokyo');
        
        // autoload by samurai
        $loader = new Loader($this);
        $loader->register();
        $this->loader = $loader;
    }


    /**
     * include environment file.
     *
     * @access  private
     */
    private function _includeEnvironment()
    {
        $env = $this->getEnv();
        foreach ( $this->getPaths() as $path ) {
            foreach ( $this->getControllerSpaces() as $space ) {
                $file = sprintf('%s/%s/Config/Environment/%s.php', $path, str_replace('\\', DS, $space), $env);
                if ( file_exists($file) ) {
                    include $file;
                }
            }
        }
    }




    /**
     * set env.
     *
     * @access  public
     * @param   string  $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
        return $this->getEnv();
    }


    /**
     * get env
     *
     * @access  public
     * @return  string
     */
    public function getEnv()
    {
        return $this->env;
    }


    /**
     * get env from environment variables.
     *
     * @access  protected
     */
    protected function _getEnvFromEnvironmentVariables()
    {
        // has env ?
        if ( $env = getenv('SAMURAI_ENV') ) {
            return $env;
        }

        return null;
    }
    
    
    
    /**
     * add class contain path.
     *
     * @access  public
     * @param   string  $path
     */
    public function addPath($path)
    {
        if ( ! in_array($path, $this->paths) ) {
            $this->paths[] = $path;
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
        return $this->paths;
    }



    /**
     * add controller contain namespace.
     *
     * @access  public
     * @param   string  $namespace
     */
    public function addControllerSpace($namespace)
    {
        if ( ! in_array($namespace, $this->controller_spaces) ) {
            $this->controller_spaces[] = $namespace;
        }
    }


    /**
     * get controller contain spaces.
     *
     * @access  public
     * @return  array
     */
    public function getControllerSpaces()
    {
        return $this->controller_spaces;
    }


    /**
     * clear controller contain spaces.
     *
     * @access  public
     */
    public function clearControllerSpaces()
    {
        $this->controller_spaces = array();
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
        if ( $value !== null || ! isset($this->config[$key])) {
            $this->config[$key] = $value;
        }

        return $this->config[$key];
    }


    /**
     * set timezone.
     *
     * @access  public
     * @param   string  $zone
     */
    public function setTimezone($zone)
    {
        $this->config('date.timezone', $zone);
        date_default_timezone_set($zone);
    }
}

