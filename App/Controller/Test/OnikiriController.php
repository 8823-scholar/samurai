<?php
/**
 * The MIT License
 *
 * Copyright (c) 2007-2013, Samurai Framework Project, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace App\Controller\Test;

use App\Controller\AppController;

/**
 * [description]
 *
 * @package     Samurai
 * @subpackage  [sub package]
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class OnikiriController extends AppController
{
    /**
     * @override
     */
    public function defineDeps()
    {
        $this->addModel('User');
    }


    /**
     * test of find.
     *
     * @access  public
     */
    public function find()
    {
        // most simply find sample.
        $user = $this->User->find(1);
        var_dump($user->id, $user->name, $user->mail);

        // find by simple where.
        $user = $this->User->find('name = ?', '真下');
        var_dump($user->id, $user->name, $user->mail);
        $user = $this->User->find('name = ? and mail = ?', '渡邊', 'foo3@ezample.jp');
        var_dump($user->id, $user->name, $user->mail);
        $user = $this->User->find('name = ? and mail = ?', ['渡邊', 'foo3@ezample.jp']);
        var_dump($user->id, $user->name, $user->mail);
        $user = $this->User->find('name = :name and mail = :mail', ['name' => '渡邊', 'mail' => 'foo3@ezample.jp']);
        var_dump($user->id, $user->name, $user->mail);

        // find by array condition.
        $user = $this->User->find(['where' => ['name = ? or name = ?', '森', '鶴巻'], 'order' => 'id DESC', 'group' => 'name']);
        var_dump($user->id, $user->name, $user->mail);
    }

    /**
     * test of find all.
     *
     * @access  public
     */
    public function findAll()
    {
        // most simpley findAll sample.
        $users = $this->User->findAll('name = ?', '真下');
        foreach ( $users as $user ) {
            var_dump($user->getId(), $user->getName(), $user->getMail());
        }

        // findAll simple cases.
        $users = $this->User->findAll('name = ? or name = ?', '渡邊', '鶴巻');
        foreach ( $users as $user ) {
            var_dump($user->getId(), $user->getName(), $user->getMail());
        }
        $users = $this->User->findAll('name = ? or name = ?', ['渡邊', '鶴巻']);
        foreach ( $users as $user ) {
            var_dump($user->getId(), $user->getName(), $user->getMail());
        }
        $users = $this->User->findAll('name = :name or name = :name2', ['name' => '渡邊', 'name2' => '鶴巻']);
        foreach ( $users as $user ) {
            var_dump($user->getId(), $user->getName(), $user->getMail());
        }
        
        // findAll by array condition.
        $users = $this->User->findAll(['where' => ['name = ? or name = ?', '森', '内野'], 'order' => 'id DESC', 'group' => 'name']);
        foreach ( $users as $user ) {
            var_dump($user->getId(), $user->getName(), $user->getMail());
        }
    }

    /**
     * use condition find.
     *
     * @access  public
     */
    public function findUseCondition()
    {
        // most simple.
        $cond = $this->User->condition();
        $cond->where->add(['name' => '木内']);
        $user = $this->User->find($cond);
        var_dump($user->id, $user->name, $user->mail);

        // chain condition.
        $cond = $this->User->condition();
        $cond->where->add(['name' => '真下'])->addlike('mail', '%@%')->notLike('mail', '%hoge%')
            ->addIn('name', ['木内', '真下'])->notIn('name', '内野', '森');
        $user = $this->User->find($cond);
        var_dump($user->id, $user->name, $user->mail);

        // chain condition findAll.
        $cond = $this->User->condition();
        $cond->where->add('name = ?', '鶴巻')->orAdd('name = ?', '渡邊')->orLike('mail', '%hoge%')
            ->orNotLike('mail', '%@%')->orIn('name', '相澤', '中田')->orIn('name', ['森', '内野'])->orNotIn('name', ['木内'])
            ->groupBy('mail', 'id')
            ->orderByField('mail', ['foo8ezample.jp', 'foo9@ezample.jp'])->orderBy('id DESC')
            ->limit(10)->page(1);
        $users = $this->User->findAll($cond);
        foreach ( $users as $user ) {
            var_dump($user->getId(), $user->getName(), $user->getMail());
        }
    }


    /**
     * bridge model find.
     *
     * @access  public
     */
    public function findBridgeModel()
    {
        // most simple find.
        $user = $this->User->where(['name' => '木内'])->find();
        var_dump($user->id, $user->name, $user->mail);

        // use chain.
        $user = $this->User->where(['name' => '木内'])->add('mail LIKE ?', '%@%')->find();
        var_dump($user->id, $user->name, $user->mail);

        // findAll
        $users = $this->User->where(['name' => '木内'])->orIN('name', ['真下', '渡邊'])->findAll();
        foreach ( $users as $user ) {
            var_dump($user->getId(), $user->getName(), $user->getMail());
        }
    }


    /**
     * save by entity.
     *
     * @access  public
     */
    public function save()
    {
        $user = $this->User->find(1);
        $user->name = $user->name . 1;
        $user->setMail(sprintf('%s@example.jp', md5(uniqid())));
        $user->save();
        var_dump($user->id, $user->name, $user->mail);
    }

    /**
     * create by entity.
     *
     * @access  public
     */
    public function createAndDestroy()
    {
        // create.
        $user = $this->User->build();
        $user->setName('build');
        $user->setMail('build@example.jp');
        $user->save();
        var_dump($user->getId(), $user->getName(), $user->getMail());

        // destroy
        $result = $user->destroy();
        var_dump($result);
    }
}

