<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Facebook
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

/**
 * Test class for JFacebookNote.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Facebook
 * @since       3.2.0
 */
class JFacebookNoteTest extends TestCase
{
	/**
	 * @var    Registry  Options for the Facebook object.
	 * @since  3.2.0
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock client object.
	 * @since  3.2.0
	 */
	protected $client;

	/**
	 * @var    JFacebookNote  Object under test.
	 * @since  3.2.0
	 */
	protected $object;

	/**
	 * @var    JFacebookOauth  Facebook OAuth 2 client
	 * @since  3.2.0
	 */
	protected $oauth;

	/**
	 * @var    string  Sample JSON string.
	 * @since  3.2.0
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  3.2.0
	 */
	protected $errorString = '{"error": {"message": "Generic Error."}}';

	/**
	 * Backup of the SERVER superglobal
	 *
	 * @var  array
	 */
	protected $backupServer;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	protected function setUp()
	{
		$this->backupServer = $_SERVER;
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$app_id = "app_id";
		$app_secret = "app_secret";
		$my_url = "http://localhost/gsoc/joomla-platform/facebook_test.php";
		$access_token = array(
			'access_token' => 'token',
			'expires' => '51837673', 'created' => '2443672521');

		$this->options = new Registry;
		$this->client = $this->getMockBuilder('JHttp')->setMethods(array('get', 'post', 'delete', 'put'))->getMock();
		$this->input = new JInput;
		$this->oauth = new JFacebookOauth($this->options, $this->client, $this->input);
		$this->oauth->setToken($access_token);

		$this->object = new JFacebookNote($this->options, $this->client, $this->oauth);

		$this->options->set('clientid', $app_id);
		$this->options->set('clientsecret', $app_secret);
		$this->options->set('redirecturi', $my_url);
		$this->options->set('sendheaders', true);
		$this->options->set('authmethod', 'get');

		parent::setUp();
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     \PHPUnit\Framework\TestCase::tearDown()
	 * @since   3.6
	 */
	protected function tearDown()
	{
		$_SERVER = $this->backupServer;
		unset($this->backupServer, $this->options, $this->client, $this->input, $this->oauth, $this->object);
		parent::tearDown();
	}

	/**
	 * Tests the getNote method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testGetNote()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
		->method('get')
		->with($note . '?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getNote($note),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getNote method - failure
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  RuntimeException
	 */
	public function testGetNoteFailure()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 401;
		$returnData->body = $this->errorString;

		$this->client->expects($this->once())
		->method('get')
		->with($note . '?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->object->getNote($note);
	}

	/**
	 * Tests the getComments method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testGetComments()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
		->method('get')
		->with($note . '/comments?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getComments($note),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getComments method - failure
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  RuntimeException
	 */
	public function testGetCommentsFailure()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 401;
		$returnData->body = $this->errorString;

		$this->client->expects($this->once())
		->method('get')
		->with($note . '/comments?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->object->getComments($note);
	}

	/**
	 * Tests the createComment method.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testCreateComment()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';
		$message = 'test message';

		// Set POST request parameters.
		$data = array();
		$data['message'] = $message;

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
		->method('post')
		->with($note . '/comments?access_token=' . $token['access_token'], $data)
		->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->createComment($note, $message),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the createComment method - failure.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  RuntimeException
	 */
	public function testCreateCommentFailure()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';
		$message = 'test message';

		// Set POST request parameters.
		$data = array();
		$data['message'] = $message;

		$returnData = new stdClass;
		$returnData->code = 401;
		$returnData->body = $this->errorString;

		$this->client->expects($this->once())
		->method('post')
		->with($note . '/comments?access_token=' . $token['access_token'], $data)
		->will($this->returnValue($returnData));

		$this->object->createComment($note, $message);
	}

	/**
	 * Tests the deleteComment method.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testDeleteComment()
	{
		$token = $this->oauth->getToken();
		$comment = '5148941614_12343468';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = true;

		$this->client->expects($this->once())
		->method('delete')
		->with($comment . '?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->deleteComment($comment),
			$this->equalTo(true)
		);
	}

	/**
	 * Tests the deleteComment method - failure.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  RuntimeException
	 */
	public function testDeleteCommentFailure()
	{
		$token = $this->oauth->getToken();
		$comment = '5148941614_12343468';

		$returnData = new stdClass;
		$returnData->code = 401;
		$returnData->body = $this->errorString;

		$this->client->expects($this->once())
		->method('delete')
		->with($comment . '?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->object->deleteComment($comment);
	}

	/**
	 * Tests the getLikes method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testGetLikes()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
		->method('get')
		->with($note . '/likes?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->getLikes($note),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getLikes method - failure
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  RuntimeException
	 */
	public function testGetLikesFailure()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 401;
		$returnData->body = $this->errorString;

		$this->client->expects($this->once())
		->method('get')
		->with($note . '/likes?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->object->getLikes($note);
	}

	/**
	 * Tests the createLike method.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testCreateLike()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		$this->client->expects($this->once())
		->method('post')
		->with($note . '/likes?access_token=' . $token['access_token'], '')
		->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->createLike($note),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the createLike method - failure.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  RuntimeException
	 */
	public function testCreateLikeFailure()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 401;
		$returnData->body = $this->errorString;

		$this->client->expects($this->once())
		->method('post')
		->with($note . '/likes?access_token=' . $token['access_token'], '')
		->will($this->returnValue($returnData));

		$this->object->createLike($note);
	}

	/**
	 * Tests the deleteLike method.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testDeleteLike()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = true;

		$this->client->expects($this->once())
		->method('delete')
		->with($note . '/likes?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->assertThat(
			$this->object->deleteLike($note),
			$this->equalTo(true)
		);
	}

	/**
	 * Tests the deleteLike method - failure.
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  RuntimeException
	 */
	public function testDeleteLikeFailure()
	{
		$token = $this->oauth->getToken();
		$note = '124346363456';

		$returnData = new stdClass;
		$returnData->code = 401;
		$returnData->body = $this->errorString;

		$this->client->expects($this->once())
		->method('delete')
		->with($note . '/likes?access_token=' . $token['access_token'])
		->will($this->returnValue($returnData));

		$this->object->deleteLike($note);
	}
}
