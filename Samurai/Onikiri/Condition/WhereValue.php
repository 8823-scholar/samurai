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
 * Where Condition's value.
 *
 * @package     Onikiri
 * @subpackage  Condition
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class WhereValue
{
    /**
     * key
     *
     * @access  public
     * @param   string
     */
    public $key;

    /**
     * value
     *
     * @access  public
     * @param   mixed
     */
    public $value;

    /**
     * negative
     *
     * @access  public
     * @param   boolean
     */
    public $negative = false;

    /**
     * chain by.
     *
     * @access  public
     * @param   string
     */
    public $chain_by = WhereCondition::CHAIN_BY_AND;

    /**
     * parent.
     *
     * @access  public
     * @var     Samurai\Onikiri\Condition\Condition
     */
    public $parent;


    /**
     * constructor.
     *
     * @access  public
     * @param   WhereCondition  $where
     * @param   string          $key
     * @param   mixed           $value
     */
    public function __construct(WhereCondition $where, $key, $value)
    {
        $this->parent = $where;
        $this->key = $key;
        $this->value = $value;
    }


    /**
     * negative flag on.
     *
     * @access  public
     */
    public function not()
    {
        $this->negative = true;
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

        if ( $this->key ) {
            $sql[] = $this->key;
        }

        // when NULL
        if ( $this->value === null ) {
            $sql[] = $this->negative ? 'IS NOT NULL' : 'IS NULL';
        }

        // when array
        elseif ( is_array($this->value) ) {
            $sql[] = $this->negative ? 'NOT IN (' : 'IN (';
            $sub = array();
            foreach ( $this->value as $value ) {
                $sub[] = '?';
                $this->parent->parent->addParam($value);
            }
            $sql[] = join(', ', $sub);
            $sql[] = ')';
        }

        // normal.
        else {
            $sql[] = $this->negative ? '<>' : '=';
            $sql[] = '?';
            $this->parent->parent->addParam($this->value);
        }

        return join(' ', $sql);
    }
}

