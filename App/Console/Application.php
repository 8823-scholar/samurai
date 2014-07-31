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

namespace App\Console;

use App;
use Samurai\Console as SamuraiConsole;

require_once dirname(__DIR__) . '/Application.php';

/**
 * Application class.
 *
 * @package     App
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Application extends App\Application
{
    /**
     * samurai console application.
     *
     * @access  private
     */
    private $console_app;


    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        // Samurai console application configure inherit.
        $this->inheritConsoleApplication();
        
        // application dir.
        $this->addAppPath(__DIR__, __NAMESPACE__, self::PRIORITY_HIGH);
        $this->config('controller.namespaces', ['App\Console', 'Samurai\Console']);

        // default spec namespaces
        $this->config('spec.default.namespaces', ['app', 'app:console']);
    }



    /**
     * inherit samurai console application.
     *
     * @access  private
     */
    private function inheritConsoleApplication()
    {
        $app = $this->getConsoleApplication();
        $app->inheritConfigure($this);
    }
    


    /**
     * get samurai console application.
     *
     * @access  private
     */
    private function getConsoleApplication()
    {
        if (! $this->console_app) {
            $this->console_app = new SamuraiConsole\Application();
        }
        return $this->console_app;
    }
}

