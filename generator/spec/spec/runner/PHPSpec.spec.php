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
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * spec of PHPSpec.
 * 
 * @package     Samurai
 * @subpackage  Spec
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Spec_Runner_PHPSpec_Spec_Context extends Samurai_Spec_Context_PHPSpec
{
    // @dependencies

    
    // add spec method.
    // method name is need to start "it".
    
    public function itBeEqual()
    {
        $this->spec(1)->should->be(1);
    }

    public function itBeArray()
    {
        $this->spec(array())->should->beArray();
    }

    public function itBeInteger()
    {
        $this->spec(10)->should->beInteger();
    }

    public function itBeBoolean()
    {
        $this->spec(true)->should->beTrue();
        $this->spec(false)->should->beFalse();
    }

    public function itCompareThan()
    {
        $this->spec(10)->should->beLessThan(20);
        $this->spec(10)->should->beLessThanOrEqualTo(10);
        $this->spec(10)->should->beGreaterThan(5);
        $this->spec(10)->should->beGreaterThanOrEqualTo(10);
    }

    public function itBeNull()
    {
        $this->spec(NULL)->should->beNull();
    }

    public function itContainText()
    {
        $this->spec('Yes, I am.')->should->containText('am.');
    }

    public function itMatch()
    {
        $this->spec('foobarzoo')->should->match('/^foo/');
        $this->spec('foobarzoo')->should->match('/zoo$/');
    }

    public function itHasKey()
    {
        $map = array('key1' => 'value1', 'key2' => 'value2');
        $this->spec($map)->should->haveKey('key2');
    }

    public function itBeAnInstanceof()
    {
        $obj = new Exception();
        $this->spec($obj)->should->beAnInstanceOf('Exception');
    }
    
    
    
    /**
     * before case.
     *
     * @access  public
     */
    public function before()
    {
    }

    /**
     * after case.
     *
     * @access  public
     */
    public function after()
    {
    }

    /**
     * before all cases.
     *
     * @access  public
     */
    public function beforeAll()
    {
        $this->_injectDependencies();
        $this->_setupFixtures();
    }

    /**
     * after all cases.
     *
     * @access  public
     */
    public function afterAll()
    {
        $this->_clearFixtures();
    }
}

