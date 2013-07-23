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

namespace Samurai\Console;

use Samurai\Samurai;

// composer autoload
$autoload_file = dirname(dirname(__DIR__)) . '/vendor/autoload.php';;
if ( file_exists($autoload_file) ) {
    require_once $autoload_file;
}

/**
 * Application class.
 *
 * @package     Samurai.Console
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Application extends Samurai\Application
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();
        
        // environment
        $this->setEnv($this->getEnvFromEnvironmentVariables());

        // application dir.
        $this->addAppPath(__DIR__, __NAMESPACE__, self::PRIORITY_LOW);
        
        // default spec namespaces
        $this->config('spec.default.namespaces', ['samurai:samurai', 'samurai:console']);
    }


    /**
     * configure from application console
     *
     * @access  public
     * @param   Samurai\Application $app
     */
    public function inheritConfigure(Samurai\Application $app)
    {
        // environment
        $app->setEnv($this->getEnvFromEnvironmentVariables());

        // application dir.
        $app->addAppPath(__DIR__, __NAMESPACE__, self::PRIORITY_LOW);
    }


    /**
     * {@inheritdoc}
     */
    protected function getEnvFromEnvironmentVariables()
    {
        // has request ?
        $opts = getopt('', array('ENV:'));
        if (isset($opts['ENV'])) {
            return $opts['ENV'];
        }

        return parent::getEnvFromEnvironmentVariables();
    }
}

