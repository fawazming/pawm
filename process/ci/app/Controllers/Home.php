<?php namespace App\Controllers;

class Home extends BaseController
{
	public function pro()
	{
		$incoming = $this->request->getPost();
		var_dump($this->call($this->bbuilder($incoming)));
		return view('response', ['msg' => $incoming['pin'].' '.$incoming['phone']]);
	}

	public function bbuilder($incoming)
	{
		$phone = $incoming['phone'];
		$pin = $incoming['pin'];

		$output = ["network"=>"1",
		"mobile_number"=>$phone,
		"plan" => "216",
		"Ported_number" => false];

		return $output;
	}

	private function call($body)
	{
		$options = [
		        'baseURI' => 'https://www.amityrechargeco.com/api/',
		        'timeout'  => 4,
		        'headers' => [
	                'Authorization' => $_ENV['TOKEN'],
	                'Content-Type'     => 'application/json',
	            ],
		];
		// {"network": "1",
		// "mobile_number": "08108097322",
		// "plan": "216",
		// "Ported_number": false}
		$client = \Config\Services::curlrequest($options);
		$response = $client->request('POST', 'data', ['body' => json_encode($body)]);

		// $response = [];
		// switch ($method) {
		// 	case 'get':
		// 		$response = $client->get($uri);
		// 		break;
		// 	case 'post':
		// 		$response = $client->request('POST', $uri, ['json' => $body]);
		// 		break;
		// 	default:
		// 		break;
		// }

		// Response
		$code   = $response->getStatusCode();    // 200
		// $header_contentType   = $response->getHeaderLine('Content-Type');
		// $header_auth   = $response->getHeaderLine('Authorization');
		$body   = $response->getBody();
		$reason = $response->getReason();

		// var_dump($response);
		return ['code'=>$code, 'body'=>$body, 'reason'=>$reason];
	}

	//--------------------------------------------------------------------

}
