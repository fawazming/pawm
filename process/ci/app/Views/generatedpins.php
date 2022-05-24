<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PAWM</title>
</head>
<body>
	<h4><center><?= $quantity ?> Pins for <?= $agent ?> </center></h4>
	<p>
	<?php
		switch ($network) {
			case '1':
				echo 'MTN';
				break;
		}
	?>

	<?php
		switch ($plan) {
			case '1':
				echo '1GB';
				break;
			case '2':
				echo '2GB';
				break;
			case '5':
				echo '5GB';
				break;
			case '6':
				echo '500MB';
				break;
		}
	?>	
	</p>
	<?php foreach ($pins as $pin):?>
	<p><?=$pin?></p>
	<?php endforeach; ?>
	<small>Powered by Rayyan Technologies</small>
</body>
</html>