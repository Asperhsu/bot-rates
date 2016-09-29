<?php

namespace Asper\Service;

use Asper\Contract\Cacheable;

class BotRate {
	protected $botSourceSite = "http://rate.bot.com.tw";
	protected $botSourceUrl = "xrt/flcsv/0/day";
	protected $csvColumnNameMapping = [
		'currency'=>0, 'buyCash'=>2, 'buySpot'=>3, 'sellCash'=>12, 'sellSpot'=>13
	];
	protected $updateTime = 0;

	public function __construct(){

	}

	public function getRates(){
		return $this->fetchRateFromSource();
	}

	protected function fetchCSV(){
		$url = implode('/', [$this->botSourceSite, $this->botSourceUrl] );

		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
				"Host: rate.bot.com.tw\r\n"
				)
			);

		$context = stream_context_create($opts);
		$file = file_get_contents($url, false, $context);

		$this->parseResponseHeaderUpdateTime($http_response_header);

		return $file;
	}

	protected function parseCSV($csvContents){
		$rates = [];
		$rows = explode("\r\n", $csvContents);

		foreach($rows as $index => $row){
			if($index == 0){ continue; }

			$data = explode(",", $row);
			if( count($data) < 10 ){ continue; }

			$rates += $this->parseCSVRow($data);
		}

		return $rates;
	}

	protected function parseCSVRow(Array $row){
		$rate = [];
		foreach($this->csvColumnNameMapping as $name => $colIndex){
			$$name = trim($row[$colIndex]);
		}

		$rate[ $currency ] = compact('buyCash', 'buySpot', 'sellCash', 'sellSpot');

		return $rate;
	}

	protected function parseResponseHeaderUpdateTime($headers){
		$str = '';
		foreach($headers as $header){
			if( strpos($header, 'attachment; filename') === false ){ continue; }

			$matches = [];
			preg_match('/ExchangeRate@(.*).csv/', $header, $matches);
			
			if( isset($matches[1]) ){
				$this->updateTime = strtotime($matches[1]);
			}
		}
	}

	public function fetchRateFromSource(){
		$csvContents = $this->fetchCSV();
		$rates = $this->parseCSV($csvContents);

		return [
			'createTime' => time(),
			'updateTime' => $this->updateTime,
			'rates' => $rates
		];
	}
}