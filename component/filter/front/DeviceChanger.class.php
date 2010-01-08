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
 * アクセス端末の切り替えをサポートするフロントフィルター
 *
 * 端末情報は自前で揃える必要があるが、端末の切り替えを手軽におこなえるようにする
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Front_DeviceChanger extends Samurai_Filter
{
    /**
     * Deviceコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Device;

    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * Cookieコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Cookie;


    /**
     * @override
     */
    public function _prefilter()
    {
        parent::_prefilter();
        if($this->_isEnable()){
            $device = $this->Request->get('samurai_device_changer');
            $c_device = $this->Cookie->get('SAMURAI.Filter.Front.DeviceChanger.device');
            if($device == 'reset'){
                $this->Cookie->del('SAMURAI.Filter.Front.DeviceChanger.device', '/');
            } elseif($device){
                $this->Cookie->set('SAMURAI.Filter.Front.DeviceChanger.device', $device, NULL, '/');
                $file = sprintf('%s/front/.device/%s', Samurai_Config::get('directory.filter'), $device);
                $this->_load($file);
            } elseif($c_device){
                $file = sprintf('%s/front/.device/%s', Samurai_Config::get('directory.filter'), $c_device);
                $this->_load($file);
            }
        }
    }


    /**
     * ファイル情報をロードする
     *
     * @access     private
     */
    private function _load($file)
    {
        try {
            $info = Samurai_Yaml::load(Samurai_Loader::getPath($file));
            //httpフィールドのセット
            if(isset($info['http'])){
                foreach((array)$info['http'] as $_key => $_val){
                    $this->Request->setHeader($_key, $_val);
                }
            }
            //UIDセクションのアレンジ(同じ端末でアクセスした場合、UIDが被るので)
            if(isset($info['uid_section']) && $this->Request->hasHeader($info['uid_section'])){
                $random = $this->Cookie->get('SAMURAI.Filter.Front.DeviceChanger.seed', uniqid());
                $uid_sec = $info['uid_section'];
                $this->Request->setHeader($uid_sec, $this->Request->getHeader($uid_sec) . $random);
                $this->Cookie->set('SAMURAI.Filter.Front.DeviceChanger.seed', $random, NULL, '/');
            }
            //headerにdebugの目印
            $this->Request->setHeader('x-debug', '1');
        } catch(Samurai_Exception $E){}
    }


    /**
     * 切り替え可能かどうかのチェック
     *
     * @access     private
     */
    private function _isEnable()
    {
        if($this->getAttribute('ip') && isset($_SERVER['REMOTE_ADDR'])){
            foreach((array)$this->getAttribute('ip') as $ip){
                $ip = str_replace('*', '.*?', $ip);
                if(preg_match('/'.$ip.'/', $_SERVER['REMOTE_ADDR'])){
                    return true;
                }
            }
        }
        return false;
    }
}

