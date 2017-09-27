<?

namespace Edu\Cnm\DataDesign\ApiTest;

/**
 * Test to insure that the new JWT implementation is bug free and efficient cross checking against the Tweet Api
 *
 *
 */

class JwtTweetApiTest extends DataDesignApiTest {
	/**
	 * Api endpoint to test against
	 */
	protected $postApiEndPoint = "https://bootcamp-coders.cnm.edu/~gkephart/ng4-bootcamp/public_html/api/tweet";

	/**
	 * helper method to create a valid object to send to the API
	 *
	 * @return object valid object created
	 */
	public function createValidObject() : object {
		return (object) ["tweetContent" => bin2hex(random_bytes(12))];
	}

	/**
	 * method to test get tweetByTweetId this will run through all of the test case for validateJwtToken.
	 */
	public function validGetTweetByTweetId() : void {

		//make a ajax call to the restEndpoint in order  to get a tweet by tweetId
		$reply = $this->guzzle->get($this->postApiEndPoint . "?id=35", ["headers" =>
			["X-XSRF-TOKEN" => $this->xsrfToken, "X-JWT-TOKEN" => $this->jwtToken]]
		);

		//decode the reply object for later use
		$replyObject = json_decode($reply->getBody());

		//enforce that the ajax call was successful and the headers are returned successfully
		$this->assertEquals($reply->getStatusCode(), 200);
		$this->assertEquals($replyObject->status, 200);
		$this->assertEquals($reply->getHeader("JWT-Token"), $this->jwtToken);
		$this->assertEquals($reply->getHeader("XSRF-TOKEN"), $this->xsrfToken);
	}
}