<?php

namespace Edu\Cnm\DataDesign\ApiTest;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJarInterface;
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
	 * JWT token purpose of tests
	 */
	public function setUp() {

		// get an XSRF token by visiting the main site
		$this->guzzle = new Client(["cookies" => true]);
		$this->guzzle->get("https://bootcamp-coders.cnm.edu/");

		// get the XSRF cookie - this can be simplified once my pull request is published in Guzzle 6.3
		// @see https://github.com/guzzle/guzzle/pull/1318
		$this->cookieJar = $this->guzzle->getConfig("cookies");
		$cookieArray = $this->cookieJar->toArray();
		foreach($cookieArray as $cookie) {
			if(strcasecmp($cookie["Name"], "XSRF-TOKEN") === 0) {
				$this->xsrfToken = $cookie["Value"];
				break;
			}
		}
	}

}