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
 * 実行時間を計測するためのフィルター
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_ExecutionTime extends Samurai_Filter
{
    /**
     * 開始時間
     *
     * @access   public
     * @var      int
     */
    public $start = 0;

    /**
     * 終了時間
     *
     * @access   public
     * @var      int
     */
    public $end = 0;

    /**
     * 処理時間
     *
     * @access   public
     * @var      int
     */
    public $time = 0;

    /**
     * 処理時間(php only)
     *
     * @access   public
     * @var      int
     */
    public $time_php = 0;

    /**
     * 処理時間(query only)
     *
     * @access   public
     * @var      int
     */
    public $time_sql = 0;

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
    protected function _prefilter()
    {
        parent::_prefilter();
        $this->start = microtime(true);
    }


    /**
     * @override
     */
    protected function _postfilter()
    {
        parent::_postfilter();
        
        //総合実行時間
        $end  = microtime(true);
        $time = $end - $this->start;
        $name = $this->getAttribute('name', $this->ActionChain->getCurrentActionName());
        Samurai_Logger::info('[%s] %0.1f ms', array($name, $time * 1000));
        
        //SQL実行時間
        $query_time = 0;
        if(class_exists('ActiveGatewayManager', false)){
            foreach(ActiveGatewayManager::getPoolQuery() as $query_info){
                $query_time += $query_info['time'];
            }
        }
        
        //計算
        $this->end = $end;
        $this->time = $time;
        $this->time_php = round($time - $query_time, 4);
        $this->time_sql = round($query_time, 4);
    }
}

