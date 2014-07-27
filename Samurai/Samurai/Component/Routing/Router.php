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

namespace Samurai\Samurai\Component\Routing;

use Samurai\Raikiri\DependencyInjectable;
use Samurai\Samurai\Component\Core\YAML;

/**
 * Routing class.
 *
 * URL dispatch to action.
 *
 * @package     Samurai
 * @subpackage  Routing
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Router
{
    /**
     * root routing.
     *
     * @access  protected
     * @var     Rule\RootRule
     */
    protected $_root;

    /**
     * default routing.
     *
     * @access  protected
     * @var     Rule\DefaultRule
     */
    protected $_default;

    /**
     * routes
     *
     * @access  protected
     * @var     array
     */
    protected $_rules = array();

    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * constructor.
     *
     * @access  public
     */
    public function __construct()
    {
        $this->_default = new Rule\DefaultRule();
    }



    /**
     * import routing config.
     *
     * @access  public
     * @param   string  $file
     */
    public function import($file)
    {
        $rules = YAML::load($file);

        foreach ($rules as $rule) {
            list($key, $value) = each($rule);
            switch ($key) {
                case 'root':
                    $this->setRoot($value);
                    break;
                case 'match':
                    $this->addMatchRule($value);
                    break;
            }
        }
    }


    /**
     * set root routing
     *
     * @access  public
     * @param   string
     */
    public function setRoot($value)
    {
        $rule = new Rule\RootRule($value);
        $this->_root = $rule;
    }


    /**
     * Add mathing rule.
     *
     * @access  public
     * @param   array   $value
     */
    public function addMatchRule(array $value)
    {
        $rule = new Rule\MatchRule($value);
        $this->_rules[] = $rule;
    }





    /**
     * routing.
     *
     * @access  public
     * @return  Rule\Rule
     */
    public function routing()
    {
        // has dispatch.
        if ($action = $this->getDispatchAction()) {
            return new Rule\MatchRule(array('action' => $action));
        }

        // root rule.
        $path = $this->Request->getPath();
        if ($this->_root && $this->_root->match($path)) {
            return $this->_root;
        }

        // default rule.
        if ($this->_default && $this->_default->match($path)) {
            return $this->_default;
        }
    }


    /**
     * Get dispatched action
     *
     * enable target action name by request key.
     * ex. <input type="submit" name="dispatch-controller-action" value="submit" />
     *
     * @access  public
     * @return  string
     */
    public function getDispatchAction()
    {
        $params = $this->Request->getAll();
        foreach (array_keys($params) as $key) {
            if (preg_match('/^dispatch-(.+)/', $key, $matches)) {
                $action = str_replace('-', '.', $matches[1]);
                return $action;
            }
        }
    }
}

