<?php	// update bot rate
require("vendor/autoload.php");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Taipei');

$cache = new Asper\Util\MemCache();
$botRate = new Asper\Service\BotRate();

$cacheExpireSec = 86400; //24hours
$data = $botRate->getRates();

$rateJson = json_encode($data['rates']);
$cache->set('rates', $rateJson, $cacheExpireSec);
$cache->set('createTime', $data['createTime'], $cacheExpireSec);
$cache->set('updateTime', $data['updateTime'], $cacheExpireSec);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);