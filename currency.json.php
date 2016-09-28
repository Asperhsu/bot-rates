<?php
require("vendor/autoload.php");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Taipei');

//retrive cache data
$cache = new Asper\Util\GSJsonCache();

$createTime = $cache->get('createTime');
$updateTime = $cache->get('updateTime');
$rateJson = $cache->get('rates');
$rates = json_decode($rateJson, true);

$data = [
	'createTime' => $createTime,
	'updateTime' => $updateTime,
	'rates'	=> $rates
];

//check paremeter
$currency = '';
if( count($_GET) ){
	$getKeys = array_keys($_GET);
	$currency = array_shift($getKeys);
	$currency = strtoupper($currency);
}

if( isset($data['rates'][$currency]) ){
	$ret = [
		'updateTime' => $data['updateTime'],
		'rates'		 => $data['rates'][$currency],
	];
}else{
	$ret = $data;
}

header('Content-Type: application/json');
echo json_encode($ret, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);