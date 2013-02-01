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
 * ファイルのアップロードに関するバリデーションを、透過的に行うためのフィルター
 *
 * アップロードされたファイルのコピーなどは各アクションに委ね、サポートしない
 * 
 * @package    Samurai
 * @subpackage Filter
 * @copyright  2007-2010 Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Filter_Upload extends Samurai_Filter
{
    /**
     * Uploadコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Upload;

    /**
     * Utilityコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $Utility;

    /**
     * ActionChainコンポーネント
     *
     * @access   public
     * @var      object
     */
    public $ActionChain;

    /**
     * エラーメッセージ
     *
     * @access   private
     * @var      array
     */
    private $_messages = array(
        'ini_size'   => 'ファイルサイズが大きすぎます',
        'form_size'  => 'ファイルサイズが大きすぎます',
        'max_size'   => 'ファイルサイズが大きすぎます',
        'partial'    => 'ファイルはアップロードされませんでした',
        'required'   => 'ファイルを指定してください',
        'extension'  => 'ファイルが許可されていません',
        'mime'       => 'ファイルが許可されていません',
        'no_tmp_dir' => '一時保存ディレクトリが存在しません',
        'cant_write' => 'ファイルの一保存の書込に失敗しました'
    );


    /**
     * @override
     */
    protected function _prefilter()
    {
        parent::_prefilter();
        //アップロードインスタンスの補完
        if(!is_object($this->Upload)){
            Samurai_Loader::loadByClass('Samurai_Request_Upload');
            $this->Upload = new Samurai_Request_Upload();
            Samurai::getContainer()->registerComponent('Upload', $this->Upload);
        }
        //各種処理
        foreach($this->getAttributes() as $name => $attributes){
            $File = $this->Upload->getFile($name);
            $attributes = $this->_margeAttributes($attributes);
            if($File){
                //拡張子書き換え
                $this->_rewriteExtension($File, $attributes['rewrite']);
                //バリデート
                $this->_validate4Array($name, $File, $attributes);
            }
        }
    }


    /**
     * 拡張子の書き換え
     *
     * @access     private
     * @param      object  $File      Samurai_Request_Upload_File
     * @param      array   $rewrite   書き換えルール
     */
    private function _rewriteExtension($File, $rewrite=array())
    {
        if($File->isArray()){
            foreach($File->getFiles() as $File){
                $this->_rewriteExtension($File, $rewrite);
            }
        } else {
            foreach($rewrite as $from => $to){
                $froms = explode(',', $from);
                if(in_array($File->extension, $froms)){
                    $File->extension = $to;
                    $File->name = $File->filename . '.' . $File->extension;
                }
            }
        }
    }


    /**
     * 配列用のバリデート
     *
     * @access     private
     * @param      string  $name         名前
     * @param      object  $File         Samurai_Request_Upload_File
     * @param      array   $attributes   設定
     */
    private function _validate4Array($name, $File, $attributes=array())
    {
        if($File->isArray()){
            foreach($File->getFiles() as $_key => $File){
                $this->_validate4Array("{$name}.{$_key}", $File, $attributes);
            }
        } else {
            $this->_validate($name, $File, $attributes);
        }
    }


    /**
     * バリデート
     *
     * @access     private
     * @param      string  $name         名前
     * @param      object  $File         Samurai_Request_Upload_File
     * @param      array   $attributes   設定
     */
    private function _validate($name, $File, $attributes=array())
    {
        //エラーメッセージ
        $messages = $attributes['messages'];
        $error_message = NULL;
        if($File->error != UPLOAD_ERR_OK){
            switch($File->error){
                //php.iniでのサイズ指定オーバー
                case UPLOAD_ERR_INI_SIZE:
                    $error_message = $messages['ini_size'];
                    break;
                //HTMLフォームで指定されたMAX_FILE_SIZEのオーバー
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = $messages['form_size'];
                    break;
                //一部しかアップロードされなかった場合
                case UPLOAD_ERR_PARTIAL:
                    $error_message = $messages['partial'];
                    break;
                //ファイルがアップロードされなかった場合
                case UPLOAD_ERR_NO_FILE:
                    if(isset($attributes['required']) && $attributes['required']){
                        $error_message = $messages['required'];
                    }
                    break;
                //一時保存ディレクトリが存在しない場合
                case UPLOAD_ERR_NO_TMP_DIR:
                    Samurai_Logger::fatal($messages['no_tmp_dir']);
                    break;
                //書込に失敗した場合
                case UPLOAD_ERR_CANT_WRITE:
                    Samurai_Logger::fatal($messages['cant_write']);
                    break;
            }
        }
        //samurai.yamlでの設定に違反するエラー
        elseif($File->isUploaded()){
            //サイズオーバー
            if($attributes['max_size'] < $File->size){
                $error_message = $messages['max_size'];
            }
            //拡張子限定
            elseif(!$this->_checkExtension($File->extension, $attributes['extension'])){
                $error_message = $messages['extension'];
            }
            //mime限定
            elseif(!$this->_checkMime($File->path, $attributes['mime'])){
                $error_message = $messages['mime'];
            }
        }
        //エラーの格納
        if($error_message !== NULL){
            $ErrorList = $this->ActionChain->getCurrentErrorList();
            $ErrorList->setType(Samurai_Config::get('error.upload'));
            $ErrorList->add($name, $error_message);
        }
    }





    /**
     * 許可された拡張子かどうかチェック
     *
     * @access     private
     * @param      string  $extension   拡張子
     * @param      mixed   $accepts     許可拡張子一覧
     * @return     boolean
     */
    private function _checkExtension($extension, $accepts)
    {
        //指定がない場合
        if($accepts == '') return true;
        //チェック
        $accepts = preg_split('/(\s*,\s*)/', trim($accepts));
        if(in_array(strtolower($extension), $accepts) || in_array(strtoupper($extension), $accepts)){
            Samurai_Logger::debug('This extension is accepted in "%s". -> %s', array(join(',', $accepts), $extension));
            return true;
        } else {
            Samurai_Logger::debug('This extension is denied in "%s". -> %s', array(join(',', $accepts), $extension));
            return false;
        }
    }


    /**
     * mimeまで踏み込んだチェック
     *
     * @access     private
     * @param      string  $path      ファイルパス
     * @param      string  $accepts   ファイルの拡張子
     * @return     boolean
     */
    private function _checkMime($path, $accepts)
    {
        //指定がない場合
        if($accepts == '') return true;
        if(substr(PHP_OS, 0, 3) == 'WIN') return true;
        //チェック
        $accepts = preg_split('/(\s*,\s*)/', trim($accepts));
        $mime_type = preg_replace('/;.*$/', '', trim(`file -bi $path`));
        foreach($accepts as $accept){
            $accept = preg_quote($accept, '|');
            $accept = str_replace('*', '.*', $accept);
            if(preg_match("|{$accept}|i", $mime_type)){
                Samurai_Logger::debug('This mime is accepted in "%s". -> %s', array(join(',', $accepts), $mime_type));
                return true;
            }
        }
        Samurai_Logger::debug('This mime is denied in "%s". -> %s', array(join(',', $accepts), $mime_type));
        return false;
    }





    /**
     * 設定配列をデフォルトとマージ
     *
     * @access     private
     * @param      array   $attributes   設定情報
     * @return     array   マージされた設定
     */
    private function _margeAttributes($attributes)
    {
        $default = array(
            'required'  => false,
            'max_size'  => 1024000,
            'extension' => '',
            'mime'      => '',
            'messages'  => $this->_messages,
            'rewrite'   => array(),
        );
        return $this->Utility->array_merge($default, (array)$attributes);
    }
}

