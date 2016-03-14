<?php

namespace Asper\Service;

use Asper\Contract\Cacheable;

class BotRate {
	protected $botSourceSite = "http://rate.bot.com.tw";
	protected $botSourceUrl = "Pages/Static/UIP003.zh-TW.htm";
	protected $csvColumnNameMapping = [
		'currency'=>0, 'buyCash'=>2, 'buySpot'=>3, 'sellCash'=>12, 'sellSpot'=>13
	];

	public function __construct(){

	}

	public function getRates(){
		return $this->fetchRateFromSource();
	}

	protected function fetchSourceHtml(){
		$url = implode('/', [$this->botSourceSite, $this->botSourceUrl] );
		
		try {
			$html = file_get_contents($url);
		} catch (Exception $e) {
			die("Open Bot Url Failed");
		}

		return $html;
	}

	protected function findCSVUrl($html){
		// find link element
		$aPos = strpos($html, '<a id="DownloadCsv"');
		$hrefPos = strpos($html, 'href="', $aPos);
		$endPos = strpos($html, '">', $hrefPos);
		
		// get link attribute
		$link = substr($html, $hrefPos+6, $endPos-$hrefPos-6);
		$link = htmlspecialchars_decode($link);

		return $link;
	}

	protected function parseUpdateTimeFromCSVLink($csvLink){
		$datePos = strpos($csvLink, '&date=');
		$dateLength = 6;
		$dateString = substr($csvLink, $datePos + $dateLength);
		$updateTime = strtotime($dateString);

		return $updateTime;
	}

	protected function parseCSV($csvLink){
		$url = implode('/', [$this->botSourceSite, $csvLink] );

		try {
			$fp = fopen($url, "r");
		} catch (Exception $e) {
			die("Open Bot CSV File Error");
		}

		$row = 0;
		$rates = [];

		while( ($data = fgetcsv($fp, 1000, ',')) !== FALSE ){
			$row++; if($row == 1){ continue; }

			$rates += $this->parseCSVRow($data);
		}

		fclose($fp);

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

	public function fetchRateFromSource(){
		$sourceHtml = $this->fetchSourceHtml();		
		$csvLink = $this->findCSVUrl($sourceHtml);
		$updateTime = $this->parseUpdateTimeFromCSVLink($csvLink);

		$rates = $this->parseCSV($csvLink);

		return [
			'createTime' => time(),
			'updateTime' => $updateTime,
			'rates' => $rates
		];
	}
}