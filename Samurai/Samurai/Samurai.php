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

/**
 * Framework main class.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Samurai
{
    /**
     * version
     *
     * @const   string
     */
    const VERSION = '3.0.0';

    /**
     * state.
     *
     * @const   string
     */
    const STATE = 'beta';


    /**
     * Get version.
     *
     * @access  public
     * @return  string
     */
    public static function getVersion()
    {
        return self::VERSION;
    }


    /**
     * Get environment constant
     *
     * @access  public
     * @return  string
     */
    public static function getEnv()
    {
        $env = 'development';
        if ( defined('Samurai\Samurai\Config\ENV') ) {
            $env = \Samurai\Samurai\Config\ENV;
        }
        return $env;
    }


    /**
     * get path.
     *
     * @access  public
     */
    public static function getPath()
    {
        // defined consttant ?
        return defined('Samurai\Samurai\Config\CORE_DIR') ? Config\CORE_DIR : __DIR__;
    }




    /**
     * get container
     *
     * @access  public
     * @return  Samurai\Raikiri\Container
     */
    public function getContainer()
    {
        return Raikiri\ContainerFactory::get('samurai');
    }
}

