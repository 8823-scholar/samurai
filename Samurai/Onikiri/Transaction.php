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
     * begin ?
     *
     * @var     boolean
     */
    protected $_begin = false;

    /**
     * connection
     *
     * @var     Samurai\Onikiri\Connection
     */
    protected $_connection;


    /**
     * get connection
     *
     * @return  Samurai\Onikiri\Connection
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * set connection
     *
     * @param   Samurai\Onikiri\Connection  $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->_connection = $connection;
    }


    /**
     * begin transaction
     */
    public function begin()
    {
        if ($this->_begin) return;

        $connection = $this->getConnection();
        $connection->beginTransaction();
        $this->_begin = true;
    }

    /**
     * rollback transaction
     *
     * @throw   Samurai\Onikiri\Exception\TransactionFailedException
     */
    public function rollback($message = null)
    {
        $connection = $this->getConnection();
        $connection->rollback();

        $this->_valid = false;
        throw new TransactionFailedException($message ? $message : 'failed to transaction.');
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
}

