<?php

namespace Asper\Util;

use Asper\Contract\Cacheable;

class MemCache implements Cacheable {
	private $cache;


	public function __construct(){		
		$this->cache = new \Memcache;
	}

	public function get($name){
		return $this->cache->get($name);
	}

	public function set($name, $val, $expireSec=null){
		$MEMCACHE_COMPRESSED = 0;
		return $this->cache->set($name, $val, $MEMCACHE_COMPRESSED, $expireSec);
	}
}