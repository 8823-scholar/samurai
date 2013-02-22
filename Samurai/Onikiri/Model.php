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
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri;

/**
 * model.
 *
 * @package     Onikiri
 * @subpackage  Model
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Model
{
    /**
     * name
     *
     * @access  public
     * @var     string
     */
    public $name;

    /**
     * table name
     *
     * @access  public
     * @var     string
     */
    public $table_name;

    /**
     * entity class name.
     *
     * @access  public
     * @var     string
     */
    public $entity_class;

    /**
     * primary key
     *
     * @access  public
     * @var     string
     */
    public $primary_key = 'id';

    /**
     * database alias.
     *
     * @access  public
     * @var     string
     */
    public $database = 'base';

    /**
     * target constant: master
     *
     * @const   string
     */
    const TARGET_MASTER = 'master';

    /**
     * target constant: slave
     *
     * @const   string
     */
    const TARGET_SLAVE = 'slave';

    /**
     * target constant: auto
     *
     * @const   string
     */
    const TARGET_AUTO = 'auto';



    /**
     * Get primary key.
     *
     * @access  public
     * @return  string
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }


    /**
     * Get table name.
     *
     * @access  public
     * @return  string
     */
    public function getTableName()
    {
        if ( $this->table_name ) return $this->table_name;

        // Blog\Articles -> blog_articles
        $class = explode('\\', get_class($this));
        array_shift($class);
        array_shift($class);
        $class = join('', $class);
        $names = preg_split('/(?=[A-Z])/', $class);
        array_shift($names);
        array_pop($names);
        return strtolower(join('_', $names));
    }


    /**
     * Get entity class.
     *
     * @access  public
     * @return  string
     */
    public function getEntityClass()
    {
        if ( $this->entity_class ) return $this->entity_class;
        
        // Blog\ArticlesModel -> Blog\ArticlesEntity
        $class = get_class($this);
        $class = preg_replace('/Model$/', '', $class);
        $class = $class . 'Entity';
        return $class;
    }




    /**
     * find by id or condtion.
     * return first entity.
     *
     * supported syntax:
     *
     * 1. $model->find($id);
     * 2. $model->find('name = ?', $name);
     * 3. $model->find('name = ? and gender = ?', $name, $gender);
     * 4. $model->find('name = ? and gender = ?', [$name, $gender]);
     * 5. $model->find('name = :name and gender = :gender', ['name' => $name, 'gender' => $gender]);
     * 6. $model->find(['where' => ['name = ?', $name], 'order' => 'id']);
     * 7. $model->find($cond);
     *
     * @access  public
     * @return  Entity
     */
    public function find()
    {
        // convert to condition.
        $cond = call_user_func_array(array($this, 'toCondition'), func_get_args());
        $cond->limit(1);

        // find.
        $entities = $this->findAll($cond);
        return $entities->first();
    }


    /**
     * find by condition.
     * return all entities.
     *
     * supported syntax:
     * 
     * 1. $model->findAll(1,2,3);
     * 2. $model->findAll([1,2,3]);
     * 3. $model->findAll('gender = ?', $gender);
     * 4. $model->findAll('gender = ? or gender = ?', $gender1, $gender2);
     * 5. $model->findAll('gender = ? or gender = ?', [$gender1, $gender2]);
     * 6. $model->findAll('gender = :gender1 or gender = :gender2', ['gender1' => $gender1, 'gender2' => $gender2]);
     * 7. $model->findAll(['where' => ['gender = ? or gender = ?', $gender1, $gender2], 'order' => 'id DESC']);
     * 8. $model->findAll($cond);
     *
     * @access  public
     * @return  Entities
     */
    public function findAll()
    {
        // convert to condition.
        $cond = call_user_func_array(array($this, 'toCondition'), func_get_args());

        // to SQL.
        $sql = $cond->toSQL();
        var_dump($sql);

        // query.
        $entities = $this->query($sql, $cond->getParams());
        return $entities;
    }


    /**
     * execute sql.
     *
     * @access  public
     * @param   string  $sql
     * @param   array   $params
     */
    public function query($sql, array $params = array())
    {
        //$helper = $this->getHelper();
        $con = $this->establishConnection();
        $sth = $con->prepare($sql, array(Connection::ATTR_CURSOR => Connection::CURSOR_SCROLL));

        // bind params
        foreach($params as $key => $value){
            $type = Connection::PARAM_STR;
            if ( is_null($value) ) {
                $type = Connection::PARAM_NULL;
            } elseif ( is_int($value) ) {
                $type = Connection::PARAM_INT;
            } elseif ( is_bool($value) ) {
                $type = Connection::PARAM_BOOL;
            } elseif ( is_resource($value) ) {
                $type = Connection::PARAM_LOB;
            }
            if ( is_int($key) ) {
                $sth->bindValue($key + 1, $value, $type);
            } else {
                $sth->bindValue($key, $value, $type);
            }
        }

        $result = $sth->execute();
        $entities = new Entities($this, $sth);
        return $entities;
    }


    /**
     * connect to backend.
     *
     * @access  public
     * @param   string  $target
     */
    public function establishConnection($target = self::TARGET_AUTO)
    {
        if ( $target === self::TARGET_AUTO ) {
            $target = $this->inTx() ? self::TARGET_MASTER : self::TARGET_SLAVE;
        }
        
        $manager = Manager::singleton();
        return $manager->establishConnection($this->database, $target);
    }



    /**
     * build entity.
     *
     * @access  public
     * @param   array   $attributes
     * @param   boolean $exists
     * @return  Entity
     */
    public function build($attributes, $exists = false)
    {
        $class = $this->getEntityClass();
        $entity = new $class($this, $attributes, $exists);
        return $entity;
    }






    /**
     * get condition instance.
     *
     * @access  public
     * @return  Samurai\Onikiri\Condition\Condition
     */
    public function getCondition()
    {
        $cond = new Condition\Condition();
        $cond->from($this->getTableName());
        return $cond;
    }


    /**
     * convert to condition.
     *
     * @access  public
     * @param   mixed   $args
     * @return  Samurai\Onikiri\Condition
     */
    public function toCondition()
    {
        $args = func_get_args();
        $first = array_shift($args);
        $cond = $this->getCondition();

        // already condition.
        if ( $first instanceof Condition\Condition ) {
            return $first;
        }

        // first argument is int ? then it's id.
        if ( is_numeric($first) ) {
            $cond->where->add(sprintf('%s = ?', $this->getPrimaryKey()), $first);

        // first argument is string ? then simple where.
        } elseif( is_string($first) ) {
            array_unshift($args, $first);
            call_user_func_array(array($cond->where, 'add'), $args);

        // first argument is array ? then id list or array condition.
        } elseif( is_array($first) ) {
            // when id list.
            if ( isset($first[0]) ) {
                call_user_func_array(array($cond->where, 'add'), $args);

            // when array condition.
            } else {
                $cond->import($first);
            }
        }
        
        var_dump($cond);
        return $cond;
    }




    /**
     * is in transaction ?
     *
     * @access  public
     * @return  boolean
     */
    public function inTx()
    {
        $manager = Manager::singleton();
        return $manager->inTx();
    }




    /**
     * magick method.
     *
     * findBy*
     * findAllBy*
     *
     * @access  public
     * @params  string  $method
     * @param   array   $args
     */
    public function __call($method, array $args = array())
    {
    }
}

