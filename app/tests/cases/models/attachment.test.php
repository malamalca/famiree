<?php
/* SVN FILE: $Id: attachment.test.php 123 2009-11-29 18:38:55Z miha.nahtigal $ */
/**
 * AttachmentTest file
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
 * @subpackage    famiree.tests.cases.models
 * @version       $Revision: 123 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-11-29 19:38:55 +0100 (ned, 29 nov 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
App::import('Model', 'Attachment');
App::import('Behavior', 'LilUpload.LilUpload');
/**
 * LilUploadFamireeTestBehavior class
 *
 * @uses          LilUploadBehavior
 * @package       lil
 * @subpackage    lil.tests.cases.model.attachment.test
 */
class LilUploadFamireeTestBehavior extends LilUploadBehavior {
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
 * AttachmentTest class
 * 
 * This class is extended from Attachment Model. It replaces getTargetFolder
 * function with custom upload destinations for testing purposes.  
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.models
 */
class AttachmentTest extends Attachment {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'AttachmentTest';
/**
 * useTable property
 *
 * @var string
 * @access public
 */
	var $useTable = 'attachments';
/**
 * getTargetFolder function
 *
 * @param mixed $type
 * @access private
 * @return void
 */
	function getTargetFolder($type='thumbs') {
		if ($type=='thumbs') {
			return TEST_ATTACHMENT_DEST.'thumbs'.DS;
		} else {
			return TEST_ATTACHMENT_DEST.'uploads'.DS;
		}
 	}
}
/**
 * AttachmentTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.models
 */
class AttachmentTestCase extends CakeTestCase {
/**
 * Attachment property
 *
 * @var object
 * @access public
 */
	var $Attachment = null;
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'app.log', 'app.profile',
		'app.post', 'app.posts_link', 'app.comment', 'app.attachment', 'app.attachments_link',
		'app.unit', 'app.union', 'app.imgnote', 'app.setting'
	);
/**
 * startTest method
 *
 * @param string $method 
 * @access public
 * @return void
 */
	function startTest($method) {
		parent::startTest($method);
		$this->Attachment =& ClassRegistry::init('AttachmentTest');
		
		// change to mock behavior so custom destinations can be applied
		$settings = $this->Attachment->getLilUploadSettings();
		$this->Attachment->Behaviors->detach('LilUpload');
		$this->Attachment->Behaviors->attach('LilUploadFamireeTest', $settings);
	}
/**
 * endTest method
 *
 * @param string $method 
 * @access public
 * @return void
 */
	function endTest($method) {
		unset($this->Attachment);
		ClassRegistry::flush();
	}
/**
 * endCase method
 *
 * @access public
 * @return void
 */
	function endCase() {
		App::import('core', 'Folder');
		$f = new Folder();
		$f->delete(TEST_ATTACHMENT_TMP);
		$f->delete(TEST_ATTACHMENT_DEST);
	}
/**
 * testAttachmentInstance method
 *
 * @access public
 * @return void
 */
	function testAttachmentInstance() {
		$this->assertTrue(is_a($this->Attachment, 'Attachment'));
		
		// check for uplaods from fixture
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'original'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'large'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'medium'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'thumbs'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9.png'));
	}
/**
 * testAttachmentAdd method
 *
 * @access public
 * @return void
 */
	function testAttachmentAdd() {
		// put file to temporary folder so upload behavior can use it
		file_put_contents(TEST_ATTACHMENT_TMP.'asdf.tmp', file_get_contents(dirname(__FILE__).DS.'boat.jpg'));
		
		$result = $this->Attachment->save(array('AttachmentTest'=>array(
			'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
			'filename' => array(
				'size' => filesize(TEST_ATTACHMENT_TMP.'asdf.tmp'),
				'error' => false,
				'tmp_name' => TEST_ATTACHMENT_TMP.'asdf.tmp',
				'name' => 'boat.jpg',
				'type' => 'image/jpeg'
			)
		)));
		
		$this->assertTrue(!empty($result));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'49fb3c94-0780-4b0c-b892-0cccbcc1ccb9'.DS.'original'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'49fb3c94-0780-4b0c-b892-0cccbcc1ccb9'.DS.'large'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'49fb3c94-0780-4b0c-b892-0cccbcc1ccb9'.DS.'medium'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'thumbs'.DS.'49fb3c94-0780-4b0c-b892-0cccbcc1ccb9'.'.png'));
		
		$data = $this->Attachment->read();
		$this->assertEqual(@$data['AttachmentTest']['original'], 'boat.jpg');
		
		// unlink temporary files
		@unlink(TEST_ATTACHMENT_TMP.'asdf.tmp');
	}
/**
 * testGetImageSize method
 *
 * @access public
 * @return void
 */
	function testGetImageSize() {
		$size = $this->Attachment->getImageSize('4a1bde96-1040-43a3-b4a6-0544bcc1ccb9', 'original');
		$this->assertEqual(@$size['width'], 435);
		$this->assertEqual(@$size['height'], 288);
		
		$size = $this->Attachment->getImageSize('4a1bde96-1040-43a3-b4a6-0544bcc1ccb9', 'large');
		$this->assertEqual(@$size['width'], 640);
		$this->assertEqual(@$size['height'], 424);
		
		$size = $this->Attachment->getImageSize('49fb3c94-0780-4b0c-b892-doesntexist0', 'original');
		$this->assertFalse($size);
	}
/**
 * testCropToNewImage method
 *
 * @access public
 * @return void
 */
	function testCropToNewImage() {
		$result = $this->Attachment->cropToNewImage('4a1bde96-1040-43a3-b4a6-0544bcc1ccb9', 10, 20, 50, 80, 'Sample Crop Title', 1);
		$this->assertFalse(empty($result));
			
		$this->Attachment->id = $result;
		$data = $this->Attachment->read();
		
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.$result.DS.'original'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'thumbs'.DS.$result.'.png'));
		
		$this->assertEqual(@$data['AttachmentTest']['width'], 50);
		$this->assertEqual(@$data['AttachmentTest']['height'], 80);
	}
/**
 * testAttachmentDelete method
 *
 * @access public
 * @return void
 */
	function testAttachmentDelete() {
		$AttachmentsLink =& ClassRegistry::init('AttachmentsLink');
		$result = $AttachmentsLink->hasAny(array(
			'AttachmentsLink.attachment_id' => '4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
		));
		$this->assertTrue($result);
		
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'original'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'large'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'medium'));
		$this->assertTrue(file_exists(TEST_ATTACHMENT_DEST.'thumbs'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.'.png'));
		
		$this->Attachment->delete('4a1bde96-1040-43a3-b4a6-0544bcc1ccb9');
		
		$result = $AttachmentsLink->hasAny(array(
			'AttachmentsLink.attachment_id' => '4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
		));
		$this->assertFalse($result);
		
		$this->assertFalse(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'original'));
		$this->assertFalse(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'large'));
		$this->assertFalse(file_exists(TEST_ATTACHMENT_DEST.'uploads'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.DS.'medium'));
		$this->assertFalse(file_exists(TEST_ATTACHMENT_DEST.'thumbs'.DS.'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9'.'.png'));
	}
}
?>