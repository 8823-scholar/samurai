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

use Samurai\Onikiri\Exception\TransactionFailedException;

/**
 * Transaction
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Transaction
{
    /**
     * valid ?
     *
     * @var     boolean
     */
    protected $_valid = true;

    /**
     * connection
     *
     * @var     array(Samurai\Onikiri\Connection)
     */
    protected $_connections = [];


    /**
     * get connections
     *
     * @return  Samurai\Onikiri\Connection
     */
    public function getConnections()
    {
        return $this->_connections;
    }

    /**
     * set connection
     *
     * @param   Samurai\Onikiri\Connection  $connection
     */
    public function setConnection(Connection $connection)
    {
        if (! in_array($connection, $this->_connections, true)) {
            $this->_connections[] = $connection;
        }
    }


    /**
     * begin transaction
     */
    public function begin()
    {
        foreach ($this->getConnections() as $connection) {
            if (! $connection->inTx()) $connection->beginTransaction();
        }
    }

    /**
     * commit transaction
     */
    public function commit()
    {
        foreach ($this->getConnections() as $connection) {
            $connection->commit();
        }

        $this->_valid = false;
    }

    /**
     * rollback transaction
     *
     * @throw   Samurai\Onikiri\Exception\TransactionFailedException
     */
    public function rollback($message = null)
    {
        foreach ($this->getConnections() as $connection) {
            $connection->rollback();
        }

        $this->_valid = false;
        throw new TransactionFailedException($message ? $message : 'failed to transaction.');
    }

    /**
     * rollback transaction without throw
     */
    public function rollbackWithoutThrow()
    {
        try {
            $this->rollback();
        } catch (TransactionFailedException $e) {
        }
    }


    /**
     * is valid ?
     *
     * @return  boolean
     */
    public function isValid()
    {
        return $this->_valid;
    }


    /**
     * in transaction ?
     *
     * @return  boolean
     */
    public function inTx()
    {
        if (! $connections = $this->getConnections()) return false;
        foreach ($connections as $connection) {
            if (! $connection->inTx()) return false;
        }
        return true;
    }
}

