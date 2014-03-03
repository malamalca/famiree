<?php
/* SVN FILE: $Id: lil_upload_model_fixture.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
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
 * @version       $Revision: 113 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-08-16 12:09:41 +0200 (ned, 16 avg 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * LilUploadModelFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class LilUploadModelFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string 'LilUploadModel'
 * @access public
 */
	var $name = 'LilUploadModel';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'string', 'length'=>36, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true),
		'class' => array('type' => 'string', 'null' => true),
		'foreign_id' => array('type' => 'integer', 'null' => true),
		'filename' => array('type' => 'string', 'null' => true),
		'original' => array('type' => 'string', 'null' => true),
		'ext' => array('type' => 'string', 'null' => true),
		'dir' => array('type' => 'string', 'null' => true),
		'mimetype' => array('type' => 'string', 'null' => true),
		'filesize' => array('type' => 'integer', 'null' => true),
		'height' => array('type' => 'integer', 'null' => true),
		'width' => array('type' => 'integer', 'null' => true),
		'title' => array('type' => 'string', 'null' => true),
		'description' => array('type' => 'string', 'null' => true),
		'checksum' => array('type' => 'string', 'null' => true),
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
		array('id'=>'49fb3c7f-388c-427a-b654-0cccbcc1ccb9', 'user_id'=>1),
	);
}
?>
