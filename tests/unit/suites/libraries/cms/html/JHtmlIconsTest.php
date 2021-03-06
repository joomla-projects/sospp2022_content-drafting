<?php
/**
 * @package	    Joomla.UnitTest
 * @subpackage  HTML
 *
 * @copyright   (C) 2012 Open Source Matters, Inc. <https://www.joomla.org>
 * @license	    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JHtmlIcons.
 *
 * @package     Joomla.UnitTest
 * @subpackage  HTML
 * @since       3.0
 */
class JHtmlIconsTest extends TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function setUp()
	{
		parent::setUp();

		// We need to mock the application
		$this->saveFactoryState();

		$mockApp = $this->getMockCmsApp();
		$mockApp->expects($this->any())
			->method('getName')
			->willReturn('administrator');

		$mockApp->expects($this->any())
			->method('isClient')
			->with('administrator')
			->willReturn(true);

		JFactory::$application = $mockApp;

		$mockRouter = $this->getMockBuilder('Joomla\\CMS\\Router\\Router')->getMock();
		$mockRouter->expects($this->any())
			->method('build')
			->willReturn(new \JUri);

		TestReflection::setValue('JRoute', '_router', array('site' => $mockRouter));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function tearDown()
	{
		TestReflection::setValue('JRoute', '_router', array());

		// Restore the factory state
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Tests the buttons and button methods
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function testButtons()
	{
		$buttons = array(
			array(
				'link' => JRoute::_('index.php?option=com_content&task=article.add'),
				'image' => 'file-add',
				'icon' => 'header/icon-48-article-add.png',
				'text' => JText::_('MOD_QUICKICON_ADD_NEW_ARTICLE'),
			),
			array(
				'link' => JRoute::_('index.php?option=com_installer'),
				'image' => 'puzzle',
				'icon' => 'header/icon-48-extension.png',
				'text' => JText::_('MOD_QUICKICON_EXTENSION_MANAGER'),
				'access' => false
			),
			array(
				'link' => JRoute::_('index.php?option=com_templates'),
				'image' => 'eye',
				'icon' => 'header/icon-48-themes.png',
				'text' => JText::_('MOD_QUICKICON_TEMPLATE_MANAGER'),
			)
		);

		$this->assertThat(
			JHtmlIcons::buttons($buttons),
			$this->isType('string'),
			'JHtmlIcons::buttons() should return a string with the HTML markup of the button(s)'
		);
	}
}
