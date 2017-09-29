<?php

require_once dirname(__DIR__, 2) . "/vendor/autoload.php";
require_once dirname(__DIR__) . "/lib/uuid.php";

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
	$signature = generateUuidV4();

	//store the signature in its string version
	$_SESSION["signature"] = $signature->toString();

	$token = (new Builder())
		->set($value, $content)
		->setIssuer("https://bootcamp-coders.cnm.edu")
		->setAudience("https://bootcamp-coders.cnm.edu")
		->setId(session_id())
		->setIssuedAt(time())
		->setExpiration(time() + 3600)
		->sign($signer, $signature->toString())
		->getToken();

	// add the JWT to the header
	setcookie("JWT-TOKEN", $token->getPayload(), 0, "/");


	$_SESSION["JWT-TOKEN"] = $token->getPayload();

}

function verifyAuthSession(): void {

	//if  the JWT does not exist in the cookie jar throw an exception
	$headers = array_change_key_case(apache_request_headers(), CASE_UPPER);
	if(array_key_exists("X-JWT-TOKEN", $headers) === false) {
		throw(new InvalidArgumentException("invalid JWT token", 401));
	}

	//grab the string representation of the Token from the header then parse it into an object
	$headerJwt = $headers["X-JWT-TOKEN"];
	$headerJwt = (new Parser())->parse($headerJwt);

	//grab the string representation of the Token from the session then parse it into an object
	$sessionJWT = $_SESSION["JWT-TOKEN"];
	$sessionJWT= (new Parser())->parse($sessionJWT);

	if ($sessionJWT->getClaims() !== $headerJwt->getClaims())

	//enforce the JWT is valid
	$validator = new ValidationData();
	$validator->setId(session_id());
	if($headerJwt->validate($validator) !== true) {
		throw (new InvalidArgumentException("not authorized to preform task", 402));
	}

	//verify that the JWT was signed by the server
	$signer = new Sha512();

	if($headerJwt->verify($signer, $_SESSION["signature"]) !== true) {
		throw (new InvalidArgumentException("not authorized to preform task", 403));
	}


}