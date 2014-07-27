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
     * @traits
     */
    use TransactionHolder;


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
     * get scope defines
     *
     * @return  array
     */
    public function scopes()
    {
        return [];
    }


    /**
     * find by id or criteria.
     * return first entity.
     *
     * supported syntax:
     *
     * 1. $model->find($id);
     * 2. $model->find('name = ?', $name);
     * 3. $model->find('name = ? and gender = ?', [$name, $gender]);
     * 4. $model->find('name = :name and gender = :gender', [':name' => $name, ':gender' => $gender]);
     * 5. $model->find($criteria);
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function find()
    {
        // convert to criteria.
        $criteria = call_user_func_array(array($this, 'argsToCriteria'), func_get_args());
        $criteria->limit(1);

        // find.
        $entities = $this->findAll($criteria);
        return $entities->first();
    }
    
    /**
     * find by criteria.
     * return all entities.
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function findAll()
    {
        // convert to criteria.
        $criteria = call_user_func_array(array($this, 'argsToCriteria'), func_get_args());

        // to SQL.
        $sql = $criteria->toSQL();

        // query
        $sth = $this->query($sql, $criteria->getParams());

        // to entoties
        $entities = new Entities($this);
        foreach ($sth->fetchAll(Connection::FETCH_ASSOC) as $row) {
            $entities->add($this->build($row, true));
        }

        return $entities;
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
        if (! $entity->isExists()) {
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
        if ($entity->isExists()) {
            $this->delete($entity->getPrimaryValue());
        }
    }
    
    
    /**
     * create.
     *
     * @param   array   $attributes
     * @return  Samurai\Onikiri\Entity
     */
    public function create($attributes = array())
    {
        $entity = $this->build($attributes);

        // to SQL.
        $criteria = $this->criteria();
        $sql = $criteria->toInsertSQL($attributes);

        // query.
        $sth = $this->query($sql, $criteria->getParams(), Database::TARGET_MASTER);
        if ($sth->isSuccess()) {
            $entity->exists = true;
            $entity->setPrimaryValue($sth->lastInsertId());
            return $entity;
        }
    }
    
    
    /**
     * update by criteria.
     *
     * $table->update(['name' => 'Kiuchi'], 'id = ?', 1);
     * $table->update(['name' => 'Kiuchi'], $criteria);
     *
     * @param   array   attributes
     * @param   mixed   criteria
     */
    public function update()
    {
        // convert to criteria.
        $args = func_get_args();
        $attributes = array_shift($args);
        $criteria = call_user_func_array(array($this, 'argsToCriteria'), $args);

        // to SQL.
        $sql = $criteria->toUpdateSQL($attributes);

        // query.
        $sth = $this->query($sql, $criteria->getParams(), Database::TARGET_MASTER);
        return $sth->isSuccess();
    }
    
    
    /**
     * delete by condition.
     *
     * @param   array   attributes
     * @param   mixed   conditions
     */
    public function delete()
    {
        // convert to criteria.
        $criteria = call_user_func_array(array($this, 'argsToCriteria'), func_get_args());

        // to SQL.
        $sql = $criteria->toDeleteSQL();

        // query.
        $sth = $this->query($sql, $criteria->getParams(), Database::TARGET_MASTER);
        return $sth->isSuccess();
    }
    
    
    /**
     * execute sql.
     *
     * @param   string  $sql
     * @param   array   $params
     * @param   string  $target
     * @return  Samurai\Onikiri\Statement
     */
    public function query($sql, array $params = [], $target = Database::TARGET_MASTER)
    {
        $con = $this->establishConnection($target);
        $sth = $con->prepare($sql);

        // bind params
        foreach($params as $key => $value){
            $type = Connection::PARAM_STR;
            if (is_null($value)) {
                $type = Connection::PARAM_NULL;
            } elseif (is_int($value)) {
                $type = Connection::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = Connection::PARAM_BOOL;
            } elseif (is_resource($value)) {
                $type = Connection::PARAM_LOB;
            }
            $sth->bindValue($key, $value, $type);
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
    public function establishConnection($target = Database::TARGET_MASTER)
    {
        // tx
        $tx = $this->getTx();
        if ($tx && $tx->isValid()) $target = Database::TARGET_MASTER;

        $connection = $this->getOnikiri()->establishConnection($this->database, $target);
        if ($tx && $tx->isValid()) {
            $tx->setConnection($connection);
            $tx->begin();
        }

        return $connection;
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
    
    /**
     * args convert to criteria.
     *
     * @access  public
     * @param   mixed   $args
     * @return  Samurai\Onikiri\Criteria\Criteria
     */
    public function argsToCriteria()
    {
        $args = func_get_args();
        $first = array_shift($args);
        $criteria = $this->criteria();

        // already converted.
        if ($first instanceof Criteria\Criteria) {
            return $first;
        }

        // first argument is int ? then it's id.
        if (is_numeric($first)) {
            $criteria->where(sprintf('%s = ?', $this->getPrimaryKey()), $first);

        // first argument is string ? then simple where.
        } elseif(is_string($first)) {
            array_unshift($args, $first);
            call_user_func_array(array($criteria, 'where'), $args);
        }
        
        return $criteria;
    }
    
    
    /**
     * magick method.
     *
     * findBy*
     * findAllBy*
     *
     * @param   string  $method
     * @param   array   $args
     * @return  mixed
     */
    public function __call($method, array $args = array())
    {
        $scopes = $this->scopes();
        if (array_key_exists($method, $scopes)) {
            return $scopes[$method];
        } elseif (preg_match('/^findBy(.+)$/', $method, $matches)) {
            $column = strtolower($matches[1]);
            return $this->find("{$column} = ?", array_shift($args));
        } elseif (preg_match('/^findAllBy(.+)$/', $method, $matches)) {
            $column = strtolower($matches[1]);
            return $this->findAll("{$column} = ?", array_shift($args));
        }
    }
}

