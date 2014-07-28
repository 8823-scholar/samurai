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

namespace Samurai\Samurai\Filter;

use Samurai\Raikiri\DependencyInjectable;

/**
 * Filter class.
 *
 * @package     Samurai
 * @subpackage  Filter
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Filter
{
    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * execute.
     *
     * prefilter -> next filter::execute -> postfilter
     */
    public function execute()
    {
        $this->prefilter();
        $this->chainFilter();
        $this->postfilter();
    }


    /**
     * Pre filter.
     *
     * execute brefore action.
     *
     * @access  public
     */
    public function prefilter()
    {
        // TODO:logger
    }
    
    
    /**
     * Post filter.
     *
     * execute after action.
     *
     * @access  public
     */
    public function postfilter()
    {
        // TODO:logger
    }


    /**
     * chaining filter.
     *
     * @access  public
     */
    public function chainFilter()
    {
        $this->FilterChain->next();
        $this->FilterChain->execute();
    }





    /**
     * set attribute.
     *
     * @access  public
     * @param   string  $key
     * @param   mixed   $value
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * set all attributes
     *
     * @param   array   $attributes
     * @param   boolean $force
     */
    public function setAttributes($attributes, $force = false)
    {
        if ($force) {
            $this->attributes = $attributes;
        } else {
            $this->attributes = array_merge($this->attributes, $attributes);
        }
    }


    /**
     * get attribute.
     *
     * @param   string  $key
     * @param   mixed   $default
     */
    public function getAttribute($key, $default = null)
    {
    }


    /**
     * get self name.
     *
     * @return  string
     */
    public function getName()
    {
        $names = explode('\\', get_class($this));
        return preg_replace('/Filter$/', '', array_pop($names));
    }
}

