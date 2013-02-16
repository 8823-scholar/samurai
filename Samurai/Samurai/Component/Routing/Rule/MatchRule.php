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

namespace Samurai\Samurai\Component\Routing\Rule;

/**
 * Routing Rule "Match"
 *
 * matching routing rule.
 *
 * @package     Samurai
 * @subpackage  Component.Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class MatchRule extends Rule
{
    /**
     * constructor
     *
     * @access  public
     */
    public function __construct($rule)
    {
        foreach ( $rule as $key => $value ) {
            switch ( $key ) {
                case 'as':
                    $this->setName($value);
                    break;
                case 'controller':
                    $this->setController($value);
                    break;
                case 'action':
                    $this->setAction($value);
                    break;
                default:
                    // when numeric key, then path
                    if ( is_numeric($key) ) {
                        $this->setPath($value);
                    // else key is path, and value is action
                    } else {
                        $this->setPath($key);
                        $this->setAction($value);
                    }
                    break;
            }
        }
    }



    /**
     * @implements
     */
    public function match($path)
    {
        return false;
    }
}

