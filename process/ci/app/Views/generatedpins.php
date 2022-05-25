<?php
function network($network)
{
	switch ($network) {
		case '1':
			return 'MTN';
			break;
	}
}

function plan($plan)
{
	switch ($plan) {
		case '1':
			return '1GB';
			break;
		case '2':
			return '2GB';
			break;
		case '5':
			return '5GB';
			break;
		case '6':
			return '500MB';
			break;
	}
}
	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PAWM</title>
</head>
<body style="font-size: 1.5rem;">
	<h4><center><?= $quantity ?> Pins for <?= $agent ?> </center></h4>
	<p>
		<?= network($network)?>	
		<?= plan($plan)?>	
	</p>
	<?php $pns = ''; ?>
	<?php foreach ($pins as $pin):?>
		<?php $pns = $pns.'%20'.$pin; ?>
	<p><?=$pin?></p>
	<?php endforeach; ?>
	<a href='<?php echo base_url("sms?ph=".$agent."&sm=".network($network)."%20".plan($plan).$pns)?>'>SMS</a>
	<small>Powered by Rayyan Technologies</small>
</body>
</html>