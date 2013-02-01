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
 * Json表示を担当するFilter
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_ViewJson extends Samurai_Filter
{
    /**
     * Requestコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Request;

    /**
     * Responseコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Response;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;


    /**
     * @override
     */
    protected function _postfilter()
    {
        parent::_postfilter();
        //Action結果の取得
        $result = $this->ActionChain->getCurrentActionResult();
        if(Samurai_Config::get('encoding.internal') != 'UTF-8'){
            mb_convert_variables('UTF-8', Samurai_Config::get('encoding.internal'), $result);
        }
        $result = json_encode($result);
        //JSONP(JSON with Padding)コールバックの指定がある場合
        if($this->getAttribute('jsonp') && $this->Request->getParameter($this->getAttribute('jsonp'))){
            $result = $this->Request->getParameter($this->getAttribute('jsonp')).'('.$result.');';
        //JSONF(JSON with Frash)
        } elseif($this->getAttribute('jsonf') && $this->Request->getParameter($this->getAttribute('jsonf'))){
            $result = $this->Request->getParameter($this->getAttribute('jsonf')).'='.$result;
        //JSONI(JSON with Iframe)コールバックの指定がある場合
        } elseif($this->getAttribute('jsoni') && $this->Request->getParameter($this->getAttribute('jsoni'))){
            $function = $this->Request->getParameter($this->getAttribute('jsoni'));
            //$view = "parent.".$Request->getParameter($this->getAttribute('jsoni'))."(".$view.");";
            $result = join("\r\n", array(
                "<SCRIPT type=\"text/javascript\">",
                "    var result = {$view};",
                "    alert(typeof(parent.document));",
                //"    parent.{$function}(result);",
                "</SCRIPT>",
            ));
        } else {
            $this->Response->setHeader('x-json', $result);
        }
        $Body = $this->Response->setBody($result);
        if(is_object($Body) && !$this->Response->hasHeader('content-type')){
            //$Body->setHeader('content-type', sprintf('application/json; charset=%s', Samurai_Config::get('encoding.output')));
            //$Body->setHeader('content-type', sprintf('text/json; charset=%s', Samurai_Config::get('encoding.output')));
        }
        $this->Response->execute();
    }
}
