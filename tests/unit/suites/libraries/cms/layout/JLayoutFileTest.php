<?php
/**
 * @package	    Joomla.UnitTest
 * @subpackage  Layout
 *
 * @copyright   (C) 2012 Open Source Matters, Inc. <https://www.joomla.org>
 * @license	    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JLayoutFile.
 */
class JLayoutFileTest extends TestCase
{
	/**
	 * @var JLayoutFile
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$application = $this->getMockCmsApp();

		$this->object = new JLayoutFile('joomla.sidebars.submenu');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();
		unset($this->object);
		parent::tearDown();
	}

	/**
	 * Tests the escape method
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function testEscape()
	{
		$this->assertThat(
			$this->object->escape('This is cool & fun to use!'),
			$this->equalTo('This is cool &amp; fun to use!')
		);
	}
}
