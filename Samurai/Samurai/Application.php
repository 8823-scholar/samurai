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

// common constants.
defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);

// autoload by composer
$autoload_file = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload_file)) {
    require_once $autoload_file;
}

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
    public $env = self::ENV_DEVELOPMENT;

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
     * bootstrap steps is...
     *
     * 1. configure
     * 
     *     call Application::configure().
     *     placed at path/to/App/Application.php
     *
     * 2. initializer
     *
     *     call Initializer::configure().
     *     placed at path/to/App/Config/Initializer/*.php
     *
     * 3. environment 
     *
     *     call Environment::configure().
     *     placed at path/to/App/Config/Environment/${ENV}.php
     *
     * @access  public
     */
    public function bootstrap()
    {
        // booted ?
        if ($this->_booted) return;
        $this->_booted = true;

        // call application::configure
        $this->configure();

        // collect initializers
        $this->initializers();

        // environment
        $this->environment();
        
        var_dump($this);
        exit;
    }


    /**
     * base configure.
     *
     * @access  public
     */
    public function configure()
    {
        // environment
        $this->setEnv($this->getEnvFromEnvironmentVariables());

        // application root dir.
        $this->config('directory.root', dirname(dirname(__DIR__)));
        
        // application dir.
        $this->config('directory.app.', ['dir' => __DIR__, 'namespace' => __NAMESPACE__, 'priority' => 'low']);

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
        
        // main dicon
        $this->config('dicon', 'Config/Samurai/samurai.dicon');

        // set encodings.
        $this->config('locale', 'ja');
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
     * call initializers.
     *
     * @access  protected
     */
    protected function initializers()
    {
        $initializers = $this->loader->find('Config/Initializer/*.php');
        foreach ($initializers as $file) {
            require_once $file->getRealPath();
            $class = $file->getClassName();
            $initializer = new $class();
            $initializer->configure($this);
        }
    }


    /**
     * environment settings.
     *
     * @access  public
     */
    public function environment()
    {
        $env = $this->getEnv();
        $name = 'Config/Environment/' . ucfirst($env) . '.php';

        foreach ($this->loader->find($name) as $file) {
            require_once $file->getRealPath();
            $class = $file->getClassName();
            $initializer = new $class();
            $initializer->configure($this);
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
        if (! $env) $env = self::ENV_DEVELOPMENT;

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
    protected function getEnvFromEnvironmentVariables()
    {
        // has env ?
        if ( $env = getenv('SAMURAI_ENV') ) {
            return $env;
        }

        return self::ENV_DEVELOPMENT;
    }
    
    
    


    /**
     * accessor config.
     *
     * = get all config. (as array)
     *
     *   $config = $app->config();
     *
     * = get config by simple key. (as mixed)
     *
     *   $config = $app->config('namespace.key');
     *
     * = set config by simple scalar variable.
     *
     *   $app->config('namespace.key', 'hello');
     *
     * = set config into array post. (last char is ".")
     *
     *   $app->config('namespace.somethings.', 'value1');
     *   $app->config('namespace.somethings.', 'value2');
     *   $config = $app->config('namespace.somethings');    // ["value1", "value2"]
     *
     * @access  public
     * @param   string  $key
     * @param   mixed   $value
     */
    public function config($key = null, $value = null)
    {
        // when key is null, then return all config.
        if (! $key) return $this->config;

        // when last char is ".", then behavior for array.
        $is_array = false;
        if (substr($key, -1) === '.') {
            $is_array = true;
            $key = substr($key, 0, -1);
        }

        // set config.
        if ($value !== null) {
            if ($is_array) {
                if (! array_key_exists($key, $this->config)) $this->config[$key] = array();
                array_push($this->config[$key], $value);
            } else {
                $this->config[$key] = $value;
            }
        }

        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
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

