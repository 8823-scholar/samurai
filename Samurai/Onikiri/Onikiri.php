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

namespace Samurai\Onikiri;

use Samurai\Onikiri\Schema\TableSchema;
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Samurai\Component\Core\YAML;
use Samurai\Samurai\Component\Cache\ApcCache;
use Samurai\Samurai\Component\Cache\ArrayCache;

/**
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Onikiri
{
    /**
     * config
     *
     * @var     Samurai\Onikiri\Configuration
     */
    public $config;
    
    /**
     * database configurations
     *
     * @var     array
     */
    private $_databases = [];

    /**
     * table instances
     *
     * @var     array
     */
    private $_table_factory = [];

    /**
     * transaction
     *
     * @var     Samurai\Onikiri\Transaction
     */
    private $_tx;

    /**
     * lock modes
     *
     * @const   int
     */
    const LOCK_FOR_UPDATE = 1;
    const LOCK_IN_SHARED = 2;

    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * configure
     *
     * @return  Samurai\Onikiri\Configuration
     */
    public function configure()
    {
        $this->config = new Configuration();

        $this->config->setNamingStrategy(new Mapping\DefaultNamingStrategy());

        $cache = new ApcCache();
        if ($cache->isSupported()) {
            $cache->setPrefix('samurai.onikiri.table.schema');
        } else {
            $cache = new ArrayCache();
        }
        $this->config->setSchemaCacher($cache);

        return $this->config;
    }
    
    
    /**
     * import database configurations
     *
     * @param   string  $file
     */
    public function import($file)
    {
        if (! file_exists($file)) return;

        $settings = YAML::load($file);
        foreach ($settings as $alias => $setting) {
            $this->_databases[$alias] = new Database($setting);
        }
    }
    
    
    /**
     * get database configuration.
     *
     * @param   string  $alias
     * @param   string  $target
     * @return  Database
     */
    public function getDatabase($alias, $target = Database::TARGET_MASTER)
    {
        $database = isset($this->_databases[$alias]) ? $this->_databases[$alias] : null;
        if ($target === Database::TARGET_SLAVE) {
            $database = $database->pickSlave();
        }
        return $database;
    }

    /**
     * set database.
     *
     * @param   string  $alias
     * @param   Samurai\Onikiri\Database    $database
     */
    public function setDatabase($alias, Database $database)
    {
        $this->_databases[$alias] = $database;
    }


    /**
     * get model instance
     *
     * User -> UserTable.php
     * UserArticle -> UserArticleTable.php
     *
     * @param   string  $alias
     * @return  Samurai\Onikiri\EntityTable
     * @throws  Samurai\Onikiri\Exception\EntityTableNotFoundException
     */
    public function getTable($alias)
    {
        // has factory ?
        if (isset($this->_table_factory[$alias])) return $this->_table_factory[$alias];

        $class_name = $this->config->getNamingStrategy()->aliasToTableClassName($alias);
        foreach ($this->config->getModelDirs() as $dir) {
            $class_full_name  = sprintf('%s\\%s', $dir['namespace'], $class_name);
            if (class_exists($class_full_name)) {
                $table = new $class_full_name($this);
                if ($container = $this->raikiri()) {
                    $table->setContainer($container);
                }
                $this->_table_factory[$alias] = $table;
                return $table;
            }
        }
        throw new Exception\EntityTableNotFoundException();
    }


    /**
     * get table schema instance
     *
     * @param   string  $table
     * @param   string  $database
     * @return  Samurai\Onikiri\Schema\TableSchema
     */
    public function getTableSchema($table, $database = 'base')
    {
        $cacher = $this->config->getSchemaCacher();
        if ($cacher->has($table)) return $cacher->get($table);

        $driver = $this->getDatabase($database)->getDriver();
        $connection = $this->establishConnection($database, Database::TARGET_SLAVE);
        $describe = $driver->getTableDescribe($connection, $table);

        $schema = new TableSchema($table, $describe);
        $cacher->cache($table, $schema);
        return $schema;
    }
    
    
    /**
     * connect to backend.
     *
     * @param   string  $alias
     * @param   string  $target
     */
    public function establishConnection($alias, $target = Database::TARGET_MASTER)
    {
        $database = $this->getDatabase($alias, $target);
        return $database->connect();
    }


    /**
     * get transaction instance
     *
     * @return  Samurai\Onikiri\Transaction
     */
    public function getTx()
    {
        if ($this->_tx && $this->_tx->isValid()) return $this->_tx;

        return $this->_tx = new Transaction();
    }
}

