<?php
/* SVN FILE: $Id: imgnote_fixture.php 128 2009-12-02 17:16:53Z miha.nahtigal $ */
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
/**
 * ImgnoteFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class ImgnoteFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Imgnote';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'attachment_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36),
		'profile_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'x1' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'y1' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'width' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'height' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'note' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
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
			'id' => 1,
			'user_id' => 1,
			'attachment_id' => '4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
			'profile_id' => 1,
			
			'x1' => 10,
			'y1' => 20,
			'width' => 100,
			'height' => 80,
			
			'note' => 'This is a first memory.',
			'created'  => '2009-03-01 11:22:33',
			'modified'  => '2008-03-01 22:33:44'
		),
	);
}
?>