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

namespace Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Core\Accessor;

/**
 * task option defeinition.
 *
 * @package     Samurai
 * @subpackage  Component.Task
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class OptionDefine
{
    /**
     * long name
     *
     * @var     string
     */
    public $name;

    /**
     * short name
     *
     * @var     string
     */
    public $short_name;

    /**
     * required
     *
     * @var     boolean
     */
    public $required = false;

    /**
     * value
     *
     * @var     mixed
     */
    public $value = null;

    /**
     * default
     *
     * @var     mixed
     */
    public $default = null;

    /**
     * description
     *
     * @var     string
     */
    public $description;


    /**
     * @traits
     */
    use Accessor;


    /**
     * required
     */
    public function required()
    {
        $this->required = true;
    }

    /**
     * is required ?
     *
     * @return  boolean
     */
    public function isRequired()
    {
        return $this->required;
    }


    /**
     * has short name ?
     *
     * @return  boolean
     */
    public function hasShortName()
    {
        return $this->getShortName() !== null;
    }


    /**
     * has default ?
     *
     * @return  boolean
     */
    public function hasDefault()
    {
        return $this->default !== null && $this->default !== true;
    }
}

