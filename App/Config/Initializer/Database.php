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

namespace App\Config\Initializer;

use Samurai\Samurai\Application as SamuraiApplication;
use Samurai\Samurai\Component\Core\Initializer;
use Samurai\Onikiri\Onikiri;

/**
 * database initializer.
 *
 * @package     Samurai
 * @subpackage  Config.Initializer
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Database extends Initializer
{
    /**
     * {@inheritdoc}
     */
    public function configure(SamuraiApplication $app)
    {
        $app->config('container.callback.initialized.', function($c) use ($app) {
            $onikiri = new Onikiri();
            $config = $onikiri->configure();

            // register model directory.
            $loader = $app->getLoader();
            foreach ($loader->find($app->config('directory.model')) as $dir) {
                $config->addModelDir($dir->toString(), $dir->getNameSpace());
            }

            // load configuration.
            // App/Config/Database/[env].yml
            $file = $loader->find($app->config('directory.config.database') . DS . $app->getEnv() . '.yml')->first();
            if ($file) $onikiri->import($file);

            $c->register('onikiri', $onikiri);
        });
    }
}

