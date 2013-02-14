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

namespace App\Config;

use Samurai\Samurai;

/**
 * Config - Bootstrap
 *
 * @package     App
 * @subpackage  Config
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */

// path constants
define('Samurai\Samurai\Config\ROOT_DIR', dirname(dirname(__DIR__)));
define('Samurai\Samurai\Config\APP_DIR', dirname(__DIR__));
define('Samurai\Samurai\Config\CORE_DIR', dirname(dirname(__DIR__)) . '/Samurai/Samurai');


// composer autoload
$autoload_file = dirname(dirname(__DIR__)) . '/vendor/autoload.php';;
if ( file_exists($autoload_file) ) {
    require_once $autoload_file;
}


// environment
if ( ! defined('Samurai\Samurai\Config\ENV') ) {
    // from ENV
    if ( $env = getenv('SAMURAI_ENV') ) {
        define('Samurai\Samurai\Config\ENV', $env);
    }

    // default
    else {
        define('Samurai\Samurai\Config\ENV', 'development');
    }
}


// samurai bootsrap
require_once Samurai\Config\CORE_DIR . '/Config/bootstrap.php';

