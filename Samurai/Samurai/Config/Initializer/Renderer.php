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

use Samurai\Samurai\Application;
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
    public function configure(Application $app)
    {
        $app->config('renderer.name', 'twig');
        $app->config('renderer.auto_reload', true);
        $app->config('renderer.auto_escape_html', true);
        $app->config('renderer.initializers.default', function(Application $app, SamuraiRenderer $renderer) {
            $this->initialize($app, $renderer);
        });
    }


    /**
     * callback on initialize.
     *
     * @access  public
     * @param   Samurai\Samurai\Application $app
     * @param   Samurai\Samurai\Component\Renderer\Renderer $renderer
     */
    public function initialize(Application $app, SamuraiRenderer $renderer)
    {
        switch ($app->config('renderer.name')) {
            case 'twig':
                $this->initialize4Twig($app, $renderer);
                break;
        }
    }

    /**
     * initialize for twig.
     *
     * @access  protected
     * @param   Samurai\Samurai\Application $app
     * @param   Samurai\Samurai\Component\Renderer\Renderer $renderer
     */
    protected function initialize4Twig(Application $app, SamuraiRenderer $renderer)
    {
        // register autoloader.
        \Twig_Autoloader::register();

        // set directory.
        $twig_loader = null;
        foreach ($app->loader->find($app->config('directory.template')) as $dir) {
            if (! $twig_loader) {
                $twig_loader = new \Twig_Loader_Filesystem($dir->getRealPath());
            } else {
                $twig_loader->addPath($dir->getRealPath());
            }
        }
        if ($twig_loader) {
            foreach ($app->loader->find($app->config('directory.layout')) as $dir) {
                $twig_loader->addPath($dir->getRealPath(), 'layout');
            }
        }

        // init.
        $twig = $renderer->getEngine();
        $twig->setLoader($twig_loader);
        $twig->setCache($app->loader->find($app->config('directory.temp'))->first() . DS . 'twig');
        if ($app->config('renderer.auto_reload')) $twig->enableAutoReload();

        // default escape.
        if ($app->config('renderer.auto_escape_html')) {
            $filter = new \Twig_Extension_Escaper(true);
            $twig->addExtension($filter);
        }
    }
}

