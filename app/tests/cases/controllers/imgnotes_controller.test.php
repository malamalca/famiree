<?php
/* SVN FILE: $Id: imgnotes_controller.test.php 156 2010-01-15 14:26:08Z miha.nahtigal $ */
/**
 * ImgnoteControllerTest file
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
 * @version       $Revision: 156 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-15 15:26:08 +0100 (pet, 15 jan 2010) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
// first, overwrite configuration with default values
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'config' . DS .'core.php';
// import default controller
App::import('Controller', 'Imgnotes');
/**
 * TestImgnotesController class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class TestImgnotesController extends ImgnotesController {
/**
 * autoRender property
 *
 * @var boolean
 * @access public
 */
	var $autoRender = false;
/**
 * redirectUrl property
 *
 * @var boolean
 * @access public
 */
	var $test_redirectUrl = null;
/**
 * renderedAction property
 *
 * @var boolean
 * @access public
 */
	var $test_renderedAction = null;
/**
 * stopped property
 *
 * @var boolean
 * @access public
 */
	var $test_stopped = null;
/**
 * error404 property
 *
 * @var boolean
 * @access public
 */
	var $test_404 = false;
/**
 * redirect method
 *
 * @access public
 * @return void
 */
	function redirect($url, $status = null, $exit = true) {
		$this->test_redirectUrl = $url;
	}
/**
 * render method
 *
 * @access public
 * @return void
 */
	function render($action = null, $layout = null, $file = null) {
		$this->test_renderedAction = $action;
	}
/**
 * _stop method
 *
 * @access private
 * @return void
 */
	function _stop($status = 0) {
		$this->test_stopped = $status;
	}
/**
 * error404 method
 *
 * @access private
 * @return void
 */
	function error404() {
		$this->test_404 = true;
	}
/**
 * test_reset method
 *
 * This method resets all test variables to default state;
 *
 * @access private
 * @return void
 */
	function test_reset() {
		$this->test_redirectUrl = null;
		$this->test_renderedAction = null;
		$this->test_stopped = null;
		$this->test_404 = false;
	}
}
/**
 * ImgnotesControllerTest class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class ImgnotesControllerTest extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
    var $fixtures = array(
		'app.post', 'app.posts_link', 'app.comment', 'app.log', 'app.profile', 'app.union', 'app.unit',
		'app.attachment', 'app.attachments_link', 'app.imgnote', 'app.setting');
/**
 * startTest method
 *
 * @access public
 * @return void
 */
	function startTest() {
		$this->Imgnotes =& new TestImgnotesController();
		$this->Imgnotes->constructClasses();
		$this->Imgnotes->Component->initialize($this->Imgnotes);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Imgnotes->Session->destroy();
		unset($this->Imgnotes);
		ClassRegistry::flush();
	}
/**
 * testAdd method
 *
 * @access public
 * @return void
 */
	function testAdd() {
		// this has to be on top for getLastInsertID to work
		$Imgnote =& ClassRegistry::init('Imgnote');
		
		$this->Imgnotes->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));
		
		$this->Imgnotes->data = array(
			'Imgnote'=>array(
				'user_id' => 1,
				'attachment_id' => '4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
				'x1' => 20,
				'y1' => 10,
				'width' => 50,
				'height' => 120,
				'note' => 'Live test note.',
			)
		);
		
		$this->Imgnotes->params['action'] = 'add';
		$this->Imgnotes->beforeFilter();
		$this->Imgnotes->Component->startup($this->Imgnotes);
		$this->Imgnotes->add();
		
		
		$imgnote_id = $Imgnote->getLastInsertID();
		$this->assertEqual(Router::url($this->Imgnotes->test_redirectUrl, true), Router::url('/imgnotes/view/' . $imgnote_id, true));
		
		$data = $Imgnote->read(null, $imgnote_id);
		$this->assertEqual($data['Imgnote']['note'], 'Live test note.');
	}
/**
 * testDelete method
 *
 * @access public
 * @return void
 */
	function testDelete() {
		$Imgnote =& ClassRegistry::init('Imgnote');
		
		$this->Imgnotes->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));
		
		$this->Imgnotes->params['action'] = 'delete';
		$this->Imgnotes->beforeFilter();
		$this->Imgnotes->Component->startup($this->Imgnotes);
		$this->Imgnotes->delete(1);
		
		
		$imgnote_id = $Imgnote->getLastInsertID();
		$this->assertFalse(empty($this->Imgnotes->test_redirectUrl));
		$this->assertFalse($Imgnote->hasAny('Imgnote.id=1'));
	}
}
?>