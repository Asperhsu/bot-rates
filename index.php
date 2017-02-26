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

?>
<!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>台灣銀行外幣匯率</title>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style>
			#table-rate th, #table-rate td{ text-align: center; }
			#table-rate .currency{ text-align: right; }
			#table-rate .change-amount{ border-left: 1px solid #ddd; text-align: left; width: 13em; }

			#header .tool > div { display: inline-block; vertical-align: middle; }
			#header .title { font-size: 1.2em; font-weight: bold; margin: 8px 10px 0; text-align: center; }

			.fromtwd, .totwd { width: 13em; }
	
			#footer {
				border-top: 2px dotted #ddd;
				padding: 0 10px 30px;
				text-align: center;
			}
			#footer > div {
				display: inline-block;
				margin: 3px 8px;
			}
		</style>
	</head>
	<body>
		<div id="header" class="well well-sm">
			<div class="row">
				<div class="col-sm-3">
					<div class="title">台灣銀行外幣匯率</div>
				</div>

				<div class="col-sm-9 tool">
					<div class="exchange-type">
						<button type="button" class="btn btn-default" data-type="fromtwd">台幣換外幣</button>
						<button type="button" class="btn btn-default" data-type="totwd">外幣換台幣</button>
					</div>

					<div class="fromtwd">
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">台幣</span>
							<input type="number" id="twd-amount" class="form-control" value='1000' />
						</div>
					</div>

					<div class="totwd">
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">外幣</span>
							<input type="number" id="foreign-amount" class="form-control" value='1' />
						</div>
					</div>

					<div class="exchange-kind">
						<select class="form-control" id="rate-type">
							<option value="sellCash">銀行現金賣出</option>
							<option value="sellSpot">銀行即期賣出</option>
							<option value="buyCash">銀行現金買入</option>
							<option value="buySpot">銀行即期買入</option>
						</select>
					</div>

					<div class="calculate">
						<button type="button" class="btn btn-primary">換算</button>
					</div>
				</div>
			</div>
		</div>
		
		<table id="table-rate" class="table table-striped table-hover">
			<thead>
				<tr>
					<th>幣值</th>
					<th>銀行現金買入</th>
					<th>銀行即期買入</th>
					<th>銀行現金賣出</th>
					<th>銀行即期賣出</th>
					<th>幣值換算</th>
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
		

		<div id="footer">
			<div>
				<span class="label label-default">資料來源</span>
				<a href="http://rate.bot.com.tw/Pages/Static/UIP003.zh-TW.htm">台灣銀行</a>
			</div>

			<div>
				<span class="label label-default">更新時間</span>
				<?php echo date('Y-m-d H:i:s', $data['updateTime']);?>
			</div>

			<div>
				<span class="label label-warning">免責聲明</span>
				幣值轉換僅供參考，實際請以銀行匯兌為準
			</div>
		</div>

		<a href="https://github.com/Aspertw/bot-rates"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>

		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script>
			$(".exchange-type button[data-type]").click(function(){
				var type = $(this).data('type');

				if( type == 'fromtwd'){
					$(".totwd").hide();
					$(".fromtwd").show();
				}
				if( type == 'totwd'){
					$(".fromtwd").hide();
					$(".totwd").show();
				}

				$(this).removeClass('btn-default').addClass('btn-success');
				$(this).siblings().removeClass('btn-success').addClass('btn-default');
			}).eq(0).click();

			$(".calculate button").click(function(){
				var type = $(".exchange-type button.btn-success").data('type');
				var amount = $("." + type + " input").val();
				var exchangeKind = $(".exchange-kind select").val();
				
				$("#table-rate tbody tr").each(function(){
					var rate = $(this).find("td." + exchangeKind).data('rate');
					var exchangeAmount = type == "totwd" ? (amount*rate) : (amount/rate);
					exchangeAmount = exchangeAmount.toFixed(3);

					var currency = type == "totwd" ? 'TWD' : $(this).find("td.currency").data('cur');

					var html = [
						'<span class="label label-info">',
							currency,
						'</span> ',
						exchangeAmount
					].join('');
					$(this).find(".change-amount").html(html);
				});
			});

			$("#twd-amount, #foreign-amount").change(function(e){
				$(".calculate button").click();
			});
		</script>
		<script>
		  // (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  // (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  // m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  // })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  // ga('create', 'UA-55384149-5', 'auto');
		  // ga('send', 'pageview');
		</script>
	</body>
</html>