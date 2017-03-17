<?php

namespace Tests;

use Journey\Cache\CacheAdapterInterface;
use Journey\Cache\CacheException;
use Journey\Cache\Adapters\LocalAdapter;
use PHPUnit\Framework\TestCase;

class LocalCache extends TestCase
{
    protected $adapter;

    /**
     * Initialize our local cache instance.
     */
    public function setUp()
    {
        if (file_exists('/tmp/.cache_key')) {
            unlink('/tmp/.cache_key');
        }
        $this->adapter = new LocalAdapter('/tmp');
    }

    /**
     * Test that the adapter implements the interface.
     *
     * @return void
     */
    public function testInterfaceCompliance()
    {
        $this->assertTrue($this->adapter instanceof CacheAdapterInterface);
    }

    /**
     * Test that an object can be set and retrieved by string value.
     *
     * @return void
     */
    public function testSetAndGet()
    {
        $value = microtime();
        $this->adapter->set('set_test', $value);
        $this->assertEquals($value, $this->adapter->get('set_test'));
    }

    /**
     * Test that sets expire after a given amount of time.
     *
     * @return void
     */
    public function testExpiration()
    {
        $value = microtime();
        $this->adapter->set('expiration_test', $value, time()-1);
        $this->assertFalse($this->adapter->get('expiration_test'));
    }

    /**
     * Test that an object can be set, retrieved, and deleted.
     *
     * @return void
     */
    public function testDelete()
    {
        $value = microtime();
        $this->adapter->set('delete_test', $value);
        $this->adapter->delete('delete_test');
        $this->assertFalse($this->adapter->get('delete_test'));
    }

    /**
     * Test that an object can be set and cleared.
     *
     * @return void
     */
    public function testClear()
    {
        $this->adapter->set('clear_test', microtime());
        $this->adapter->set('clear_test_2', microtime());
        $this->adapter->clear();
        $this->assertFalse($this->adapter->get('clear_test'));
        $this->assertFalse($this->adapter->get('clear_test_2'));
    }

    /**
     * Test for an exception on a bad directory.
     *
     * @return void
     */
    public function testBadDirectory()
    {
        $this->expectException(CacheException::class);
        new LocalAdapter('/tmp/' . bin2hex(openssl_random_pseudo_bytes(16)));
    }

    /**
     * Test some crazy directory characters.
     *
     * @return void
     */
    public function testDirectoryCharacters()
    {
        $key = '/$this/.string/>slashes=with.{file}/.extensions*.php%20/ andswers`here`||and&&itslonger'
            . "\nthan 255 characters, which is the max # of characers for a file name length"
            . 'also there may be some "issues"/"problems" with these kinds of keys. But not here!!||˙here˙or`here`'
            . 'that is all.';
        $this->adapter->set($key, "uY,QtA}EhXaGvh93PaB");
        $this->assertEquals($this->adapter->get($key), 'uY,QtA}EhXaGvh93PaB');
        $this->adapter->clear();
    }

    /**
     * Test the permissions of the local cache (should be wide open 777).
     *
     * @return void
     */
    public function testCachePermissions()
    {
        $this->adapter->set('key', 'value');
        $key = $this->adapter->key();
        $files = glob("/tmp/_cache-" . $key . "*.cache");
        $permissions = decoct(fileperms($files[0]) & 0777);
        $this->assertEquals(666, $permissions);
        $this->adapter->clear();
    }
}
