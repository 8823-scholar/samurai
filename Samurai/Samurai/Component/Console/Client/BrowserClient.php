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

namespace Samurai\Samurai\Component\Console\Client;

use ReflectionObject;
use Samurai\Raikiri\Container;
use Samurai\Raikiri\DependencyInjectable;
use Samurai\Onikiri\EntityTable;
use Samurai\Samurai\Component\Response\Response;
use Samurai\Samurai\Component\Response\Optimizer;

/**
 * console browser client
 *
 * @package     Samurai
 * @subpackage  Component.Console
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class BrowserClient extends Client implements Optimizer
{
    /**
     * messages
     *
     * @var     array
     */
    public $messages = [];


    /**
     * {@inheritdoc}
     */
    public function send($level, $message)
    {
        $this->messages[] = ['level' => $level, 'message' => $message];
    }


    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        parent::setContainer($container);

        if ($container->has('response')) {
            $response = $container->get('response');
            $response->addOptimizer('console.browser', $this);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function prepare(Response $response)
    {
        $body = $response->getBody();

        // is text/html ?
        $type = $body->getHeader('content-type');
        if ($type && preg_match('/^text\/html/', $type)) {
            $contents = [$body->getContent()];

            foreach ($this->messages as $m) {
                $contents[] = $this->logConsole($m['level'], $m['message']);
            }

            $body->setContent(join(PHP_EOL, $contents));
        }
    }


    /**
     * log to console
     *
     * @param   mixed   $var
     * @return  string
     */
    public function logConsole($level, $var)
    {
        return sprintf('<script type="text/javascript">console.%s(%s)</script>', $this->levelToJSLogger($level), json_encode($this->wrapping($var)));
    }


    /**
     * log level convert to javascript logger level
     *
     * @param   int     $level
     * @return  string
     */
    public function levelToJSLogger($level)
    {
        switch ($level) {
            case Client::LOG_LEVEL_WARN:
                return 'warn'; break;
            case Client::LOG_LEVEL_ERROR:
            case Client::LOG_LEVEL_FATAL:
                return 'error'; break;
            case Client::LOG_LEVEL_INFO:
                return 'info'; break;
            case Client::LOG_LEVEL_DEBUG:
            default:
                return 'log'; break;
        }
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function wrapping($var, array $references = [])
    {
        switch (true) {
            case is_object($var):

                $value = [];

                // is recruision ?
                if (in_array($var, $references, true)) return '** recursion **';
                $references[] = $var;

                $ref = new ReflectionObject($var);
                $value['__class_name'] = $ref->getName();

                foreach ($ref->getProperties() as $property) {

                    // simplize
                    $property->setAccessible(true);
                    $v = $property->getValue($var);
                    if (array_key_exists('Samurai\Raikiri\DependencyInjectable', $this->class_uses_deep($ref->getName()))
                        && $property->getName() === 'container') $v = '** raikiri **';
                    if ($var instanceof EntityTable && $property->getName() === 'onikiri') $v = '** onikiri **';

                    $value[$property->getName()] = $this->wrapping($v, $references);
                }
                break;
            case is_array($var):
            default:
                $value = $var;
                break;
        }
        return $value;
    }
}

