<?php
/* SVN FILE: $Id: imgnote.test.php 69 2009-05-27 12:48:15Z miha.nahtigal $ */
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
 * @version       $Revision: 69 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-05-27 14:48:15 +0200 (sre, 27 maj 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
App::import('Model', 'Imgnote');
/**
 * ImgnoteTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.models
 */
class ImgnoteTestCase extends CakeTestCase {
/**
 * Imgnote property
 *
 * @var object
 * @access public
 */
	var $Imgnote = null;
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
 * start method
 *
 * @param string $method 
 * @access public
 * @return void
 */
	function start() {
		parent::start();
		$this->Imgnote =& ClassRegistry::init('Imgnote');
	}
/**
 * testImgnoteInstance method
 *
 * @access public
 * @return void
 */
	function testImgnoteInstance() {
		$this->assertTrue(is_a($this->Imgnote, 'Imgnote'));
	}
/**
 * testImgnoteAdd method
 *
 * @access public
 * @return void
 */
	function testImgnoteAdd() {
		$result = $this->Imgnote->save(array('Imgnote'=>array(
			'attachment_id' => '4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
			'profile_id' => 2,
			'x1' => 10,
			'y1' => 20,
			'width' => 100,
			'height' => 60,
			'note' => 'This is a person'
		)));
		$data = $this->Imgnote->read();
		
		$this->assertEqual($data['Imgnote']['note'], 'This is a person');
		
		// this attachment doesn't exist so assert error
		$result = $this->Imgnote->save(array('Imgnote'=>array(
			'attachment_id' => '49fb3c94-0780-2da4-b892-0cccbcc1ccb9',
			'profile_id' => 2,
			'x1' => 10,
			'y1' => 20,
			'width' => 100,
			'height' => 60,
			'note' => 'This is a person'
		)));
		$this->assertFalse($result);
	}
}
?>