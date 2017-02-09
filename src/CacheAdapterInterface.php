<?php

namespace Journey\Cache;

interface CacheAdapterInterface
{
    /**
     * Must implement a set method.
     *
     * @param  string   $key        key to set as the cache value.
     * @param  mixed    $value      returns the value of the cached item.
     * @param  integer  $expiration when the cache should expire (unix timestamp)
     * @return $this
     */
    public function set($key, $value, $expiration = 0);

    /**
     * Must implement a get method.
     *
     * @param  string $key   Get a cached item by key.
     * @return mixed         Returns cached item or false.
     */
    public function get($key);

    /**
     * Must implement a delete method.
     *
     * @param  string $key delete a specific cached item by key.
     * @return $this
     */
    public function delete($key);

    /**
     * Clear all of the values set by this cache instance.
     *
     * @return $this
     */
    public function clear();
}
