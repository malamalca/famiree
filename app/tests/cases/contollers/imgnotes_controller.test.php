<?php
/* SVN FILE: $Id: imgnotes_controller.test.php 104 2009-07-05 18:07:47Z miha.nahtigal $ */
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
 * @version       $Revision: 104 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-07-05 20:07:47 +0200 (ned, 05 jul 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
require dirname(dirname(__FILE__)).DS.'app_test.php';
App::import('Behavior', 'LilUpload.LilUpload');
class LilUploadTestBehavior extends LilUploadBehavior {
/**
 * __moveUploadedFile method
 *
 * Override this function with different file move mechanism as move_uploaded_file()
 * which can't be used with fake upload data because of hack protection
 *
 * @access public
 * @return void
 */
	function __moveUploadedFile($source, $dest) {
		file_put_contents($dest, file_get_contents($source));
		return true;
	}
}
/**
 * ImgnoteControllerTest class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class ImgnoteControllerTest extends AppTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
    var $fixtures = array(
		'app.post', 'app.posts_link', 'app.comment', 'app.log', 'app.profile', 'app.union', 'app.unit',
		'app.attachment', 'app.attachments_link', 'app.imgnote');
/**
 * testAdd method
 *
 * @access public
 * @return void
 */
    function testAdd() {
    	// this has to be on top for getLastInsertID to work
    	$Imgnote =& ClassRegistry::init('Imgnote');
    	
		$result = $this->testAction('/imgnotes/add', array('return' =>'vars',
			'data'=>array('Imgnote'=>array(
				'user_id' => 1,
				'attachment_id' => '4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
				'x1' => 20,
				'y1' => 10,
				'width' => 50,
				'height' => 120,
				'note' => 'Live test note.',
			))));
		$this->assertRedirect('/imgnotes/view/');
		
		$data = $Imgnote->read(null, $Imgnote->getLastInsertID());
		$this->assertEqual($data['Imgnote']['note'], 'Live test note.');
	}
/**
 * testDelete method
 *
 * @access public
 * @return void
 */
	function testDelete() {
		$result = $this->testAction('/imgnotes/delete/1', array(
			'return' =>'vars', 
		));
		$this->assertRedirect();
		
		$Imgnote =& ClassRegistry::init('Imgnote');
		$this->assertFalse($Imgnote->hasAny('Imgnote.id=1'));
	}
}
?>