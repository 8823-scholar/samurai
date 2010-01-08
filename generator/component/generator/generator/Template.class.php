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

/**
 * TemplateGenerator
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Generator_Generator_Template extends Generator
{
    /**
     * web用スケルトン名(smarty)
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WEB_SMARTY = 'template/web.smarty.skeleton.php';

    /**
     * web用スケルトン名(phptal)
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WEB_PHPTAL = 'template/web.phptal.skeleton.php';

    /**
     * web用スケルトン名(simple)
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_WEB_SIMPLE = 'template/web.simple.skeleton.php';

    /**
     * cli用スケルトン名(smarty)
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CLI_SMARTY = 'template/cli.smarty.skeleton.php';

    /**
     * cli用スケルトン名(simple)
     *
     * @access   public
     * @var      string
     */
    public $SKELETON_CLI_SIMPLE = 'template/cli.simple.skeleton.php';

    /**
     * ブラウザweb
     *
     * @access   public
     * @var      int
     */
    public $BROWSER_WEB = 1;

    /**
     * ブラウザcli
     *
     * @access   public
     * @var      int
     */
    public $BROWSER_CLI = 2;


    /**
     * @implements
     */
    public function generate($template_name, $skeleton, $params = array())
    {
        //ローカライズ
        $template_dir = sprintf('%s/%s', Samurai_Config::get('generator.directory.samurai'), Samurai_Config::get('generator.directory.template'));
        $template_file = sprintf('%s/%s', $template_dir, $template_name);
        //ジェネレイト
        $params['template_names'] = preg_split('/[_\/]/', preg_replace('/\.tpl$/', '', $template_name));
        $params['output_code'] = Samurai_Config::get('generator.encoding.output');
        if(file_exists($template_dir) && is_dir($template_dir)){
            $result = $this->_generate($template_file, $skeleton, $params);
        } else {
            $result = $this->RESULT_IGNORE;
        }
        return array($result, $template_file);
    }


    /**
     * スケルトンの取得
     *
     * @access     public
     * @param      string  $filename   スケルトン名
     * @return     string
     */
    public function getSkeleton($filename = NULL)
    {
        if(!$filename){
            $filename = 'SKELETON_WEB_' . strtoupper(Samurai_Config::get('generator.renderer.name'));
            $filename = $this->{$filename};
        }
        return parent::getSkeleton($filename);
    }
}

