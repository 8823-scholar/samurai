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

use PDOStatement;
use Samurai\Onikiri\Connection;

/**
 * Statement (base is PDO)
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Statement extends PDOStatement
{
    /**
     * connection.
     *
     * @access  public
     * @var     Connection
     */
    public $connection;


    /**
     * @override
     */
    private function __construct()
    {
    }


    /**
     * For support number, named mixed placeholder.
     *
     * @override
     */
    public function bindValue($parameter, $value, $data_type = Connection::PARAM_STR)
    {
        // numbered holder to named holder.
        if (is_int($parameter)) {
            $parameter = ':numbered_holder_' . $parameter;
        }
        return parent::bindValue($parameter, $value, $data_type);
    }



    /**
     * Set connection.
     *
     * @access  public
     * @param   Connection  $onnection
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get connection.
     *
     * @access  public
     * @return  Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }


    /**
     * get last insert id.
     *
     * @access  public
     * @return  string
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }



    /**
     * is success ?
     *
     * @access  public
     * @return  boolean
     */
    public function isSuccess()
    {
        return $this->errorCode() === '00000';
    }
}

