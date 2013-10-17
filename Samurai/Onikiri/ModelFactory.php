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

use Samurai\Onikiri\Manager;

/**
 * Model factory class.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ModelFactory
{
    /**
     * manager
     *
     * @access  public
     * @var     Samurai\Onikiri\Manager
     */
    public $manager;

    /**
     * models
     *
     * @access  private
     * @var     array
     */
    private $_models = array();

    
    /**
     * constructor.
     *
     * @access  public
     * @param   Samurai\Onikiri\Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * get model.
     *
     * @access  public
     * @param   string  $name
     * @return  Model
     */
    public function get($name)
    {
        if (! isset($this->_models[$name])) {
            $this->_models[$name] = $this->create($name);
        }
        return $this->_models[$name];
    }


    /**
     * create and return model instance.
     *
     * @access  public
     * @param   string  $name
     * @return  Model
     */
    public function create($name)
    {
        $names = preg_split('/(?=[A-Z])/', $name);

        foreach ($this->manager->getModelSpaces() as $path => $namespace) {
            $file_path = $path . join(DS, $names) . 'Model.php';
            $class_name = $namespace . join('\\', $names) . 'Model';
            if (class_exists($class_name)) {
                $model = new $class_name();
                return $model;
            } elseif (file_exists($file_path)) {
                require_once $file_path;
                $model = new $class_name();
                return $model;
            }
        }

        throw new \InvalidArgumentException("No such model. -> {$name}");
    }
}

