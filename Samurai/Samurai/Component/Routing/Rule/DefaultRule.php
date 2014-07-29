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
 * Default Rule.
 *
 * @package     Samurai
 * @subpackage  Componen.Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class DefaultRule extends Rule
{
    /**
     * {@inheritdoc}
     */
    public function match($path)
    {
        $paths = explode(DS, $path);
        array_shift($paths);
        if (count($paths) < 2) return false;

        // more population pattern.
        // /:controller/:action
        if (preg_match('|^/(\w+)/(\w+)/?$|', $path, $matches)) {
            $this->setController($matches[1]);
            $this->setAction($matches[2]);
            return true;
        }

        // has id, or nested controller ?
        // /:controller/:action/:id
        // /:controller/:controller/:action
        if (count($paths) > 2) {
            $action = array_pop($paths);

            // has format.
            if (preg_match('/^(\w+)\.(\w+)$/', $action, $matches)) {
                $action = $matches[1];
                $this->setParam('format', $matches[2]);
            }

            if (is_numeric($action)) {
                $this->setParam('id', (int)$action);
                $action = array_pop($paths);
            }
            $controller = join('_', $paths);
            $this->setController($controller);
            $this->setAction($action);
            return true;
        }

        return false;
    }
}

