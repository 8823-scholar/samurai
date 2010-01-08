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
 * セッション使用準備をおこなうFilter
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Session extends Samurai_Filter
{
    /**
     * Sessionコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Session;

    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        //Sessionコンポーネント補完
        if(!$this->Session){
            $Container = Samurai::getContainer();
            $Def = $Container->getContainerDef();
            $Def->class = 'Samurai_Request_Session';
            $Container->registerComponent('Session', $Def);
            $this->Session = $Container->getComponent('Session');
        }
        //情報の設定
        foreach($this->getAttributes() as $_key => $_val){
            switch($_key){
                case 'type':
                    $this->Session->setType((string)$_val);
                    break;
                case 'dsn':
                    $this->Session->setDsn((string)$_val);
                    break;
                case 'name':
                    $this->Session->name((string)$_val);
                    break;
                case 'id':
                    $this->Session->id((string)$_val);
                    break;
                case 'save_path':
                    $this->Session->savePath((string)$_val);
                    break;
                case 'cache_limiter':
                    $this->Session->cacheLimiter((string)$_val);
                    break;
                case 'cache_expire':
                    $this->Session->cacheExpire((int)$_val);
                    break;
                case 'use_cookies':
                    $this->Session->setUseCookies((int)$_val);
                    break;
                case 'cookie_lifetime':
                    $this->Session->setCookieLifetime((int)$_val);
                    break;
                case 'cookie_path':
                    $this->Session->setCookiePath((string)$_val);
                    break;
                case 'cookie_domain':
                    $this->Session->setCookieDomain((string)$_val);
                    break;
                case 'cookie_sequre':
                    $this->Session->setCookieSequre((bool)$_val);
                    break;
                case 'gc_divisor':
                case 'gc_probability':
                case 'gc_maxlifetime':
                    $this->Session->setGCConfig($_key, (int)$_val);
                    break;
            }
        }
        //携帯ではUIDを取得できる限り、UIDをSESSIONIDとする
        if($this->Device->isMobile() && $uid = $this->Device->getUid()){
            $this->Session->id($uid);
        }
        //Session開始
        $this->Session->start();
    }


    /**
     * @override
     */
    protected function _postfilter(){
        parent::_postfilter();
        
        $attributes = $this->getAttributes();
        if(isset($attributes['close'])){
            $this->Session->close($attributes['close']);
        }
    }
}

