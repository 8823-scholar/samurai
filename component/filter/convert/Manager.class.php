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

Samurai_Loader::loadByClass('Filter_Convert_Converter');

/**
 * コンバート処理を統括するクラス
 *
 * <code>
 *     $foo = $ConverterManager->convert('trim', $Request->get('foo'));
 *     $items = $ConverterManager->convert('toArray', $Request->get('items'));
 *     $dto   = $ConverterManager->convert('toObject', array('foo' => $foo, 'items' => $items), true);
 * </code>
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  The BSD License
 */
class Filter_Convert_Manager
{
    /**
     * コンバータキャッシュ
     *
     * @access   private
     * @var      array
     */
    private $_converters = array();


    /**
     * コンストラクタ
     *
     * @access    public
     */
    public function __construct()
    {
        
    }





    /**
     * コンバート実行
     *
     * @access     public
     * @param      string  $converter   コンバーター名
     * @param      mixed   $value       対象値
     * @return     mixed   コンバートされた値
     */
    public function convert($converter, $value)
    {
        $Converter = $this->getConverter($converter);
        $value = $Converter->convert($value);
        return $value;
    }


    /**
     * コンバーター取得
     *
     * @access     public
     * @param      string  $name   コンバーター名
     * @return     object
     */
    public function getConverter($name)
    {
        if(!$name) $name = 'through';
        //見つからない場合は作成
        if(!isset($this->_converters[$name])){
            $class = 'Filter_Convert_Converter_' . ucfirst($name);
            if(Samurai_Loader::loadByClass($class)){
                $this->_converters[$name] = new $class();
            } else {
                $class = 'Filter_Convert_Converter_Native';
                Samurai_Loader::loadByClass($class);
                $this->_converters[$name] = new $class($name);
            }
        }
        return $this->_converters[$name];
    }
}

