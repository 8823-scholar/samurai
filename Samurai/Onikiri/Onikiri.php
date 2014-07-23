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

namespace Samurai\Onikiri;

/**
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Onikiri
{
    /**
     * config
     *
     * @var     Samurai\Onikiri\Configuration
     */
    public $config;


    /**
     * configure
     *
     * @return  Samurai\Onikiri\Configuration
     */
    public function configure()
    {
        $this->config = new Configuration();

        $this->config->setNamingStrategy(new Mapping\DefaultNamingStrategy());

        return $this->config;
    }


    /**
     * get model instance
     *
     * User -> UserTable.php
     * UserArticle -> UserArticleTable.php
     *
     * @param   string  $alias
     * @return  Samurai\Onikiri\EntityTable
     * @throws  Samurai\Onikiri\Exception\EntityTableNotFoundException
     */
    public function getTable($alias)
    {
        $class_name = $this->config->getNamingStrategy()->aliasToTableClassName($alias);
        foreach ($this->config->getModelDirs() as $dir) {
            $file_name = sprintf('%s/%s.php', $dir['dir'], $class_name);
            $class_full_name  = sprintf('%s\\%s', $dir['namespace'], $class_name);
            if (file_exists($file_name)) {
                require_once $file_name;
                return new $class_full_name();
            }
        }
        throw new Exception\EntityTableNotFoundException();
    }
}

