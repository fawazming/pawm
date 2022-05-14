<?php namespace App\Controllers;

class Home extends BaseController
{
	public function pro()
	{
		$incoming = $this->request->getPost();
		$this->call();
		return view('response', ['msg' => $incoming['pin'].' '.$incoming['phone']]);
	}

	private function call()
	{
		$options = [
		        'baseURI' => 'https://amityrechargeco.com/api/',
		        'timeout'  => 4,
		        'headers' => [
	                'Authorization' => 'Token  '.$_ENV['TOKEN'],
	                'Content-Type'     => 'application/json',
	            ],
		];
		$client = \Config\Services::curlrequest($options);
		$response = $client->get('data');

		// Response
		$code   = $response->getStatusCode();    // 200
		$header_contentType   = $response->getHeaderLine('Content-Type');
		$header_auth   = $response->getHeaderLine('Authorization');
		$body   = $response->getBody();
		$reason = $response->getReason();

		var_dump($body, $code, $reason, $header_contentType, $header_auth, $_ENV['TOKEN']);
	}

	//--------------------------------------------------------------------

}
