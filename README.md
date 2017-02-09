Journey Cache
-------------
[![Build Status](https://travis-ci.org/journeygroup/cache.svg?branch=master)](https://travis-ci.org/journeygroup/cache)
[![Code Coverage](https://scrutinizer-ci.com/g/journeygroup/cache/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/journeygroup/cache/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/journeygroup/cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/journeygroup/cache/?branch=master)

## What

Journey cache is a very simple string caching interface. It is a much simpler
alternative to the PSR-6 interface, and mostly useful for basic string cache.
The interface comes bundled with a local file cache 
([LocalAdapter](/src/Adapters/LocalAdapter.php)) and a memcached 
([MemcachedAdapter](/src/Adapters/LocalAdapter.php)) implementation.

## Interface

```php
interface CacheAdapterInterface
{
    public function set($key, $value, $expiration = 0);
    public function get($key);
    public function delete($key);
    public function clear();
}

```
It's simple enough you can probably guess how to write your own adapters, but
if you want more documentation, [read the interface](/src/CacheAdapterInterface.php).

_Note: The PSR-6 interface is fantastic, but sometimes you just don't need to
be as verbose or robust as it requires. This fills that gap._
