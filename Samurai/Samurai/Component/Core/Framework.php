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
use Samurai\Samurai\Samurai;
use Samurai\Samurai\Config;

/**
 * Framework executer.
 *
 * @package     Samurai
 * @subpackage  Component.Core
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Framework extends Raikiri\Object
{
    /**
     * @override
     */
    public function defineDeps()
    {
        $this->addDep('Config');
        $this->addDep('Router');
        $this->addDep('ActionChain');
        $this->addDep('FilterChain');
        $this->addDep('Response');
    }


    /**
     * initialize container.
     *
     * @access  public
     * @param   string  $dicon
     */
    public function initContainer($dicon)
    {
        $container = Raikiri\ContainerFactory::create('samurai');
        $container->import($dicon);
    }



    /**
     * execute.
     *
     * 1. init container.
     * 2. load config
     * 3. routing
     * 4. action chain
     * 5. filter chain
     */
    public function execute()
    {
        // load settings
        $this->_loadConfig();

        // routing
        $this->_routing();

        // action chain.
        while ( $action = $this->ActionChain->getCurrentAction() ) {

            // clear.
            $this->FilterChain->clear();

            $this->FilterChain->setAction($action['controller'], $action['action']);
            $this->FilterChain->build();
            $this->FilterChain->execute();

            $this->ActionChain->next();
        }

        // response.
        $this->Response->execute();
    }


    /**
     * load configuration.
     *
     * @access  private
     */
    private function _loadConfig()
    {
        // base configurations.
        $this->Config->import(Config\APP_DIR . '/Config/Samurai/config.yml');
    }



    /**
     * routing.
     *
     * @access  private
     */
    private function _routing()
    {
        // import.
        $this->Router->import(Config\APP_DIR . DS . $this->Config->get('directory.config.routing') . '/routes.yml');

        // routing.
        $rule = $this->Router->routing();

        // add action chain.
        $this->ActionChain->addAction($rule->getController(), $rule->getAction());
    }
}

