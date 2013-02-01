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
 * generate component.
 * 
 * @package    Samurai
 * @subpackage Generator
 * @copyright  Samurai Framework Project
 * @author     KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Action_Add_Component extends Generator_Action
{
    /**
     * 実行トリガー
     *
     * @access     public
     */
    public function execute()
    {
        parent::execute();
        if ( $this->_isUsage() || !$this->args ) return 'usage';
        if ( ! $this->_validate() ) return 'usage';
        
        $params = array();
        $params['is_model'] = $this->Request->get('model', false);
        $params['description'] = $this->Request->get('description');

        // enable multiple.
        while ( $name = array_shift($this->args) ) {
            $file = $this->_addComponent($name, $params);
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
            if(!preg_match('/^[a-zA-Z][a-zA-Z0-9_]+?$/', $name)){
                $this->ErrorList->add('name', "{$name} -> Component's name is Invalid. ([a-zA-Z0-9_])");
            }
        }
        return !$this->ErrorList->isExists();
    }


    /**
     * add component.
     *
     * @access     private
     * @param      string  $name
     * @param      array   $params
     */
    private function _addComponent($name, array $params = array())
    {
        $skeleton = $this->Generator->getSkeleton();
        list($result, $file) = $this->Generator->generate($name, $skeleton, $params);

        if($result == $this->Generator->RESULT_SUCCESS){
            $this->_sendMessage("{$name} -> Successfully generated. [{$file}]");
        } elseif($result == $this->Generator->RESULT_ALREADY){
            $this->_sendMessage("{$name} -> Already exists. [{$file}] -> skip");
        } else {
            $this->_sendMessage("{$name} -> Failed.");
        }
        return $file;
    }
}

