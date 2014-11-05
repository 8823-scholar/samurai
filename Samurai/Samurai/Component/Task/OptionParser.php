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

/**
 * option parser.
 *
 * supported syntax:
 *     @option  long-name               basic option
 *     @option  long-name=default       with default value
 *     @option  long-name,v=default     with short option
 *     @require long-name               required option.
 *
 * @package     Samurai
 * @subpackage  Component.Task
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class OptionParser
{
    /**
     * regexp option
     *
     * @var     string
     */
    private $regexp_option = '/^@(option|require)\s+([\w\-,=]+)(\s+(.*))?/';


    /**
     * parse
     *
     * @param   string  $string
     * @return  Samurai\Samurai\Component\Task\OptionDefine
     * @throws  InvalidArgumentException
     */
    public function parse($string)
    {
        if (! $this->isSupports($string)) throw new \InvalidArgumentException('invalid string.');

        $define = new OptionDefine();
        preg_match($this->regexp_option, $string, $matches);

        // optional | required
        if ($matches[1] === 'require') $define->required();

        // long-name
        $define->setName($this->pickName($matches[2]));
        
        // short-name
        $define->setShortName($this->pickShortName($matches[2]));

        // defalt value
        $define->setDefault($this->pickValue($define, $matches[2]));
        
        // description
        $define->setDescription($this->pickDescription($matches));

        return $define;
    }

    /**
     * pick name
     *
     * @param   string  $syntax
     * @return  string
     */
    private function pickName($syntax)
    {
        $parts = preg_split('/,|=/', $syntax);
        return trim($parts[0]);
    }
    
    /**
     * pick short name
     *
     * @param   string  $syntax
     * @return  string
     */
    private function pickShortName($syntax)
    {
        $name = null;
        if (preg_match('/,(\w+)=?/', $syntax, $matches)) {
            $name = $matches[1];
        }
        return $name;
    }
    
    /**
     * pick value
     *
     * @param   Samurai\Samurai\Component\Task\OptionDefine $define
     * @param   string  $syntax
     * @return  string
     */
    private function pickValue(OptionDefine $define, $syntax)
    {
        $value = $define->isRequired() ? null : true;
        if (preg_match('/=(.+)/', $syntax, $matches)) {
            $value = $matches[1];
        }
        return $value;
    }

    /**
     * pick description
     *
     * @param   array   $matches
     * @return  string
     */
    private function pickDescription(array $matches)
    {
        return isset($matches[4]) ? $matches[4] : '';
    }


    /**
     * formatter
     *
     * @param   Samurai\Samurai\Component\Task\Option   $option
     * @return  string
     */
    public function formatter(Option $option)
    {
        $lines = [];
        $options = [];
        $span = 30;
        foreach ($option->getDefinitions() as $define) {
            $key = '--' . $define->getName();
            if ($define->hasShortName()) {
                $key .= ',-' . $define->getShortName();
            }
            if ($define->hasDefault()) {
                switch ($define->getDefault()) {
                    case false:
                        $value = '=false';
                        break;
                    default:
                        $value = '=' . $define->getDefault();
                        break;
                }
                $key .= $value;
            }
            $options[$key] = $define->isRequired() ?
                '(required) ' . $define->getDescription() : $define->getDescription();

            // compare key length.
            if (strlen($key) > $span) $span = strlen($key);
        }

        foreach ($options as $key => $value) {
            $lines[] = sprintf("%-{$span}s  %s", $key, $value);
        }

        return join(PHP_EOL, $lines);
    }


    /**
     * is supports syntax ?
     *
     * @param   string  $string
     * @return  boolean
     */
    public function isSupports($string)
    {
        $string = trim($string);
        return preg_match($this->regexp_option, $string) === 1;
    }
}
