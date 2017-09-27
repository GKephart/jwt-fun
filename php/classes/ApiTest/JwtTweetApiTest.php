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


}