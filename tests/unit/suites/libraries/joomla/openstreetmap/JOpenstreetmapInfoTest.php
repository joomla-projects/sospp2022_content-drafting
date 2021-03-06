<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JOpenstreetmapInfo.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Openstreetmap
 *
 * @since       3.2.0
 */
class JOpenstreetmapInfoTest extends TestCase
{
	/**
	 * @var    JRegistry  Options for the Openstreetmap object.
	 * @since  3.2.0
	 */
	protected $options;

	/**
	 * @var    JHttp  Mock client object.
	 * @since  3.2.0
	 */
	protected $client;

	/**
	 * @var    JInput The input object to use in retrieving GET/POST data.
	 * @since  3.2.0
	 */
	protected $input;

	/**
	 * @var    JOpenstreetmapInfo Object under test.
	 * @since  3.2.0
	 */
	protected $object;

	/**
	 * @var    JOpenstreetmapOauth  Authentication object for the Openstreetmap object.
	 * @since  3.2.0
	 */
	protected $oauth;

	/**
	 * @var    string  Sample XML.
	 * @since  3.2.0
	 */
	protected $sampleXml = <<<XML
<?xml version='1.0'?>
<osm></osm>
XML;

	/**
	 * @var    string  Sample XML error message.
	* @since  3.2.0
	*/
	protected $errorString = <<<XML
<?xml version='1.0'?>
<osm>ERROR</osm>
XML;

	/**
	 * Backup of the SERVER superglobal
	 *
	 * @var  array
	 * @since  3.6
	 */
	protected $backupServer;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	* This method is called before a test is executed.
	*
	* @access protected
	*
	* @return void
	*/
	protected function setUp()
	{
		$this->backupServer = $_SERVER;
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$key = "app_key";
		$secret = "app_secret";

		$access_token = array('key' => 'token_key', 'secret' => 'token_secret');

		$this->options = new JRegistry;
		$this->input = new JInput;
		$this->client = $this->getMockBuilder('JHttp')->setMethods(array('get', 'post', 'delete', 'put'))->getMock();
		$this->oauth = new JOpenstreetmapOauth($this->options, $this->client, $this->input);
		$this->oauth->setToken($access_token);

		$this->object = new JOpenstreetmapInfo($this->options, $this->client, $this->oauth);

		$this->options->set('consumer_key', $key);
		$this->options->set('consumer_secret', $secret);
		$this->options->set('sendheaders', true);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 *
	 * @see     \PHPUnit\Framework\TestCase::tearDown()
	 * @since   3.6
	 */
	protected function tearDown()
	{
		$_SERVER = $this->backupServer;
		unset($this->backupServer, $this->options, $this->input, $this->client, $this->oauth, $this->object);
	}

	/**
	 * Tests the getCapabilities method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testGetCapabilities()
	{
		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleXml;

		$path = 'capabilities';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->getCapabilities(),
				$this->equalTo(new SimpleXMLElement($this->sampleXml))
		);
	}

	/**
	 * Tests the getCapabilities method - failure
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException DomainException
	 */
	public function testGetCapabilitiesFailure()
	{
		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'capabilities';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->getCapabilities();
	}

	/**
	 * Tests the retrieveMapData method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testRetrieveMapData()
	{
		$left = '1';
		$bottom = '1';
		$right = '2';
		$top = '2';

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleXml;

		$path = 'map?bbox=' . $left . ',' . $bottom . ',' . $right . ',' . $top;

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->retrieveMapData($left, $bottom, $right, $top),
				$this->equalTo(new SimpleXMLElement($this->sampleXml))
		);
	}

	/**
	 * Tests the retrieveMapData method - failure
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException DomainException
	 */
	public function testRetrieveMapDataFailure()
	{
		$left = '1';
		$bottom = '1';
		$right = '2';
		$top = '2';

		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'map?bbox=' . $left . ',' . $bottom . ',' . $right . ',' . $top;

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->retrieveMapData($left, $bottom, $right, $top);
	}

	/**
	 * Tests the retrievePermissions method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testRetrievePermissions()
	{

		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleXml;

		$path = 'permissions';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->assertThat(
				$this->object->retrievePermissions(),
				$this->equalTo(new SimpleXMLElement($this->sampleXml))
		);
	}

	/**
	 * Tests the retrievePermissions method - failure
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException DomainException
	 */
	public function testRetrievePermissionsFailure()
	{

		$returnData = new stdClass;
		$returnData->code = 500;
		$returnData->body = $this->errorString;

		$path = 'permissions';

		$this->client->expects($this->once())
		->method('get')
		->with($path)
		->will($this->returnValue($returnData));

		$this->object->retrievePermissions();
	}
}
