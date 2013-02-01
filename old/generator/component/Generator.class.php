<?php
/**
 * PHP version 5.
 *
 * Copyright (c) Samurai Framework Project, All rights reserved.
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
 * @copyright  Samurai Framework Project
 * @link       http://samurai-fw.org/
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * 各Generatorの抽象クラス
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
abstract class Generator
{
    /**
     * Renderer4Generatorコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Renderer4Generator;

    /**
     * Utilityコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Utility;

    /**
     * 結果値(成功)
     *
     * @access   public
     * @var      int
     */
    public $RESULT_SUCCESS = 1;

    /**
     * 結果値(既にある)
     *
     * @access   public
     * @var      int
     */
    public $RESULT_ALREADY = 2;

    /**
     * 結果値(失敗)
     *
     * @access   public
     * @var      int
     */
    public $RESULT_FAILED = 3;

    /**
     * 結果値(無視された)
     *
     * @access   public
     * @var      int
     */
    public $RESULT_IGNORE = 4;


    /**
     * generateトリガーメソッド
     *
     * @access     public
     */
    abstract public function generate($filename, $skeleton, $params = array());


    /**
     * generateの実処理担当
     *
     * fetch -> saveまでをこなす。
     *
     * @access     protected
     * @param      string  $filename      出力ファイル名
     * @param      string  $skeleton      スケルトン名
     * @param      array   $params        Rendererに登録する変数
     * @param      boolean $is_resource   バイナリかどうか
     * @return     int     結果値
     */
    protected function _generate($filename, $skeleton, $params = array(), $is_resource = false)
    {
        //存在確認
        if(file_exists($filename)){
            return $this->RESULT_ALREADY;
        }
        //Renderer
        if(!$is_resource){
            $params = array_merge(Samurai_Config::getAll('generator.generator'), $params);
            foreach($params as $_key => $_val){
                $this->Renderer4Generator->assign($_key, $_val);
            }
            $source = $this->Renderer4Generator->render($skeleton);
        } else {
            $source = file_get_contents($skeleton);
        }
        //ディレクトリの補完
        $directory = dirname($filename);
        if(!file_exists($directory) || !is_dir($directory)){
            $this->Utility->fillupDirectory($directory, 0755);
        }
        //generate!!
        return file_put_contents($filename, $source) ? $this->RESULT_SUCCESS : $this->RESULT_FAILED ;
    }


    /**
     * get skeleton.
     *
     * @access     public
     * @param      string  $filename
     * @return     string
     */
    public function getSkeleton($filename)
    {
        $filename = sprintf('%s/%s', Samurai_Config::get('directory.skeleton'), $filename);

        // search local.
        $skeleton = sprintf('%s/%s', Samurai_Config::get('generator.directory.samurai'), $filename);

        // search global.
        if ( ! Samurai_Loader::isReadable($skeleton) ) {
            $skeleton = Samurai_Loader::getPath($filename);
        }
        return $skeleton;
    }
}

