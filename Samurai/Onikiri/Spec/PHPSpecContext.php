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
use Samurai\Onikiri\Driver\MysqlDriver;
use Samurai\Onikiri\Driver\PgsqlDriver;
use Samurai\Onikiri\Driver\SqliteDriver;
use Samurai\Onikiri\Database;
use PhpSpec\Exception\Example\SkippingException;

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
    protected $_spec_driver;
    protected $_spec_database;
    
    protected function _setMySQLDatabase()
    {
        $this->_spec_driver = new MysqlDriver();
        $this->_spec_database = new Database($this->_getMySQLDefinition());
    }

    protected function _getMySQLDefinition()
    {
        $app = $this->__getContainer()->get('application');
        if (! $app->config('spec.mysql.database.defined'))
            throw new SkippingException('Set env "ONIKIRI_SPEC_MYSQL_(USER|PASS|HOST|PORT|DATABASE)"');

        $difinition = [
            'driver' => 'mysql',
            'user' => $app->config('spec.mysql.database.user'),
            'pass' => $app->config('spec.mysql.database.pass'),
            'host' => $app->config('spec.mysql.database.host'),
            'port' => $app->config('spec.mysql.database.port'),
            'database' => $app->config('spec.mysql.database.name'),
        ];
        return $difinition;
    }

    protected function _setMySQLDefinitionFromEnv(Database $d)
    {
        $difinition = $this->_getMySQLDefinition();

        $d->setUser($difinition['user']);
        $d->setPassword($difinition['pass']);
        $d->setHostName($difinition['host']);
        $d->setPort($difinition['port']);
        $d->setDatabaseName($difinition['database']);
    }
}

