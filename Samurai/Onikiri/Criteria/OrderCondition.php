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

namespace Samurai\Onikiri\Criteria;

/**
 * Order condition class.
 *
 * @package     Samurai.Onikiri
 * @subpackage  Criteria
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class OrderCondition extends BaseCondition
{
    /**
     * add order by field.
     *
     * @param   string  $column
     * @param   array   $params
     */
    public function addByField($column, array $params)
    {
        $value = [];
        $value[] = sprintf('FIELD(%s,', $column);

        $sub = [];
        while ($param = array_shift($params)) {
            $sub[] = '?';
            $this->params[] = $param;
        }
        $value[] = join(', ', $sub) . ')';
        $this->set(join(' ', $value));
        return $this;
    }


    /**
     * convert to SQL
     *
     * @return  string
     */
    public function toSQL()
    {
        if (! $this->has()) return '';

        $sql = [];
        $sql[] = 'ORDER BY';

        $sub = array();
        foreach ($this->conditions as $condition) {
            $sub[] = $condition;
        }
        $sql[] = join(', ', $sub);

        return join(' ', $sql);
    }
}

