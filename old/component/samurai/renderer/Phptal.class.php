<?php
/**
 * PHP version 5.
 *
 * Copyright (c) 2007-2010, Samurai Framework Project, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     * Neither the name of the Samurai Framework Project nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    SVN: $Id$
 */

Samurai_Loader::loadByClass('Samurai_Renderer');
Samurai_Loader::loadByClass('Samurai_Renderer_Engine_Phptal');

/**
 * SamuraiFWとPhptalとのブリッジクラス
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Renderer_Phptal extends Samurai_Renderer
{
    /**
     * PHPTalインスタンス
     *
     * @access   protected
     * @var      object
     */
    protected $Engine;





    /**
     * @implements
     */
    protected function _setEngine()
    {
        $this->Engine = new Samurai_Renderer_Engine_Phptal();
        Samurai::getContainer()->injectDependency($this->Engine);
    }


    /**
     * @implements
     */
    public function render($template)
    {
        $this->Engine->setTemplate($template);
        $result = $this->Engine->execute($template);
        return $result;
    }


    /**
     * @implements
     */
    public function assign($key, $value)
    {
        $this->Engine->set($key, $value);
    }


    /**
     * @implements
     */
    public function getAssignedVars()
    {
        return $this->Engine->get_template_vars();
    }


    /**
     * @implements
     */
    public function addHelper($alias, $define)
    {
        /*
        $Container = Samurai::getContainer();
        $Container->registerComponent("Renderer_Smarty_{$alias}", new Samurai_Container_Def($define));
        $this->Engine->register_object($alias, $Container->getComponent("Renderer_Smarty_{$alias}"));
        */
    }
}

