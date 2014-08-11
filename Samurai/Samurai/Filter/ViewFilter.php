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
     * location.
     *
     * @const   string
     */
    const LOCATION_URL = SamuraiController::LOCATION_URL;

    /**
     * output json data.
     *
     * @const   string
     */
    const OUTPUT_JSON = SamuraiController::OUTPUT_JSON;


    /**
     * @override
     */
    public function postfilter()
    {
        parent::postfilter();

        $result = $this->_getResult();
        $data = $this->_getResultData();
        if (! $result) return;

        $this->_doByResult($result, $data);
    }


    private function _doByResult($result, $data)
    {
        switch ($result) {
            case self::VIEW_TEMPLATE:
                $this->_renderTemplate($data);
                break;
            case self::FORWARD_ACTION:
                $this->_forwardAction($data);
                break;
            case self::LOCATION_URL:
                $this->_locationURL($data);
                break;
            case self::OUTPUT_JSON:
                $this->_outputJson($data);
                break;
            default:
                if (is_string($data)) {
                    $datas = explode(':', $data);
                    $datas = array_map('trim', $datas);
                } else {
                    $datas = $data;
                }
                $this->_doByResult($datas[0], isset($datas[1]) ? $datas[1] : null);
                break;
        }
    }


    /**
     * rendering template.
     *
     * @param   string  $template
     */
    private function _renderTemplate($template = null)
    {
        // when no template, auto generate template path.
        // View/Content/[controller]/[action].html.twig
        $def = $this->actionChain->getCurrentAction();
        if (! $template) {
            $controller = join(DS, array_map('ucfirst', explode('_', $def['controller_name'])));
            $action = $def['action'];
            $template = sprintf('%s/%s.%s', $controller, $action, $this->renderer->getSuffix());
        }

        // rendering by renderer.
        $def['controller']->beforeRenderer();
        $result = $this->renderer->render($template);
        $this->response->setBody($result);
        if ($this->response->isHttp()) {
            $this->response->setHeader('content-type', sprintf('text/html; charset=%s', $this->application->config('encoding.output')));
        }
    }


    /**
     * forward action.
     *
     * @param   string  $action
     */
    private function _forwardAction($action)
    {
        $this->actionChain->addAction($action);
    }


    /**
     * location url.
     *
     * @param   string  $url
     */
    public function _locationURL($url)
    {
        $this->response->location($url);
    }


    /**
     * output json
     *
     * @param   mixed   $data
     */
    private function _outputJson($data)
    {
        $data = json_encode($data);
        $this->response->setBody($data);
        $this->response->setHeader('content-type', sprintf('application/json; charset=%s', $this->application->config('encoding.output')));
    }




    /**
     * Get action result.
     *
     * @return  string
     * @throw   Samurai\Samurai\Exception\Exception
     */
    private function _getResult()
    {
        $def = $this->actionChain->getCurrentAction();
        $result = $def['result'];
        if (! $result) {
            return null;
        } elseif (is_string($result)) {
            return $result;
        } elseif (is_array($result)) {
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
        $def = $this->actionChain->getCurrentAction();
        $result = $def['result'];
        if (is_string($result)) {
            return $this->getAttribute($result);
        } elseif (is_array($result)) {
            return array_pop($result);
        } else {
            return null;
        }
    }
}

