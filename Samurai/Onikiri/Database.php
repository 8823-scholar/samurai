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
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri;

/**
 * Database configuration and entity;
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Database
{
    /**
     * driver
     *
     * @access  public
     * @var     Driver\Driver
     */
    public $driver;

    /**
     * user
     *
     * @access  public
     * @var     string
     */
    public $user;

    /**
     * password
     *
     * @access  public
     * @var     string
     */
    public $password;

    /**
     * host name
     *
     * @access  public
     * @var     string
     */
    public $host_name;

    /**
     * post
     *
     * @access  public
     * @var     int
     */
    public $port;

    /**
     * database name
     *
     * @access  public
     * @var     string
     */
    public $database_name;

    /**
     * driver options
     *
     * @access  public
     * @var     array
     */
    public $options = array();

    /**
     * connection
     *
     * @access  public
     * @var     Connection
     */
    public $connection;

    /**
     * connection of slave
     *
     * @access  public
     * @var     Connection
     */
    public $connection_slave;

    /**
     * slaves
     *
     * @access  private
     * @var     array
     */
    private $_slaves = array();

    /**
     * master (if slave only.)
     *
     * @access  private
     * @var     Samurai\Onikiri\Database
     */
    private $_master;



    /**
     * constructor.
     *
     * @access  public
     * @param   array   $setting
     */
    public function __construct(array $setting)
    {
        foreach ( $setting as $key => $value ) {
            switch ( $key ) {
                case 'driver':
                    $this->setDriver($value);
                    break;
                case 'user':
                    $this->setUser($value);
                    break;
                case 'pass':
                    $this->setPassword($value);
                    break;
                case 'host':
                    $this->setHostName($value);
                    break;
                case 'database':
                    $this->setDatabaseName($value);
                    break;
                case 'port':
                    $this->setPort($value);
                    break;
                case 'slaves':
                    foreach ( $value as $slave ) {
                        $this->addSlave($slave);
                    }
                    break;
            }
        }
    }


    /**
     * Set driver
     *
     * @access  public
     * @param   string  $name
     */
    public function setDriver($name)
    {
        $manager = Manager::singleton();
        $this->driver = $manager->getDriver($name);
    }

    /**
     * Get driver.
     *
     * @access  public
     * @return  Driver\Driver
     */
    public function getDriver()
    {
        if ( $this->isSlave() ) {
            return $this->_master->getDriver();
        }
        return $this->driver;
    }


    /**
     * Set user
     *
     * @access  public
     * @param   string  $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user.
     *
     * @access  public
     * @return  string
     */
    public function getUser()
    {
        if ( $this->isSlave() && ! $this->user ) {
            return $this->_master->getUser();
        }
        return $this->user;
    }


    /**
     * Set password
     *
     * @access  public
     * @param   string  $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @access  public
     * @return  string
     */
    public function getPassword()
    {
        if ( $this->isSlave() && ! $this->password ) {
            return $this->_master->getPassword();
        }
        return $this->password;
    }


    /**
     * Set host name.
     *
     * @access  public
     * @param   string  $host
     */
    public function setHostName($host)
    {
        $this->host_name = $host;
    }

    /**
     * Get host name.
     *
     * @access  public
     * @return  string
     */
    public function getHostName()
    {
        return $this->host_name;
    }


    /**
     * Set port number.
     *
     * @access  public
     * @param   int     $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Get port number.
     *
     * @access  public
     * @return  int
     */
    public function getPort()
    {
        if ( $this->isSlave() && ! $this->port ) {
            return $this->_master->getPort();
        }
        return $this->port;
    }


    /**
     * Set database name.
     *
     * @access  public
     * @param   string  $database
     */
    public function setDatabaseName($database)
    {
        $this->database_name = $database;
    }

    /**
     * Get database name.
     *
     * @access  public
     * @return  string
     */
    public function getDatabaseName()
    {
        if ( $this->isSlave() && ! $this->database_name ) {
            return $this->_master->getDatabaseName();
        }
        return $this->database_name;
    }


    /**
     * Add slave.
     *
     * @access  public
     * @param   array   $setting
     */
    public function addSlave(array $setting)
    {
        $database = new Database($setting);
        $database->setMaster($this);
        $this->_slaves[] = $database;
    }

    
   
    /**
     * pick a slave.
     *
     * @access  public
     * @return  Database
     */
    public function pickSlave()
    {
        if ( ! $this->hasSlave() ) return $this;
        return $this->_slaves[array_rand($this->_slaves)];
    }


    /**
     * Set master configuration.
     *
     * @access  public
     * @param   Samurai\Onikiri\Database    $master
     */
    public function setMaster(Database $master)
    {
        $this->_master = $master;
    }


    /**
     * Get options
     *
     * @access  public
     * @return  array
     */
    public function getOptions()
    {
        $options = $this->options;
        return $options;
    }




    /**
     * connect to backend.
     *
     * @access  public
     * @return  Connection
     */
    public function connect()
    {
        $driver = $this->getDriver();
        return $driver->connect($this);
    }




    /**
     * has slaves ?
     *
     * @access  public
     * @return  boolean
     */
    public function hasSlave()
    {
        return count($this->_slaves) > 0;
    }


    /**
     * is slave ?
     *
     * @access  public
     * @return  boolean
     */
    public function isSlave()
    {
        return $this->_master !== null;
    }
}

