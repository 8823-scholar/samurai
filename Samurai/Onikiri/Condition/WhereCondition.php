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

namespace Samurai\Onikiri\Condition;

/**
 * Where section class of condition.
 *
 * @package     Onikiri
 * @subpackage  Condition
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class WhereCondition extends BaseCondition
{
    /**
     * add condition.
     *
     * 1. $cond->where->add('foo = ?', $foo);
     * 2. $cond->where->add('foo = ? AND bar = ?', $foo, $bar);
     * 3. $cond->where->add('foo = ? AND bar = ?', [$foo, $bar]);
     * 4. $cond->where->add('foo = :foo AND bar = :bar', ['foo' => $foo, 'bar' => $bar]);
     *
     * @access  public
     */
    public function add()
    {
        $args = func_get_args();
        $condition = array_shift($args);

        if ( count($this->conditions) > 0 ) {
            $this->conditions[] = 'AND';
        }
        $this->conditions[] = $condition;

        while ( $param = array_shift($args) ) {
            if ( is_array($param) ) {
                $this->params = array_merge($this->params, $param);
            } else {
                $this->params[] = $param;
            }
        }
    }



    /**
     * convert to SQL.
     *
     * @access  public
     * @return  string
     */
    public function toSQL()
    {
        $sql = array();

        $sql[] = 'WHERE';

        if ( ! $this->conditions ) {
            $sql[] = '1';
        } else {
            $sql[] = join(' ', $this->conditions);
        }

        return join(" ", $sql);
    }
}

