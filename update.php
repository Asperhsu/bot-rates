<?php	// update bot rate
require("vendor/autoload.php");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Taipei');


$botRate = new Asper\Service\BotRate();
$data = $botRate->getRates();
$rateJson = json_encode($data['rates']);

$cache = new Asper\Util\GSJsonCache();

$cache->set('rates', $rateJson);
$cache->set('createTime', $data['createTime']);
$cache->set('updateTime', $data['updateTime']);


header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);