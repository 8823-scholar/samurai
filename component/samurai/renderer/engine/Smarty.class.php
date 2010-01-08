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

require_once 'Smarty/Smarty.class.php';

/**
 * Samurai用にSmartyをラップ
 * 
 * @package    Samurai
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @see        Smarty
 */
class Samurai_Renderer_Engine_Smarty extends Smarty
{
    /**
     * Utilityコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Utility;


    /**
     * コンストラクタ
     *
     * @access     public
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @override
     */
    public function fetch($template, $cache_id = null, $compile_id = null, $display = false)
    {
        $this->Utility->fillupDirectory($this->compile_dir);
        $this->Utility->fillupDirectory($this->cache_dir);
        return parent::fetch($template, $cache_id, $compile_id, $display);
    }


    /**
     * @override
     */
    public function _fetch_resource_info(&$params)
    {
        //template_dirの補完
        if(!Samurai_Loader::isAbsolutePath($params['resource_name'])
            && !Samurai_Loader::isAbsolutePath($this->template_dir)){
            $params['resource_base_path'] = array();
            foreach(Samurai::getSamuraiDirs() as $samurai_dir){
                $params['resource_base_path'][] = $samurai_dir . DS . $this->template_dir;
            }
        }
        return parent::_fetch_resource_info($params);
    }


    /**
     * @override
     */
    public function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null)
    {
        if($auto_id === NULL) $auto_id = Samurai_Config::get('directory.template');
        return parent::_get_auto_filename($auto_base, $auto_source, $auto_id);
    }
}

