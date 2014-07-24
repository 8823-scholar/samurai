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

namespace Samurai\Onikiri;

use Samurai\Onikiri\Connection;
use Samurai\Onikiri\Database;

/**
 * entity repository table.
 *
 * EntityTable is relational to database table.
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class EntityTable
{
    /**
     * table name
     *
     * @var     string
     */
    public $table_name;
    
    /**
     * entity class name.
     *
     * @var     string
     */
    public $entity_class;

    /**
     * primary key name.
     *
     * @var     string
     */
    public $primary_key = 'id';
    
    /**
     * database alias.
     *
     * @var     string
     */
    public $database = 'base';

    /**
     * onikiri instance.
     *
     * @var     Samurai\Onikiri\Onikiri
     */
    public $onikiri;


    /**
     * constructor
     *
     * @param   Samurai\Onikiri\Onikiri     $onikiri
     */
    public function __construct(Onikiri $onikiri)
    {
        $this->onikiri = $onikiri;
    }


    /**
     * get onikiri instance
     *
     * @return  Samurai\Onikiri\Onikiri
     */
    public function getOnikiri()
    {
        return $this->onikiri;
    }


    /**
     * get table name.
     *
     * User -> user
     * UserArticleTable -> user_article
     *
     * @return  string
     */
    public function getTableName()
    {
        if ($this->table_name) return $this->table_name;

        $tmp = explode('\\', get_class($this));
        $class = array_pop($tmp);
        $names = preg_split('/(?=[A-Z])/', $class);
        array_shift($names);
        array_pop($names);
        return strtolower(join('_', $names));
    }

    /**
     * set table name.
     *
     * @param   string  $name
     */
    public function setTableName($name)
    {
        $this->table_name = $name;
    }


    /**
     * get primary key.
     *
     * @return  string
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * set primary key.
     *
     * @param   string  $key
     */
    public function setPrimaryKey($key)
    {
        $this->primary_key = $key;
    }
    
    
    /**
     * get entity class.
     *
     * UserTable -> User
     * UserArticleTable -> UserArticle
     *
     * @return  string
     */
    public function getEntityClass()
    {
        if ($this->entity_class) return $this->entity_class;
        
        $class = get_class($this);
        $class = preg_replace('/Table$/', '', $class);
        return $class;
    }

    /**
     * set entity class.
     *
     * @param   string  $class
     */
    public function setEntityClass($class)
    {
        $this->entity_class = $class;
    }


    /**
     * get database alias.
     *
     * @return  string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * set database alias.
     *
     * @param   string  $alias
     */
    public function setDatabase($alias)
    {
        $this->database = $alias;
    }
    
    
    /**
     * build entity.
     *
     * @param   array   $attributes
     * @param   boolean $exists
     * @return  Entity
     */
    public function build($attributes = array(), $exists = false)
    {
        $class = $this->getEntityClass();
        $entity = new $class($this, $attributes, $exists);
        return $entity;
    }
    
    
    /**
     * save or create entity.
     *
     * @param   Entity  $entity
     * @param   array   $attributes
     */
    public function save(Entity $entity, $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $entity->$key = $value;
        }

        // when new record.
        if ($entity->isNew()) {
            // TODO: custom primary value handler.
            $new = $this->create($entity->toArray());
            $entity->exists = true;
            $entity->setPrimaryValue($new->getPrimaryValue());
        }

        // when update.
        else {
            $attributes = $entity->getAttributes(true);
            if (! $attributes) return;

            return $this->update($attributes, $entity->getPrimaryValue());
        }
    }
    
    
    /**
     * destroy entity.
     *
     * @param   Entity  $entity
     */
    public function destroy(Entity $entity)
    {
        /*
        if (! $entity->isNew()) {
            $this->delete($entity->getPrimaryValue());
        }
         */
    }
    
    
    /**
     * create.
     *
     * @param   array   $attributes
     * @return  Entity
     */
    public function create($attributes = array())
    {
        $entity = $this->build($attributes);

        // to SQL.
        $cri = $this->criteria();
        $sql = $cri->toInsertSQL($attributes);

        // query.
        /*
        $sth = $this->query($sql, $cri->getParams());
        if ($sth->isSuccess()) {
            $entity->exists = true;
            $entity->setPrimaryValue($sth->lastInsertId());
            return $entity;
        }
         */
    }
    
    
    /**
     * update by condition.
     *
     * @param   array   attributes
     * @param   mixed   conditions
     */
    public function update()
    {
        /*
        // convert to condition.
        $args = func_get_args();
        $attributes = array_shift($args);
        $cond = call_user_func_array(array($this, 'toCondition'), $args);

        // to SQL.
        $sql = $cond->toUpdateSQL($attributes);
        var_dump($sql, $cond->getParams());

        // query.
        $sth = $this->query($sql, $cond->getParams());
        return $sth->isSuccess();
         */
    }
    
    
    /**
     * delete by condition.
     *
     * @param   array   attributes
     * @param   mixed   conditions
     */
    public function delete()
    {
        /*
        // convert to condition.
        $args = func_get_args();
        $cond = call_user_func_array(array($this, 'toCondition'), $args);

        // to SQL.
        $sql = $cond->toDeleteSQL();
        var_dump($sql, $cond->getParams());

        // query.
        $sth = $this->query($sql, $cond->getParams());
        return $sth->isSuccess();
         */
    }
    
    
    /**
     * execute sql.
     *
     * @param   string  $sql
     * @param   array   $params
     * @return  Samurai\Onikiri\Statement
     */
    public function query($sql, array $params = [])
    {
        $con = $this->establishConnection();
        $sth = $con->prepare($sql, [Connection::ATTR_CURSOR => Connection::CURSOR_SCROLL]);

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
        return $sth;
    }
    
    
    /**
     * connect to backend.
     *
     * @param   string  $target
     * @return  Samurai\Onikiri\Connection
     */
    public function establishConnection($target = Database::TARGET_AUTO)
    {
        /*
        if ($target === Database::TARGET_AUTO) {
            $target = $this->inTx() ? Database::TARGET_MASTER : Database::TARGET_SLAVE;
        }
         */
        return $this->getOnikiri()->establishConnection($this->database, $target);
    }
    
    
    /**
     * get criteria instance.
     *
     * @return  Samurai\Onikiri\Criteria\Criteria
     */
    public function criteria()
    {
        $cri = new Criteria\Criteria($this);
        $cri->setTable($this);
        return $cri;
    }
}

