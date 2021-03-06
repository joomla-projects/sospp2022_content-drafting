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
 * Test class for JFacebook.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Facebook
 * @since       3.2.0
 */
class JFacebookTest extends TestCase
{
	/**
	 * @var    Registry  Options for the Facebook object.
	 * @since  3.2.0
	 */
	protected $options;

	/**
	 * @var    JHttp  The HTTP client object to use in sending HTTP requests.
	 * @since  3.2.0
	 */
	protected $client;

	/**
	* @var    JFacebook  Object under test.
	* @since  3.2.0
	*/
	protected $object;

	/**
	 * @var    JFacebookOAuth  Facebook OAuth 2 client
	 * @since  3.2.0
	 */
	protected $oauth;

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

		$this->options = new Registry;
		$this->client = $this->getMockBuilder('JHttp')->setMethods(array('get', 'post', 'delete', 'put'))->getMock();

		$this->object = new JFacebook($this->oauth, $this->options, $this->client);

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
		unset($this->backupServer, $this->options, $this->client, $this->object);
		parent::tearDown();
	}

	/**
	 * Tests the magic __get method - user
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetUser()
	{
		$this->assertThat(
			$this->object->user,
			$this->isInstanceOf('JFacebookUser')
		);
	}

	/**
	 * Tests the magic __get method - status
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetStatus()
	{
		$this->assertThat(
			$this->object->status,
			$this->isInstanceOf('JFacebookStatus')
		);
	}

	/**
	 * Tests the magic __get method - checkin
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetCheckin()
	{
		$this->assertThat(
			$this->object->checkin,
			$this->isInstanceOf('JFacebookCheckin')
		);
	}

	/**
	 * Tests the magic __get method - event
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetEvent()
	{
		$this->assertThat(
			$this->object->event,
			$this->isInstanceOf('JFacebookEvent')
		);
	}

	/**
	 * Tests the magic __get method - group
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetGroup()
	{
		$this->assertThat(
			$this->object->group,
			$this->isInstanceOf('JFacebookGroup')
		);
	}

	/**
	 * Tests the magic __get method - link
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetLink()
	{
		$this->assertThat(
			$this->object->link,
			$this->isInstanceOf('JFacebookLink')
		);
	}

	/**
	 * Tests the magic __get method - note
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetNote()
	{
		$this->assertThat(
			$this->object->note,
			$this->isInstanceOf('JFacebookNote')
		);
	}

	/**
	 * Tests the magic __get method - post
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetPost()
	{
		$this->assertThat(
			$this->object->post,
			$this->isInstanceOf('JFacebookPost')
		);
	}

	/**
	 * Tests the magic __get method - comment
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetComment()
	{
		$this->assertThat(
			$this->object->comment,
			$this->isInstanceOf('JFacebookComment')
		);
	}

	/**
	 * Tests the magic __get method - photo
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetPhoto()
	{
		$this->assertThat(
			$this->object->photo,
			$this->isInstanceOf('JFacebookPhoto')
		);
	}

	/**
	 * Tests the magic __get method - video
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetVideo()
	{
		$this->assertThat(
			$this->object->video,
			$this->isInstanceOf('JFacebookVideo')
		);
	}

	/**
	 * Tests the magic __get method - album
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function test__GetAlbum()
	{
		$this->assertThat(
			$this->object->album,
			$this->isInstanceOf('JFacebookAlbum')
		);
	}

	/**
	 * Tests the magic __get method - other (non existent)
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 * @expectedException  InvalidArgumentException
	 */
	public function test__GetOther()
	{
		$this->object->other;
	}

	/**
	 * Tests the setOption method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testSetOption()
	{
		$this->object->setOption('api.url', 'https://example.com/settest');

		$this->assertThat(
			$this->options->get('api.url'),
			$this->equalTo('https://example.com/settest')
		);
	}

	/**
	 * Tests the getOption method
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function testGetOption()
	{
		$this->options->set('api.url', 'https://example.com/gettest');

		$this->assertThat(
			$this->object->getOption('api.url', 'https://example.com/gettest'),
			$this->equalTo('https://example.com/gettest')
		);
	}
}
