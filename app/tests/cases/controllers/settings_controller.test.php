<?php
/* SVN FILE: $Id: profiles_controller.test.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
/**
 * ProfilesControllerTest file
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 * Licensed under The Open Group Test Suite License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          http://www.nahtigal.com/
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 * @version       $Revision: 113 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-08-16 12:09:41 +0200 (ned, 16 avg 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
// first, overwrite configuration with default values
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'config' . DS .'core.php';
// import default controller
App::import('Controller', 'Settings');
/**
 * TestSettingsController class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class TestSettingsController extends SettingsController {
/**
 * autoRender property
 *
 * @var boolean
 * @access public
 */
	var $autoRender = false;
/**
 * redirect method
 *
 * @access public
 * @return void
 */
	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
/**
 * render method
 *
 * @access public
 * @return void
 */
	function render($action = null, $layout = null, $file = null) {
		$this->renderedAction = $action;
	}
/**
 * _stop method
 *
 * @access private
 * @return void
 */
	function _stop($status = 0) {
		$this->stopped = $status;
	}
}
/**
 * SettingsControllerTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class SettingsControllerTestCase extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array('app.log', 'app.profile', 'app.union', 'app.unit', 
		'app.post', 'app.posts_link', 'app.comment', 
		'app.attachment', 'app.attachments_link', 'app.imgnote', 'app.setting'	);
/**
 * startTest method
 *
 * @access public
 * @return void
 */
	function startTest() {
		$this->Settings =& new TestSettingsController();
		$this->Settings->constructClasses();
		$this->Settings->Component->initialize($this->Settings);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Settings->Session->destroy();
		unset($this->Settings);
		ClassRegistry::flush();
	}
/**
 * testLang method
 *
 * @access public
 * @return void
 */
	function testLang() {
		$this->Settings->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));
		
		$this->Settings->data = array(
			'locale' => 'slv',
	    );
    	
	    $this->Settings->params['action'] = 'lang';
	    $this->Settings->beforeFilter();
	    $this->Settings->Component->startup($this->Settings);
	    $this->Settings->lang();
	    
		//assert that some sort of session flash was set.
		$this->assertTrue($this->Settings->Session->check('Message.flash.message'));
		$this->assertEqual($this->Settings->redirectUrl, Router::url(null, true));
		
		// a new setting has been added
		$this->assertEqual($this->Settings->Setting->id, 1);
	}
/**
 * testDatetime method
 *
 * @access public
 * @return void
 */
	function testDatetime() {

	}
}
?>