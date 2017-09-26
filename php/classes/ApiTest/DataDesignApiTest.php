<?php

namespace Edu\Cnm\DataDesign\ApiTest;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJarInterface;
use function GuzzleHttp\Psr7\str;
use PHPUnit\Framework\TestCase;

require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

require_once(dirname(__DIR__, 3) . "/vendor/autoload.php");

abstract class DataDesignApiTest extends TestCase {

	/**
	 * cookie jar for guzzle
	 * @var CookieJarInterface $cookieJar
	 **/
	protected $cookieJar = null;
	/**
	 * guzzle HTTP client
	 * @var Client $guzzle
	 **/
	protected $guzzle = null;
	/**
	 * XSRF token for non-GET requests
	 * @var string $xsrfToken
	 **/
	protected $xsrfToken = "";

	/**
	 * JWT token used for authentication
	 * @var string JWT
	 */
	protected $jwtToken = "";

	public function signIn() {

		$requestObject = (object) ["profileEmail" => "email@email.com", "profilePassword" => "password"];

		$this->assertNotEmpty($this->xsrfToken);
		$this->guzzle->post(
			"https://bootcamp-coders.cnm.edu/~gkephart/ng4-bootcamp/public_html/api/sign-in/",
			["body" => json_encode($requestObject),
				"headers" =>["X-XSRF-TOKEN" => $this->xsrfToken]]
		);

	}

	/**
	 * setup method for testing my implementation of JWT.
	 */
	public function setUp() {

		// get an XSRF token by visiting the main site
		$this->guzzle = new Client(["cookies" => true]);
		$this->guzzle->get("https://bootcamp-coders.cnm.edu/");

		//get the XSRF token andJWT and put it into the cookie jar
		$this->cookieJar = $this->guzzle->getConfig("cookies");
		$cookieArray = $this->cookieJar->toArray();

		foreach($cookieArray as $cookie) {
			if(strcasecmp($cookie["Name"], "XSRF-TOKEN") === 0) {
				$this->xsrfToken = $cookie["Value"];
				break;
			}

			$this->signIn();

			foreach($cookieArray as $token) {
				if(strcasecmp($token["Name"], "XSRF-TOKEN") === 0) {
					$this->xsrfToken = $token["Value"];
					break;
				}
			}
		}
	}

	/**
	 * tear down method to end the session
	 */
	public final function tearDown() {
		$this->guzzle->get("https://bootcamp-coders.cnm.edu/~gkephart/ng4-bootcamp/public_html/api/sign-out/");
	}



}