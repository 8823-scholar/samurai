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

/**
 * spec initializer.
 *
 * @package     Samurai
 * @subpackage  Config.Initializer
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Spec extends Initializer
{
    /**
     * {@inheritdoc}
     */
    public function configure(Application $app)
    {
        $app->config('spec.mysql.database.defined', false);
        $app->config('spec.initializers.mysql', function($app) {
            $request = $app->getRaikiri()->get('request');

            $user = $request->getEnv('ONIKIRI_SPEC_MYSQL_USER');
            $pass = $request->getEnv('ONIKIRI_SPEC_MYSQL_PASS', '');
            $host = $request->getEnv('ONIKIRI_SPEC_MYSQL_HOST', 'localhost');
            $port = $request->getEnv('ONIKIRI_SPEC_MYSQL_PORT', 3306);
            $name = $request->getEnv('ONIKIRI_SPEC_MYSQL_DATABASE');
            if (! $user || ! $name) return;

            $app->config('spec.mysql.database.user', $user);
            $app->config('spec.mysql.database.pass', $pass);
            $app->config('spec.mysql.database.host', $host);
            $app->config('spec.mysql.database.port', $port);
            $app->config('spec.mysql.database.name', $name);
            $app->config('spec.mysql.database.defined', true);
        });
        
        $app->config('spec.pgsql.database.defined', false);
        $app->config('spec.initializers.pgsql', function($app) {
            $request = $app->getRaikiri()->get('request');

            $user = $request->getEnv('ONIKIRI_SPEC_PGSQL_USER');
            $pass = $request->getEnv('ONIKIRI_SPEC_PGSQL_PASS', '');
            $host = $request->getEnv('ONIKIRI_SPEC_PGSQL_HOST', 'localhost');
            $port = $request->getEnv('ONIKIRI_SPEC_PGSQL_PORT', 5432);
            $name = $request->getEnv('ONIKIRI_SPEC_PGSQL_DATABASE');
            if (! $user || ! $name) return;

            $app->config('spec.pgsql.database.user', $user);
            $app->config('spec.pgsql.database.pass', $pass);
            $app->config('spec.pgsql.database.host', $host);
            $app->config('spec.pgsql.database.port', $port);
            $app->config('spec.pgsql.database.name', $name);
            $app->config('spec.pgsql.database.defined', true);
        });
    }
}

