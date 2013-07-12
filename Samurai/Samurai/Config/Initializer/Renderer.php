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

namespace Samurai\Samurai\Config\Initializer;

use Samurai\Samurai\Component\Core\Initializer;
use Samurai\Samurai\Component\Renderer\Renderer as SamuraiRenderer;

/**
 * renderer initializer.
 *
 * @package     Samurai
 * @subpackage  Config.Initializer
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Renderer extends Initializer
{
    /**
     * {@inheritdoc}
     */
    public function configure($app)
    {
        $app->config('renderer.name', 'twig');
        $app->config('renderer.initialize.callback', array($this, 'initialize'));
        $app->config('renderer.auto_reload', true);
        $app->config('renderer.auto_escape_html', true);
    }


    /**
     * callback on initialize.
     *
     * @access  public
     * @param   Samurai\Samurai\Component\Renderer\Renderer $renderer
     */
    public function initialize(SamuraiRenderer $renderer)
    {
        /*
// register autoloader.
\Twig_Autoloader::register();


// set directory.
$twig_loader = null;
foreach ( $loader->getPaths($app->config('directory.template'), null, $app->getControllerSpaces()) as $path ) {
    if ( ! $twig_loader ) {
        $twig_loader = new \Twig_Loader_Filesystem($path);
    } else {
        $twig_loader->addPath($path);
    }
}
if ( $twig_loader ) {
    foreach ( $loader->getPaths($app->config('directory.layout'), null, $app->getControllerSpaces()) as $path ) {
        $twig_loader->addPath($path, 'layout');
    }
}


// init.
$twig->setLoader($twig_loader);
$twig->enableAutoReload();
$twig->setCache($loader->getPath($app->config('directory.temp'), null, $app->getControllerSpaces()) . DS . 'twig');


// default escape.
$filter = new \Twig_Extension_Escaper(true);
$twig->addExtension($filter);
        */
    }
}

