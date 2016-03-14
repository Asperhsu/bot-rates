<?php
require('vendor/autoload.php');
date_default_timezone_set('Asia/Taipei');

$jsonUrl = "currency.json";
$currencyList = array(
	'USD' => '美元',
	'HKD' => '港幣',
	'GBP' => '英鎊',
	'AUD' => '澳幣',
	'CAD' => '加拿大幣',
	'SGD' => '新加坡幣',		
	'CHF' => '瑞士法郎',
	'JPY' => '日圓',
	'ZAR' => '南非幣',
	'SEK' => '瑞典克朗',
	'NZD' => '紐西蘭幣',
	'THB' => '泰銖',
	'PHP' => '菲律賓披索',
	'IDR' => '印尼盾',
	'EUR' => '歐元',
	'KRW' => '菲律賓披索',
	'VND' => '越南幣',
	'MYR' => '馬來西亞幣',
	'CNY' => '人民幣',		
);

//retrive cache data
$cache = new Asper\Util\MemCache();

$createTime = $cache->get('createTime');
$updateTime = $cache->get('updateTime');
$rateJson = $cache->get('rates');
$rates = json_decode($rateJson, true);

$data = [
	'createTime' => $createTime,
	'updateTime' => $updateTime,
	'rates'	=> $rates
];

?>
<!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>BOT Rates List Page</title>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style>
			body{ padding:0 40px;}
			th,td{ text-align: center; }
			td.currency{ text-align: right; }
		</style>
	</head>
	<body>
		<h1 class="text-center">台灣銀行匯率 Demo</h1>
		
		<div class="container-fluid">
			<div class="pull-left">
				<form class="form-inline">
					<div class="form-group">
						<label class="sr-only">外幣金額</label>
						<p class="form-control-static">外幣金額</p>
					</div>

					<div class="form-group">
						<input type='number' class="form-control" id="foreign-amount" value='1' style="width:7em"/>
					</div>	
				</form>
			</div>

			<div class="pull-right">
				<form class="form-inline">
					<div class="form-group">
						<label class="sr-only">台幣金額</label>
						<p class="form-control-static">台幣金額</p>
					</div>
					<div class="form-group">
						<input type='number' class="form-control" id="twd-amount" value='1000'style="width:7em" />
					</div>
					<div class="form-group">
						<select class="form-control" id="rate-type">
							<option value="buyCash" selected="selected">買入現金</option>
							<option value="buySpot">買入即期</option>
							<option value="sellCash">賣出現金</option>
							<option value="sellSpot">賣出即期</option>
						</select>
					</div>		
				</form>
			</div>
		</div>

		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>幣值</th>
					<th>買入現金</th>
					<th>買入即期</th>
					<th>賣出現金</th>
					<th>賣出即期</th>
					<th>台幣可換</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($currencyList as $iso3 => $cname):?>
				<?php 
					if(isset($data['rates'][$iso3])){ 
						$rate = $data['rates'][$iso3]; 
					}else{
						continue;
					}
				?>
				<tr>
					<td class="currency" data-cur="<?php echo $iso3;?>">
						<a href="<?php echo $jsonUrl.'?'.$iso3;?>"><?php echo $cname?>(<?php echo $iso3;?>)</a>
					</td>
					<td class="buyCash" data-rate="<?php echo $rate['buyCash'];?>">
						<?php echo $rate['buyCash'];?>
					</td>
					<td class="buySpot" data-rate="<?php echo $rate['buySpot'];?>">
						<?php echo $rate['buySpot'];?>
					</td>
					<td class="sellCash" data-rate="<?php echo $rate['sellCash'];?>">
						<?php echo $rate['sellCash'];?>
					</td>
					<td class="sellSpot" data-rate="<?php echo $rate['sellSpot'];?>">
						<?php echo $rate['sellSpot'];?>
					</td>
					<td class="change-amount"></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		
		<p style='text-align: center;'>
			<a href="http://rate.bot.com.tw/Pages/Static/UIP003.zh-TW.htm">資料來源: 台灣銀行</a> | 
			更新時間：<?php echo date('Y-m-d H:i:s', $data['updateTime']);?> |
			幣值為台幣(TWD) | 幣值轉換僅供參考，實際請以銀行匯兌為準
		</p>

		<a href="https://github.com/Aspertw/bot-rates"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>

		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script>
		$(function(){
			$("#foreign-amount").change(function(e){
				var amount = $(this).val();
				if( amount <= 0 ){ 
					amount = 1;  $(this).val(amount);
				}

				$("table:first tbody td").each(function(){
					var rate = $(this).data('rate');
					if( rate == undefined ){ return; }

					$(this).text( (amount * rate).toFixed(3) );
				});
			});

			$("#twd-amount").change(function(e){
				if( $(this).val() <= 0 ){ 
					$(this).val(1);
				}
				changeTwdAmount();
			});
			$("#rate-type").change(function(e){
				changeTwdAmount();
			});

			changeTwdAmount();
			function changeTwdAmount(){
				var amount = $("#twd-amount").val();
				var type = $("#rate-type").val();

				$("table:first tbody td."+type).each(function(){
					var rate = $(this).data('rate');
					var currency = (amount/rate).toFixed(3);
					$(this).parents('tr').find('td.change-amount').text(currency);
				});			
			}
		});
		</script>
	</body>
</html>

	