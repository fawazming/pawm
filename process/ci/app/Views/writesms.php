<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Compose SMS</title>
</head>
<body>
	<h3>Compose SMS</h3>
	<form action="<?=base_url('sms')?>" method="get">
		<input type="tel" name="ph"> <br><br>
		<textarea name="sm"></textarea><br>
		<input type="submit" value="Send">
	</form>
</body>
</html>