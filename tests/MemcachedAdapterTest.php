<?php

namespace Tests;

use Journey\Cache\Adapters\MemcachedAdapter;
use Journey\Cache\CacheAdapterInterface;
use Journey\Cache\CacheException;
use PHPUnit\Framework\TestCase;
use Memcached;

class MemcachedAdapterTest extends TestCase
{
    protected $adapter;

    /**
     * Initialize our local cache instance.
     */
    public function setUp()
    {
        $this->adapter = new MemcachedAdapter([
            'servers' => [[
                'host' => '127.0.0.1',
                'port' => 11211
            ]]
        ]);
        $this->adapter->clear();
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
     * Test the getInstance method.
     *
     * @return void
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf(Memcached::class, $this->adapter->getInstance());
    }
}
