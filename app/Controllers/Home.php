<?php namespace App\Controllers;

class Home extends BaseController
{
	function index() {
		echo view('ind');
	}

	public function info()
	{
		echo view('info');
	}

	public function pro()
	{
		$incoming = $this->request->getPost();

		// $res = $this->rawCall('balance', ['token' => $_ENV['SABUSS']]);
		$res = $this->rawCall('data', $this->bbuilder($incoming));
		return view('response', ['msg' => $res]);
	}

	private function rawCall($uri, $data)
	{
		if(is_array($data)){
			// $headers = ["Authorization: Token ".$_ENV['SABUSS']];
			// $data = '{"network":1,"mobile_number":"07061811568","plan":364,"Ported_number":true,"reference":"513867"}';
		$curl= curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => "https://www.gladtidingsdata.com/api/".$uri."/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\n  \"network\": \"". $data['network'] ."\",\n  \"mobile_number\": \"". $data['mobile_number'] ."\",\n  \"plan\": \"". $data['plan'] ."\",\n  \"payment_medium\": \"MAIN WALLET\",\n  \"Ported_number\": false\n}",
			CURLOPT_HTTPHEADER => [
			  "Accept: */*",
			  "Authorization: Token ".$_ENV['SABUSS'],
			  "Content-Type: application/json",
			  "User-Agent: Thunder Client (https://www.thunderclient.com)"
			],
		  ]);
			$result=curl_exec($curl);
			// dd($result);
		  $res = json_decode($result)->Status;
			$this->postcall($res, $data);
			return $res;
		}else{
			return $data;
		}
	}

	private function balance()
	{

		// $data = array("token" => $_ENV['SABUSS']);
		$headers = ["Authorization: Token ".$_ENV['SABUSS'], "Content-Type: application/x-www-form-urlencoded"];
		$curl= curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://www.gladtidingsdata.com/api/user/",
			CURLOPT_HTTPHEADER => $headers,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		//   CURLOPT_POSTFIELDS => $data,
		));
		$result=curl_exec($curl);
		return $result;
	}

	private function postcall($result, $data)
	{
		if($result == 'successful'){
			$Pins = new \App\Models\Pins();
			$Log = new \App\Models\Log();

			$Log->insert([
				'pin' => $data['reference'],
				'phone' => $data['mobile_number']
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
					'network' => $serviceid,
					'mobile_number' => $phone,
					'plan' => $plan,
					'Ported_number' => false,
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

		$val1 = bin2hex($ciphertext = $encrypter->encrypt('oiuytresd guu jhkhodrs fsfhyjgu dfgkuyutytrare'));
		$val2 = substr($val1, 2, 8);
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
		$Pins = new \App\Models\Pins();
		$incoming = $this->request->getPost();
		$login = [
			'uname' => $_ENV['uname'],
			'pword' => $_ENV['pword'],
		];
		$pins = $Pins->orderBy('created_at', 'DESC')->findall();
		if($incoming == $login){
			// dd($this->balance());
			$balance = json_decode($this->balance())->user->Account_Balance;
			// $balance = 0;
			echo view('dashboard', ['bal' => $balance, 'mod' => $_ENV['mod'], 'pins' => $pins]);
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
		
		if($incoming['network'] == 2){
			switch ($incoming['plan']) {
				case '358':
					$incoming['plan'] = 331;
					break;
				case '353':
					$incoming['plan'] = 334;
					break;
				case '354':
					$incoming['plan'] = 332;
					break;
				case '356':
					$incoming['plan'] = 329;
					break;
				default:
					$incoming['plan'] = 0;
					break;
			}
		}elseif ($incoming['network'] == 3) {
			switch ($incoming['plan']) {
				case '364':
					$incoming['plan'] = 264;
					break;
				case '358':
					$incoming['plan'] = 266;
					break;
				case '353':
					$incoming['plan'] = 267;
					break;
				case '354':
					$incoming['plan'] = 268;
					break;
				case '356':
					$incoming['plan'] = 269;
					break;
				default:
					$incoming['plan'] = 0;
					break;
			}
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

	public function writesms()
	{
		echo view('writesms');
	}

	public function sms()
	{
		$incoming = $this->request->getGet();
		$res = $this->sendsms($incoming['ph'], $incoming['sm']);
		if($res){
			// echo "SMS sent to ".$incoming['ph'];
			var_dump($res);
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
		  CURLOPT_POSTFIELDS => "{\"sender_id\":\"PawmNG\",\"destination\":\"".$phone ."\",\"channel\":\"sms\",\"message\":\"".$message."\"}",
		  CURLOPT_HTTPHEADER => [
		    "Accept: text/plain",
		    "AppId: ".$_ENV['AppId'],
		    "Authorization: ".$_ENV['ProdKey'],
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
		  return $response;
			// return true;
		}
	}

}
