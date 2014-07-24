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

namespace Samurai\Onikiri\Spec;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext as SamuraiPHPSpecContext;
use Samurai\Onikiri\Database;

/**
 * PHPSpecContext for onikiri
 *
 * @package     Samurai.Onikiri
 * @subpackage  Spec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class PHPSpecContext extends SamuraiPHPSpecContext
{
    protected function _setMySQLDefinitionFromEnv(Database $d)
    {
        $request = $this->__getContainer()->get('Request');

        $user = $request->getEnv('ONIKIRI_SPEC_MYSQL_USER');
        $pass = $request->getEnv('ONIKIRI_SPEC_MYSQL_PASS', '');
        $host = $request->getEnv('ONIKIRI_SPEC_MYSQL_HOST', 'localhost');
        $port = $request->getEnv('ONIKIRI_SPEC_MYSQL_PORT', 3306);
        $database = $request->getEnv('ONIKIRI_SPEC_MYSQL_DATABASE');
        if (! $user) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_USER"');
        if (! $host) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_HOST"');
        if (! $port) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_PORT"');
        if (! $database) throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_DATABASE"');

        $d->setUser($user);
        $d->setPassword($pass);
        $d->setHostName($host);
        $d->setPort($port);
        $d->setDatabaseName($database);
    }
}

