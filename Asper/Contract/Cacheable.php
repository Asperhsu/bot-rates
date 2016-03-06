<?php

namespace Asper\Contract;

interface Cacheable {

	public function get($name);
	public function set($name, $val, $expireSec=null);
	
}