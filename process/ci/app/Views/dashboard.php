<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dashboard</title>
</head>
<body>
	<center>
		<h1>Dashboard</h1>
		<p><?=$bal?> pts</p>
		<a href="<?=base_url('writesms')?>">SMS</a>
		<br>
		<hr>

		<form action="<?= base_url('pin')?>" method="post">
			<span>For:</span><input type="number" name="phone"><br>
			<select name="network">
				<option value="">Network</option>
				<option value="1">MTN</option>
			</select><br>
			<select name="plan">
				<option value="">Data Plan</option>
				<option value="1">1GB</option>
				<option value="2">2GB</option>
				<option value="4">5GB</option>
				<option value="6">500MB</option>
			</select><br>
			<select name="quantity">
				<option value="">Quantity</option>
				<?php if($mod == 1): ?>
				<option value="2">2</option>
				<?php endif; ?>
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="20">20</option>
			</select><br>
			<input type="submit" value="Generate">
		</form>
	</center>

</body>
</html>