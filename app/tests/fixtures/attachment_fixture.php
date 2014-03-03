<?php
/* SVN FILE: $Id: attachment_fixture.php 128 2009-12-02 17:16:53Z miha.nahtigal $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          http://www.nahtigal.com/
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 * @version       $Revision: 128 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-12-02 18:16:53 +0100 (sre, 02 dec 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
define('TEST_ATTACHMENT_TMP', TMP.'upload_tmp'.DS);
define('TEST_ATTACHMENT_DEST', TMP.'attachments_test'.DS);
/**
 * AttachmentFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class AttachmentFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Attachment';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'filename' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'original' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'ext' => array('type' => 'string', 'null' => false, 'default' => 'gif', 'length' => 6),
		'mimetype' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 30),
		'filesize' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'height' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'width' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'checksum' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		array(
			'id'=>'4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
			'user_id' => 1,
			'filename' => 'original',
			'original' => 'boat.jpg',
			'ext' => 'jpg',
			'mimetype' => 'image/jpeg',
			'filesize' => 0,
			'height' => 288,
			'width' => 435,
			'checksum' => '5b69a767dd514d637f24615c604468b8',
			
			'created'  => '2009-03-01 11:22:33',
			'modified'  => '2008-03-01 22:33:44'
		)
	);
/**
 * init
 *
 * Overloaded init whichs copies files to TMP destination
 *
 * @access private
 * @return void
 */
	function init() {
		parent::init();
		
		App::import('core', 'Folder');
		$f = new Folder();
		$f->create(TEST_ATTACHMENT_DEST.'thumbs'.DS);
		$f->create(TEST_ATTACHMENT_DEST.'uploads'.DS);
		$f->create(TEST_ATTACHMENT_TMP.'uploads'.DS);
		
		foreach ($this->records as $record) {
			$f->create(TEST_ATTACHMENT_DEST.'uploads'.DS.$record['id']);
			
			file_put_contents(TEST_ATTACHMENT_DEST.'uploads'.DS.$record['id'].DS.'original', 
				file_get_contents(dirname(__FILE__).DS.'attachments'.DS.$record['id'].DS.'original'));
			file_put_contents(TEST_ATTACHMENT_DEST.'uploads'.DS.$record['id'].DS.'large', 
				file_get_contents(dirname(__FILE__).DS.'attachments'.DS.$record['id'].DS.'large'));
			file_put_contents(TEST_ATTACHMENT_DEST.'uploads'.DS.$record['id'].DS.'medium', 
				file_get_contents(dirname(__FILE__).DS.'attachments'.DS.$record['id'].DS.'medium'));
			file_put_contents(TEST_ATTACHMENT_DEST.'thumbs'.DS.$record['id'].'.png', 
				file_get_contents(dirname(__FILE__).DS.'attachments'.DS.$record['id'].DS.'thumb'));
		}
	}
}
?>