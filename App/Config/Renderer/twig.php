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

namespace App\Config\Renderer;

use Samurai\Raikiri;
use Samurai\Samurai\Component\Core\Loader;

/**
 * bootstrap of "Twig" renderer.
 *
 * @package     Samurai
 * @subpackage  Config.Renderer
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */

// get DI.
$container = Raikiri\ContainerFactory::get();
$config = $container->getComponent('Config');


// register autoloader.
\Twig_Autoloader::register();


// set directory.
$loader = new \Twig_Loader_Filesystem(Loader::getPath($config->get('directory.template')));
$loader->addPath(Loader::getPath($config->get('directory.layout')), 'layout');


// init.
$twig = new \Twig_Environment($loader, array(
    'cache' => Loader::getPath($config->get('directory.temp')) . DS . 'twig',
    'auto_reload' => true,
));


// default escape.
$filter = new \Twig_Extension_Escaper(true);
$twig->addExtension($filter);



// return engine.
return $twig;

