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

namespace Samurai\Samurai\Component\Expression;

/**
 * Base expression class.
 *
 * @package     Samurai
 * @subpackage  Component.Expression
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
abstract class Expression
{
    /**
     * value
     *
     * @access  public
     * @var     string
     */
    public $value;

    /**
     * create expression instance(glob or regex).
     *
     * @access  public
     * @param   string  $value
     * @return  Samurai\Samurai\Component\Expression\Expression
     */
    public static function create($value)
    {
        try {
            $expr = new RegexExpression($value);
            return $expr;
        } catch(\Exception $E) {
            $expr = new GlobExpression($value);
            return $expr;
        }
    }


    /**
     * value is match ?
     *
     * @access  public
     * @param   string
     * @return  boolean
     */
    abstract public function isMatch($value);


    /**
     * Get regxp pattern
     *
     * @access  public
     * @return  string
     */
    abstract public function getRegexPattern();
}
