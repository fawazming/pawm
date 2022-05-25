<?php namespace App\Controllers;

class Home extends BaseController
{
	public function pro()
	{
		$incoming = $this->request->getPost();

		// $res = $this->rawCall('balance', ['token' => $_ENV['SABUSS']]);
		$res = $this->rawCall('buydata', $this->bbuilder($incoming));
		return view('response', ['msg' => $res]);
	}

	private function rawCall($uri, $data)
	{
		if(is_array($data)){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://samorabot.com/vtu/api/".$uri."/",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $data,
			));
			$result=curl_exec($curl);
			$this->postcall($result, $data);
			return $result;
		}else{
			return $data;
		}
	}

	private function postcall($result, $data)
	{
		if($result == 'Your transaction was successful'){
			$Pins = new \App\Models\Pins();
			$Log = new \App\Models\Log();

			$Log->insert([
				'pin' => $data['reference'],
				'phone' => $data['phone']
			]);
			$pinID = $Pins->where('pin',$data['reference'])->find()[0]['id'];
			$Pins->update($pinID, ['used'=>1]);
		}
	}

	public function bbuilder($incoming)
	{
		$phone = $incoming['phone'];
		$pin = $incoming['pin'];

		$Pins = new \App\Models\Pins();
		$pinDetails = $Pins->where('pin',$pin)->find();
		if(empty($pinDetails)){
			return 'Invalid Pin';
		}else{
			$pinDetails = $pinDetails[0];
			if($pinDetails['used']){
				return 'Pin has been used';
			}else{
				$Pins->update($pinDetails['id'], ['viewed'=>1]);
				$serviceid = explode(' ', $pinDetails['worth'])[0];
				$plan = explode(' ', $pinDetails['worth'])[1];

				$output = [
					'token' => $_ENV['SABUSS'],
					'plan' => $plan,
					'serviceid' => $serviceid,
					'phone' => $phone,
					'reference' => $pin
				];
				return $output;
			}
		}
	}	

	private function pgen()
	{
		$config         = new \Config\Encryption();
		$config->key    = $_ENV['encKey'];
		$config->driver = 'OpenSSL';

		$encrypter = \Config\Services::encrypter($config);

		$val1 = bin2hex($ciphertext = $encrypter->encrypt('This is a plain-text message!'));
		$val2 = substr($val1, 0, 6);
		return $this->numbequiv($val2);
	}


	private function numbequiv($string)
	{
		$kye = str_split($_ENV['kye']);
		$string = str_split($string);
		$out = '';

		foreach ($string as $ky => $ch) {
				$found = array_search($ch, $kye);
				$out = $out.$found;
		}
		return $out;
	}

	public function addPin()
	{
		// USE BALANCE TO GENERATE PIN
		$key = $this->request->getGet('key');
		if($key == $_ENV['adminKey']){
			$Pins = new \App\Models\Pins();
			$res = $Pins->insert([
				'pin' => $this->pgen(),
				'worth' => '1 6',
				'owner' => 'PAWM',
				'viewed' => 0,
				'used' => 0
			]);
			if($res){return view('response', ['msg' => 'New Pin Added']);}
		}else{
			return view('response', ['msg' => 'Leave here now!']);
		}
	}
	//--------------------------------------------------------------------//

	public function oga()
	{
		echo view('oga');
	}

	public function poga()
	{
		$incoming = $this->request->getPost();
		$login = [
			'uname' => $_ENV['uname'],
			'pword' => $_ENV['pword'],
		];
		if($incoming == $login){
			echo view('dashboard');
		}else{
			echo "Get out of here";
		}
	}


	public function genpin()
	{
		$Pins = new \App\Models\Pins();
		
		$incoming = $this->request->getPost();
		$error = '';
		$collated = [];

		if(empty($incoming['phone'])){
			$error = $error.' No Phone Number,';
		}
		if(($incoming['network']) == ''){
			$error = $error.' Select a network,';
		}
		if(empty($incoming['plan'])){
			$error = $error.' Select a data Plan,';
		}
		if(empty($incoming['quantity'])){
			$error = $error.' Select number of pin to gen';
		}
		
		$rounds = $incoming['quantity'];
		if(empty($error)){
			for ($i=0; $i < $rounds; $i++) { 
				$collated[$i] = [
					'pin' => $this->pgen(),
					'worth' => $incoming['network'].' '.$incoming['plan'],
					'owner' => $incoming['phone'],
					'viewed' => 1,
					'used' => 0
				];
			}
			$extractedPins = [];
			foreach ($collated as $ky => $col) {
			 	$extractedPins[$ky] = $col['pin'];
			 }

			$res = $Pins->insertBatch($collated);
			
			if($res){return view('generatedpins', [
				'pins' => $extractedPins, 
				'agent'=> $incoming['phone'], 
				'quantity' => $incoming['quantity'], 
				'network' => $incoming['network'],
				'plan' => $incoming['plan'],
			]);}

			// var_dump($extractedPins);

		}else{
			echo 'I cannot proceed because of error: <br> '. $error;
		}


	}

	public function sms()
	{
		$incoming = $this->request->getGet();
		$res = $this->sendsms($incoming['ph'], urldecode($incoming['sm'])) );
		if($res){
			echo "SMS sent to ".$incoming['ph'];
		}else{
			echo "Error while sending";
		}
	}

	public function sendsms($phone, $message)
	{
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://api.dojah.io/api/v1/messaging/sms",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\"sender_id\":\"khalifahSq\",\"destination\":\"".$phone ."\",\"channel\":\"sms\",\"message\":\"".$message."\"}",
		  CURLOPT_HTTPHEADER => [
		    "Accept: text/plain",
		    "AppId: ".$_ENV['AppId'],
		    "Authorization: "$_ENV['ProdKey'],
		    "Content-Type: application/json"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return false;
		  // return $err;
		} else {
		  // return $response;
			return true;
		}
	}

}
