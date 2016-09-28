<?php

namespace Asper\Util;

use Asper\Contract\Cacheable;

class GSJsonCache implements Cacheable {
	private $jsonFile = "gs://asper-bot-rates.appspot.com/cache.json";
	protected $data = [];

	public function __construct(){
		$this->data = $this->getData();
	}

	protected function getData(){
		if( !file_exists($this->jsonFile) ){
			return [];
		}
		
		$json = file_get_contents($this->jsonFile);
		return json_decode($json, true);		
	}

	protected function save(){
		$json = json_encode($this->data);
		file_put_contents($this->jsonFile, $json);
	}

	public function isValid($name){
		if( !file_exists($this->jsonFile) ){
			return false;
		}

		$expireSec = isset($this->data[$name]['expireSec']) ? $this->data[$name]['expireSec'] : 0;
		
		if( $expireSec == 0 ){
			return true;
		}

		$mTime = filemtime($this->jsonFile);
		return ($mTime + $expireSec) < time();		
	}

	public function get($name){
		if( $this->isValid($name) ){
			return $this->data[$name]['value'];
		}
		return null;
	}

	public function set($name, $val, $expireSec=0){
		$this->data[$name] = [
			'value' => $val,
			'expireSec' => $expireSec,
		];
		$this->save();
	}
}