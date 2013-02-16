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

namespace Samurai\Samurai\Component\Core;

use Samurai\Raikiri;

/**
 * get, set parameters.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Parameters extends Raikiri\Object
{
    /**
     * parameters
     *
     * @access  protected
     * @var     array
     */
    protected $_params = array();
    
    /**
     * @dependencies
     */
    public $ArrayUtil;


    /**
     * import
     *
     * @access  protected
     * @param   array   $data
     */
    protected function _import(array $data)
    {
        $this->_params = $this->ArrayUtil->merge($this->_params, $data);
    }


    /**
     * get.
     *
     * @access  public
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $default;
        foreach ( $keys as $i => $_key ) {
            if ( ! $i && isset($this->_params[$_key]) ) {
                $value = $this->_params[$_key];
            } elseif ( is_array($value) && isset($value[$_key]) ) {
                $value = $value[$_key];
            } else {
                $value = $default;
                break;
            }
        }
        return $value;
    }


    /**
     * get all.
     *
     * @access  public
     * @return  array
     */
    public function getAll()
    {
        return $this->_params;
    }
}

