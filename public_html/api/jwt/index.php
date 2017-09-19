<?php

use Lcobucci\JWT\{
	Builder,
	Parser,
	ValidationData,
	Signer\Hmac\Sha256

};

require_once dirname(__DIR__, 3) . "/php/lib/xsrf.php";
require_once dirname(__DIR__,3) ."/vendor/autoload.php";

//verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}


$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	//verify the HTTP method being used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	// if the HTTP method is head check/start the  php session and set the XSRF token
	if($method === "GET") {

		setXsrfCookie();

		$signer = new Sha256();
		$signer2 = new Sha256();

		$newToken = createAuthToken($signer);
		var_dump($newToken->verify($signer,'work'));

		/**
		 *
		 * wtf... $signer creates the same key pair... wat..
		 *
		 * $signer2 = new Sha256();
		 * $newToken = createAuthToken($signer);
		 *
		 * var_dump($newToken->verify($signer2,'work')); // true
		 */


		//var_dump($newToken->getHeaders());
		//var_dump($newToken->getClaims());

	} else {
		throw (new \InvalidArgumentException("attempting to brew coffee with a teapot", 418));
	}
} catch(\Exception  | \TypeError $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
}
//echo json_encode($reply);