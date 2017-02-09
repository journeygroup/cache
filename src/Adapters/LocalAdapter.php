<?php

namespace Journey\Cache;

use Journey\Cache\CacheAdapterInterface;
use Journey\Cache\CacheException;

class LocalAdapter implements CacheAdapterInterface
{
    /**
     * A filesystem path to store cache values.
     *
     * @var string
     */
    protected $path;

    /**
     * Initialize a new localcache.
     *
     * @param string $filepath
     */
    public function __construct($filepath)
    {
        if (!is_dir($filepath)) {
            throw new CacheException('Cache file path is not a directory.');
        }
        $this->path = rtrim($filepath, "/");
    }

    /**
     * Must implement a set method.
     *
     * @param  string $key   key to set as the cache value.
     * @param  mixed  $value returns the value of the cached item.
     * @return $this
     */
    public function set($key, $value, $expiration = false)
    {
        $path = $this->filename($key);
        file_put_contents($path, $this->createValue($value, $expiration));
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
        $path = $this->filename($key);
        $file = @fopen($path, 'r');
        if (!$file) {
            return false;
        }
        $timestamp = fgets($file, 11);
        fclose($file);
        if ($timestamp > time() || $timestamp == "00000000000") {
            return substr(file_get_contents($path), 11);
        }
        return false;
    }

    /**
     * Must implement a delete method.
     *
     * @param  string $key delete a specific cached item by key.
     * @return $this
     */
    public function delete($key)
    {
        $file = $this->filename($key);
        unlink($file);
        return $this;
    }

    /**
     * Clear all of the values set by this cache instance.
     *
     * @return $this
     */
    public function clear()
    {
        $key = $this->key();
        $files = glob($this->path . "/_cache-" . $key . "*.cache");
        $this->setKey();
        $this->key(true);
        foreach ($files as $file) {
            unlink($file);
        }
        return $this;
    }

    /**
     * Returns the filename of a key.
     *
     * @param  string $key name of the key
     * @return void
     */
    public function filename($key)
    {
        return $this->path . "/_cache-" . $this->key() . "-" . $key . ".cache";
    }

    /**
     * Create a parsable value from the data and expiration date.
     *
     * @param  string  $value      value of the store
     * @param  integer $expiration integer value of the expiration (unix timestamp)
     * @return void
     */
    public function createValue($value, $expiration)
    {
        $expiration = $expiration ? str_pad($expiration, 11, "0". STR_PAD_LEFT) : "00000000000";
        return $expiration . $value;
    }

    /**
     * Gets the current cache namespace key.
     *
     * Note: to save on time spent reading/writing to disk, this method uses
     * static caching. Its important that when a cache key gets reset this
     * method has it's local cache reset by passing `true`.
     *
     * @param  boolean $reset resets the static cache.
     * @return void
     */
    public function key($reset = false)
    {
        static $key;
        $key = $reset ? false : $key;
        if (!$key) {
            $path = $this->path . "/.cache_key";
            if (file_exists($path)) {
                $key = file_get_contents($path);
            } else {
                $key = $this->setKey();
            }
        }
        return $key;
    }

    /**
     * Set the current cache key.
     */
    public function setKey()
    {
        $path = $this->path . "/.cache_key";
        $key = bin2hex(openssl_random_pseudo_bytes(6));
        file_put_contents($path, $key);
        return $key;
    }
}
