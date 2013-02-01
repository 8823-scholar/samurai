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

Samurai_Loader::loadByClass('Filter_Validate_Validator');

/**
 * Validate処理を管理するクラス
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Validate_Manager
{
    /**
     * バリデーターキャッシュ
     *
     * @access   private
     * @var      array
     */
    private $_validators = array();


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function Filter_Validate_Manager()
    {
        
    }





    /**
     * validateトリガ
     *
     * @access     public
     * @param      string  $validator   バリデータ名
     * @param      mixed   $value       値
     * @param      array   $params      付加値
     * @return     boolean 検証に成功したかどうか
     */
    public function validate($validator, $value, $params)
    {
        $Validator = $this->getValidator($validator);
        return $Validator->validate($value, $params);
    }


    /**
     * Validatorの取得
     *
     * @access     public
     * @param      string  $name   バリデータ名
     * @return     object
     */
    public function getValidator($name)
    {
        if(!isset($this->_validators[$name])){
            $class = 'Filter_Validate_Validator_' . ucfirst($name);
            if(Samurai_Loader::loadByClass($class)){
                $Validator = new $class();
            } else {
                $class = 'Filter_Validate_Validator_Native';
                Samurai_Loader::loadByClass($class);
                $Validator = new $class($name);
            }
            Samurai::getContainer()->injectDependency($Validator);
            $this->_validators[$name] = $Validator;
        }
        return $this->_validators[$name];
    }
}

