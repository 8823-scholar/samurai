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

namespace Samurai\Samurai\Component\Spec\PHPSpec;

use PhpSpec\Locator\PSR0\PSR0Resource as PhpSpecPSR0Resource;

/**
 * PHPSpec PSR0 Resource
 *
 * @package     Samurai
 * @subpackage  Component.Spec.PHPSpec
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class PSR0Resource extends PhpSpecPSR0Resource
{
    private $parts;
    private $locator;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $parts, PSR0Locator $locator)
    {
        parent::__construct($parts, $locator);

        $this->parts = $parts;
        $this->locator = $locator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSrcFilename()
    {
        $path = implode(DIRECTORY_SEPARATOR, $this->parts);
        $path = substr($path, strlen($this->locator->getSrcNamespace()));
        return $this->locator->getFullSrcPath() . $path . '.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getSrcNamespace()
    {
        $nsParts = $this->parts;
        array_pop($nsParts);

        return rtrim(implode('\\', $nsParts), '\\');
    }

    /**
     * {@inheritdoc}
     */
    public function getSrcClassname()
    {
        return implode('\\', $this->parts);
    }
}

