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

namespace Samurai\Samurai\Component\Cache;

/**
 * using apc cache class.
 *
 * @package     Samurai.Component
 * @subpackage  Cache
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ApcCache extends ArrayCache
{
    /**
     * {@inheritdoc}
     */
    public function cache($key, $value, $expire = null)
    {
        return apc_store($key, $value, $expire ? $expire : 0);
    }

    /**
     * {@inheritdoc}
     */
    public function uncache($key)
    {
        parent::uncache($key);
        return apc_delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->has($key) ? parent::get($key) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (parent::has($key)) return true;

        $value = apc_fetch($key, $success);
        if ($success === true) {
            parent::cache($key, $value);
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported()
    {
        return extension_loaded('apc');
    }
}

