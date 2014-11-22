<?php

namespace Samurai\Samurai\Component\DataStore;

use Redis as ExtRedis;

/**
 * bridge to data store redis.
 *
 * @package     Samurai.Samurai
 * @subpackage  Component.DataStore
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 */
class Redis
{
    /**
     * driver
     *
     * @var     Redis
     */
    public $driver;


    /**
     * construct
     */
    public function __construct()
    {
        if (! $this->isSupports()) throw new \Exception('install redis, please.  http://pecl.php.net/package/redis');
        $this->driver = new ExtRedis();
    }
    
    
    /**
     * connect to redis
     *
     * @param   string  $host
     * @param   int     $port
     */
    public function connect($host, $port = 6379)
    {
        return $this->driver->connect($host, $port);
    }


    /**
     * set string
     *
     * @param   string      $key
     * @param   string|int  $value
     */
    public function set($key, $value)
    {
        $this->driver->set($key, (string)$value);
    }

    /**
     * get string
     *
     * @param   string  $key
     * @return  string
     */
    public function get($key)
    {
        $value = $this->driver->get($key);
        return $value !== false ? $value : null;
    }


    /**
     * add data to list
     *
     * @param   string      $key
     * @param   string|int  $value
     */
    public function addList($key, $value)
    {
        $this->driver->rPush($key, (string)$value);
    }

    /**
     * get list
     *
     * @param   string  $key
     * @return  array
     */
    public function getList($key, $offset = 0, $limit = -1)
    {
        return $this->driver->lRange($key, $offset, $limit);
    }


    /**
     * add data to sets
     *
     * @param   string      $key
     * @param   string|int  $value
     */
    public function addSet($key, $value)
    {
        $this->driver->sAdd($key, $value);
    }

    /**
     * get set
     *
     * @param   string
     * @return  array
     */
    public function getSet($key)
    {
        return $this->driver->sMembers($key);
    }


    /**
     * add data to sorted set.
     *
     * @param   string      $key
     * @param   string      $member
     * @param   int|float   $value
     */
    public function addSortedSet($key, $member, $value)
    {
        $this->driver->zAdd($key, $value, $member);
    }

    /**
     * get sorted set rank
     *
     * @param   string      $key
     * @param   string      $member
     */
    public function getSortedRank($key, $member)
    {
        return $this->getSortedRankAsc($key, $member);
    }
    
    /**
     * get sorted set rank by asc
     *
     * @param   string      $key
     * @param   string      $member
     */
    public function getSortedRankAsc($key, $member)
    {
        $score = $this->driver->zScore($key, $member);
        if ($score === false) return null;
        return $this->driver->zCount($key, '-inf', -- $score);
    }
    
    /**
     * get sorted set rank by desc
     *
     * @param   string      $key
     * @param   string      $member
     */
    public function getSortedRankDesc($key, $member)
    {
        $score = $this->driver->zScore($key, $member);
        if ($score === false) return null;
        return $this->driver->zCount($key, ++ $score, '+inf');
    }


    /**
     * delete data
     *
     * @param   string  $key
     */
    public function delete($key)
    {
        $this->driver->delete($key);
    }



    /**
     * is redis supported ?
     *
     * @return  boolean
     */
    public function isSupports()
    {
        return extension_loaded('redis');
    }

}
