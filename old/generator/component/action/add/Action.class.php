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
 * add action command.
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Add_Action extends Generator_Action
{
    /**
     * テンプレート指定
     *
     * @access   public
     * @var      string
     */
    public $template = '';


    /**
     * execute.
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        if ( $this->_isUsage() || ! $this->args ) return 'usage';
        if ( ! $this->_validate() ) return 'usage';

        //テンプレートオプションの調節
        $this->_adjustTemplateOption();

        $params = array();
        $params['description'] = $this->Request->get('description');

        // enable multipule.
        $templates = explode(',', $this->template);
        while ( $name = array_shift($this->args) ) {
            // add action.
            $action_name = strtolower($name);
            $action_file = $this->_addAction($action_name, $params);

            // add yaml.
            if ( $action_file ) {
                $template = array_shift($templates);
                $this->_addYaml($action_name, $action_file, $this->Generator->YAML_GLOBAL);
                $this->_addYaml($action_name, $action_file, $this->Generator->YAML_ACTION, array('template' => $template));
                if($this->Request->get('options.d') || $this->Request->get('dicon')){
                    $this->_addDicon($action_name, $action_file, $this->Generator->DICON_GLOBAL);
                    $this->_addDicon($action_name, $action_file, $this->Generator->DICON_ACTION);
                }
            }
        }
        if($this->template){
            $this->Request->set('args', explode(',', $this->template));
            return "template";
        }
    }


    /**
     * validate.
     *
     * @access     private
     * @return     boolean
     */
    private function _validate()
    {
        // check name.
        foreach ( $this->args as $name ) {
            if ( ! preg_match('/^[a-z][a-z0-9_]*?$/', $name) ) {
                $this->ErrorList->add('action_name', "{$name} -> Action's name is Invalid. ([a-z0-9_])");
            } elseif ( preg_match('/[_]{2,}/', $name) ) {
                $this->ErrorList->add("action_name", "{$name} -> Action's name is Invalid. (Don't use \"_(under bar)\" continously)");
            }
        }
        return !$this->ErrorList->isExists();
    }


    /**
     * テンプレートオプションの調節
     *
     * @access     private
     */
    private function _adjustTemplateOption()
    {
        if(!$this->Request->get('no-template') && !$this->Request->get('template')){
            foreach($this->args as $action){
                $template = sprintf('%s.%s', strtolower(join('/', explode('_', $action))),
                                        Samurai_Config::get('generator.renderer.suffix', 'tpl'));
                $this->template .= $this->template ? ",{$template}" : $template ;
            }
        }
    }


    /**
     * アクションを追加する
     *
     * @access     private
     * @param      string  $action_name   Action名
     * @param      array   $params        Rendererに渡される値
     */
    private function _addAction($action_name, $params = array())
    {
        //Skeletonの決定
        $skeleton = $this->Generator->getSkeleton();
        //Generate
        list($result, $action_file) = $this->Generator->generate($action_name, $skeleton, $params);
        if($result == $this->Generator->RESULT_SUCCESS){
            $this->_sendMessage("{$action_name} -> Successfully generated. [{$action_file}]");
        } elseif($result == $this->Generator->RESULT_ALREADY){
            $this->_sendMessage("{$action_name} -> Already exists. [{$action_file}] -> skip");
        } else {
            $this->_sendMessage("{$action_name} -> Failed.");
        }
        return $action_file;
    }


    /**
     * YAMLを追加する
     *
     * @access     private
     * @param      string  $action_name   Action名
     * @param      string  $action_file   Actionパス
     * @param      int     $scope         YAMLの空間値
     * @param      array   $params        Rendererに渡される値
     */
    private function _addYaml($action_name, $action_file, $scope, $params = array())
    {
        //Skeletonの決定
        $skeleton = $this->Generator->getSkeleton($this->Generator->SKELETON_YAML);
        //Generate
        $params['action_names'] = explode('_', $action_name);
        if($scope == $this->Generator->YAML_GLOBAL){
            $params['action'] = false;
            $params['global'] = true;
            array_pop($params['action_names']);
            array_push($params['action_names'], '*');
        } elseif($scope == $this->Generator->YAML_ACTION){
            $params['action'] = true;
            $params['global'] = false;
        }
        list($result, $yaml_file) = $this->Generator->generate4Yaml($action_file, $skeleton, $params, $scope);
        $yaml_base = basename($yaml_file);
        if($result == $this->Generator->RESULT_SUCCESS){
            $this->_sendMessage("{$yaml_base} -> Successfully generated. [{$yaml_file}]");
        } elseif($result == $this->Generator->RESULT_ALREADY){
            $this->_sendMessage("{$yaml_base} -> Already exists. [{$yaml_file}] -> skip");
        } else {
            $this->_sendMessage("{$yaml_base} -> Failed.");
        }
    }


    /**
     * diconを追加する
     *
     * @access     private
     * @param      string  $action_name   Action名
     * @param      string  $action_file   Actionパス
     * @param      int     $scope         DICONの空間値
     * @param      array   $params        Rendererに渡される値
     */
    private function _addDicon($action_name, $action_file, $scope, $params = array())
    {
        //Skeletonの決定
        $skeleton = $this->Generator->getSkeleton($this->Generator->SKELETON_DICON);
        //Generate
        $params['action_names'] = explode('_', $action_name);
        if($scope == $this->Generator->DICON_GLOBAL){
            array_pop($params['action_names']);
            array_push($params['action_names'], '*');
        }
        list($result, $dicon_file) = $this->Generator->generate4Dicon($action_file, $skeleton, $params, $scope);
        $dicon_base = basename($dicon_file);
        if($result == $this->Generator->RESULT_SUCCESS){
            $this->_sendMessage("{$dicon_base} -> Successfully generated. [{$dicon_file}]");
        } elseif($result == $this->Generator->RESULT_ALREADY){
            $this->_sendMessage("{$dicon_base} -> Already exists. [{$dicon_file}] -> skip");
        } else {
            $this->_sendMessage("{$dicon_base} -> Failed.");
        }
    }
}

