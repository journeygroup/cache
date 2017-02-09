<?php

namespace Journey\Cache\Adapters;

use Journey\Cache\CacheAdapterInterface;
use Memcached;

class MemcachedAdapter implements CacheAdapterInterface
{
    /**
     * Instance of memcached.
     *
     * @var \Memcached
     */
    protected $instance;

    /**
     * Initialize a new localcache.
     *
     * @param string $filepath
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->instance = new Memcached();
        if ($config['servers']) {
            foreach ($config['servers'] as $server) {
                $this->instance->addServer($server['host'], $server['port']);
            }
        }
    }

    /**
     * Must implement a set method.
     *
     * @param  string $key   key to set as the cache value.
     * @param  mixed  $value returns the value of the cached item.
     * @param  integer  $expiration when the cache should expire (unix timestamp)
     * @return $this
     */
    public function set($key, $value, $expiration = 0)
    {
        $key = $this->createKey($key);
        $this->instance->set($key, $value, $expiration);
        return $this;
    }

    /**
     * Must implement a get method.
     *
     * @param  string $key   Get a cached item by key.
     * @return mixed         Returns cached item or false.
     */
    public function get($key)
    {
        $key = $this->createKey($key);
        return $this->instance->get($key);
    }

    /**
     * Must implement a delete method.
     *
     * @param  string $key delete a specific cached item by key.
     * @return $this
     */
    public function delete($key)
    {
        $this->instance->delete($this->createKey($key));
        return $this;
    }

    /**
     * Clear all of the values set by this cache instance.
     *
     * @return $this
     */
    public function clear()
    {
        $this->instance->delete('memcached_adapter_namespace');
        $this->key(true);
        return $this;
    }

    /**
     * Return the Memcached instance.
     *
     * @return \Memcached
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Get the value of a namespace key.
     *
     * @return string
     */
    public function key($reset = false)
    {
        static $key;
        $key = $reset ? false : $key;
        if (!$key) {
            $key = $this->instance->get('memcached_adapter_namespace', function ($memc, $key, &$value) {
                $value = bin2hex(openssl_random_pseudo_bytes(8));
                return true;
            });
        }
        return $key;
    }

    /**
     * Given a user provided key, return a namespaced key.
     *
     * @return string
     */
    public function createKey($key)
    {
        return $this->key() . "-" . $key;
    }
}
