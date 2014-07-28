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

namespace Samurai\Samurai\Filter;

use App\Application;
use Samurai\Samurai\Component\Core\Loader;
use Samurai\Samurai\Controller\SamuraiController;
use Samurai\Samurai\Exception\Exception;

/**
 * View filter.
 *
 * render template, forward action, location, etc...
 *
 * @package     Samurai
 * @subpackage  Filter
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ViewFilter extends Filter
{
    /**
     * View template.
     *
     * @const   string
     */
    const VIEW_TEMPLATE = SamuraiController::VIEW_TEMPLATE;

    /**
     * Forward action.
     *
     * @const   string
     */
    const FORWARD_ACTION = SamuraiController::FORWARD_ACTION;


    /**
     * @dependencies
     */
    public $ActionChain;
    public $Renderer;
    public $Response;
    public $application;


    /**
     * @override
     */
    public function postfilter()
    {
        parent::postfilter();

        $result = $this->_getResult();
        $data = $this->_getResultData();
        if ( ! $result ) return;

        // what do ?
        switch ( $result ) {
            case self::VIEW_TEMPLATE:
                $this->_renderTemplate($data);
                break;
            case self::FORWARD_ACTION:
                $this->_forwardAction($data);
                break;
        }
    }


    /**
     * rendering template.
     *
     * @access  private
     * @param   string  $template
     */
    private function _renderTemplate($template = null)
    {
        // when no template, auto generate template path.
        // View/Content/[controller]/[action].html.twig
        if ( ! $template ) {
            $def = $this->ActionChain->getCurrentAction();
            $controller = join(DS, array_map('ucfirst', explode('_', $def['controller_name'])));
            $action = $def['action'];
            $template = sprintf('%s/%s.%s', $controller, $action, $this->Renderer->getSuffix());
        }

        // rendering by renderer.
        $result = $this->Renderer->render($template);
        $this->Response->setBody($result);
        if ( $this->Response->isHttp() ) {
            $this->Response->setHeader('content-type', sprintf('text/html; charset=%s', $this->application->config('encoding.output')));
        }
    }



    /**
     * forward action.
     *
     * @access  private
     * @param   string  $action
     */
    private function _forwardAction($action)
    {
        $this->ActionChain->addAction($action);
    }




    /**
     * Get action result.
     *
     * @access  private
     * @return  string
     * @throw   Samurai\Samurai\Exception\Exception
     */
    private function _getResult()
    {
        $def = $this->ActionChain->getCurrentAction();
        $result = $def['result'];
        if ( ! $result ) {
            return null;
        } elseif ( is_string($result) ) {
            return $result;
        } elseif ( is_array($result) ) {
            return array_shift($result);
        } else {
            throw new Exception('invalid action result.');
        }
    }

    /**
     * Get action result data.
     *
     * @access  private
     * @return  mixed
     */
    private function _getResultData()
    {
        $def = $this->ActionChain->getCurrentAction();
        $result = $def['result'];
        if ( is_string($result) ) {
            return null;
        } elseif ( is_array($result) ) {
            return array_pop($result);
        } else {
            return null;
        }
    }
}

