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
	//--------------------------------------------------------------------

}
