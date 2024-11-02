<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dashboard</title>
</head>
<body>
	<center id="app">
		<h1>Dashboard</h1>
		<p><?=$bal?> pts</p>
		<!-- <a href="<?=base_url('writesms')?>">SMS</a> -->
		<br>
		<hr>

		<form action="<?= base_url('pin')?>" method="post">
			<span>For:</span><input type="number" name="phone"><br>
			<select name="network">
				<option value="">Network</option>
				<option value="1">MTN</option>
				<option value="2">GLO</option>
				<option value="3">AIRTEL</option>
				<!-- <option value="6">9Mobile</option> -->
			</select><br>
			<select name="plan" id="plan">
				<option value="">Data Plan</option>
				<option value="364">25MB</option>
				<option value="358">500MB</option>
				<option value="353">1GB</option>
				<option value="354">2GB</option>
				<option value="356">5GB</option>
				<option value="486">1GB Awoof</option>
				<option value="487">3.5GB Awoof</option>
				<option value="488">15GB Awoof</option>
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

		<br>
		<hr>
			<h3>PINS</h3>
			<table>
				<thead>
					<tr>
						<th>PIN</th>
						<th>Worth</th>
						<th>Used</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($pins as $key => $pin):?>
						<tr>
							<td onclick="printer('<?=$pin['pin']?>_<?=$pin['worth']?>')"> <?=$pin['pin']?> </td>
							<td> <?=$pin['worth']?> </td>
							<td> <?=$pin['used']?'Yes':'No'?> </td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<div id="printCanvas" style="width: 80mm;">
				<main>
				<center>
					<h1 onclick="sshot()">🐾 Paw Mobile</h1>
					<h2 id="pinn"></h2>
					<h3 id="worthh"></h3>
					<p onclick="pri()">To use pin, visit pawm.sgm.ng</p>
				</center></main>
				<script src="<?=base_url('assets/mmm.js')?>"></script>
				<script>
					function sshot() {
						html2canvas(document.querySelector('main')).then(canvas => {
							let w = document.querySelector('#worthh').innerText;
							let a = document.createElement("a");
							a.download = `PAWmobile ${w}.png`;
							a.href = canvas.toDataURL("image/png");
							a.click(); // MAY NOT ALWAYS WORK!
						});
					}
				</script>
				<script>
					function pri() {
						window.print();
						window.close();
					}
				</script>

			</div>
	</center>
	<script>
		function printer(pin) {
			
			let pins = pin.split('_')
			let pi = pins[0]
			let wor = pins[1].split(' ')
			let network = wor[0]
			let plan = wor[1]
			if(network == 1){
				network = "MTN";
			}else if(network == 2){
				network = "GLO";
			}else if(network == 3){
				network = "AIRTEL";
			}

			if(plan == 358 || plan == 331 || plan == 266){
				plan = "500MB";
			}else if(plan == 353 || plan == 334 || plan == 267){
				plan = "1GB";
			}else if(plan == 354 || plan == 332 || plan == 268){
				plan = "2GB";
			}else if(plan == 356 || plan == 329 || plan == 269){
				plan = "5GB";
			}else if(plan == 364 || plan == 264){
				plan = "25MB/100MB";
			}else if(plan == 486){
				plan = "1GB Awoof 24Hrs Validity";
			}else if(plan == 487){
				plan = "3.5GB Awoof 2Days Validity";
			}else if(plan == 488){
				plan = "15GB Awoof 7Days Validity";
			}
			console.log(pi, network, plan)
			document.querySelector('#pinn').innerText = pi
			document.querySelector('#worthh').innerText = `${network} ${plan}`
			PrintElem('printCanvas')
		}
		function PrintElem(elem)
		{
			var mywindow = window.open('', 'PRINT', 'width=80mm');

			mywindow.document.write('<html><head><title>' + document.title  + '</title>');
			mywindow.document.write('</head><body >');
			// mywindow.document.write('<h1>' + document.title  + '</h1>');
			mywindow.document.write(document.getElementById(elem).innerHTML);
			mywindow.document.write('</body></html>');

			mywindow.document.close(); // necessary for IE >= 10
			mywindow.focus(); // necessary for IE >= 10*/

			

			return true;
		}
		
	</script>
</body>
</html>