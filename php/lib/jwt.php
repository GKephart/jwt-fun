<?php
require_once dirname(__DIR__, 2) . "/vendor/autoload.php";

use Lcobucci\JWT\{
	Builder, Signer\Hmac\Sha512, Parser, ValidationData
};
use Ramsey\Uuid\{
	Uuid
};


function setJwtAndAuthHeader(string $value, $content): void {

//enforce that the session is active
	if(session_status() !== PHP_SESSION_ACTIVE) {
		throw(new RuntimeException("session not active"));
	}

// create the signer object
	$signer = new Sha512();

//create a UUID to sign the JWT and then store it in the session
	$signature = Uuid::uuid1();
	$_SESSION["signature"] = $signature;

	$token = (new Builder())
		->set($value, $content)
		->setIssuer("https://bootcamp-coders.cnm.edu")
		->setAudience("https://bootcamp-coders.cnm.edu")
		->setId(session_id())
		->setIssuedAt(time())
		->setExpiration(time() + 3600)
		->sign($signer, $signature);

//store the JWT in the session for verification
	$_SESSION["JWT"] = $token;

// add the JWT to the header
	setcookie("JWT", $token->getToken(), 0, "/");
}

function validateAuthSession(): void {

//if  the JWT does not exist in the cookie jar throw an exception
	$headers = array_change_key_case(apache_request_headers(), CASE_UPPER);
	if(array_key_exists("JWT", $headers) === false) {
		throw(new InvalidArgumentException("invalid JWT token", 401));
	}

//grab the string representation of the Token
	$jwt = $_COOKIE["JWT"];

// parse the string representation of the JWT back into an object
	$parsedJwt = (new Parser())->parse($jwt);

	//enforce the JWT is valid
	$validator = new ValidationData();
	$validator->setId(session_id());
	$validJwt = $parsedJwt;
	if($validJwt->validate($validator) !== true) {
		throw (new InvalidArgumentException("not authorized to preform task",403));
	}

//verify that the JWT was signed by the server
	$signer = new Sha512();
	$verifyJwt = $parsedJwt;
	if($verifyJwt->verify($signer, $_SESSION["signature"]) !== true) {
		throw (new InvalidArgumentException("not authorized to preform task", 400));
	}

//if the JWT in the session does not match the JWT hit the dead mans switch
	if($parsedJwt !== $_SESSION["JWT"]) {
		unset($_COOKIE["XSRF-TOKEN"], $_COOKIE["JWT"]);
		$_SESSION = [];
		throw (new InvalidArgumentException("please log in again"));
	}
}