<?php
require_once dirname(__DIR__ , 2) . "/vendor/autoload.php";

use Lcobucci\JWT\{
	Builder,
	Signer\Hmac\Sha512,
	Parser,
	ValidationData
};

/**
 * this if block exists because apache_request_headers() is not portable across web servers
 * this will clone apache_request_headers()'s functionality if the web server doesn't support apache_request_headers()
 * (e.g., non Apache web servers, Apache web servers with apache_request_headers() disabled)
 *
 * @see http://php.net/manual/en/function.apache-request-headers.php apache_request_headers()
 **/


if(function_exists("apache_request_headers") === false) {
	/**
	 * clones apache_request_headers()'s behavior
	 *
	 * @return array all HTTP request headers
	 **/
	function apache_request_headers() {
		$headers = array();
		foreach($_SERVER as $header => $value) {
			// divide the header name by the underbar
			$headerNameArray = explode("_" , $header);
			// request headers always are prefixed by HTTP_
			if(array_shift($headerNameArray) === "HTTP") {
				// convert HTTP_FOO_HEADER to Foo-Header
				array_walk($headerNameArray, function(&$headerName) {
					$headerName = ucfirst(strtolower($headerName));
				});
				$headers[join("-", $headerNameArray)] = $value;
			}
		}
		return($headers);
	}
}
/**
 * sets an XSRF cookie, generating one if necessary
 *
 *
 * @throws RuntimeException if the session is not active
 **/
function setXsrfCookie() {


	//
	$cookiePath = "/";

	// enforce that the session is active
	if(session_status() !== PHP_SESSION_ACTIVE) {
		throw(new RuntimeException("session not active"));
	}
	// if the token does not exist, create one and send it in a cookie
	if(empty($_SESSION["XSRF-TOKEN"]) === true) {
		$_SESSION["XSRF-TOKEN"] = hash("sha512", session_id() . bin2hex(openssl_random_pseudo_bytes(16)));
	}
	setcookie("XSRF-TOKEN", $_SESSION["XSRF-TOKEN"], 0, $cookiePath);
}
/**
 * verifies the X-XSRF-TOKEN sent by Angular matches the XSRF-TOKEN saved in this session.
 * This function returns nothing, but will throw an exception when something does not match
 *
 * @see https://code.angularjs.org/1.4.2/docs/api/ng/service/$http Angular $http service
 * @throws InvalidArgumentException when tokens do not match
 * @throws RuntimeException if the session is not active
 **/
function verifyXsrf() {
	// enforce that the session is active
	if(session_status() !== PHP_SESSION_ACTIVE) {
		throw(new RuntimeException("session not active"));
	}
	// grab the XSRF token sent by Angular, jQuery, or JavaScript in the header
	$headers = array_change_key_case(apache_request_headers(), CASE_UPPER);
	if(array_key_exists("X-XSRF-TOKEN", $headers) === false) {
		throw(new InvalidArgumentException("invalid XSRF token", 401));
	}
	$angularHeader = $headers["X-XSRF-TOKEN"];
	// compare the XSRF token from the header with the correct token in the session
	$correctHeader = $_SESSION["XSRF-TOKEN"];
	if($angularHeader !== $correctHeader) {
		throw(new InvalidArgumentException("invalid XSRF token", 401));
	}


}

function setJwtAndAuthHeader(string $value, $content ) :void {

	//enforce that the session is active
	if(session_status() !== PHP_SESSION_ACTIVE) {
		throw(new RuntimeException("session not active"));
	}

	$signer = new Sha512();

	//create a weak salt for the cookie.
	$id =bin2hex(random_bytes(16));

	$token = (new Builder())
		->set($value, $content)
		->setIssuer("https://bootcamp-coders.cnm.edu")
		->setAudience("https://bootcamp-coders.cnm.edu")
		->setId($id)
		->setIssuedAt(time())
		->setExpiration(time() + 3600)
		->sign($signer, session_Id());

	$_SESSION["JWT"] = $token->getToken();

	//declare a path for the cookie mmm
	$cookiePath = "/";

	setcookie("JWT", $token->getToken(), 0, $cookiePath );
}

function validateAuthSession() : void {

	if(empty($_COOKIE["JWT"]) === true) {
		throw (new InvalidArgumentException("not authorized to preform task1"));
	}

	$jwt  = $_COOKIE["JWT"];

	var_dump($jwt);

	$parsedJwt = (new Parser())->parse($jwt);



	if($parsedJwt !== $_SESSION["JWT"]){
		throw (new InvalidArgumentException("not authorized to preform task2"));
	}



}