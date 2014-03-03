<?php
/* SVN FILE: $Id: attachments_link_fixture.php 128 2009-12-02 17:16:53Z miha.nahtigal $ */
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
 * AttachmentsLinkFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class AttachmentsLinkFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'AttachmentsLink';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'attachment_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36),
		'class' => array('type' => 'string', 'null' => false, 'length' => 30),
		'foreign_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36),
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
			'attachment_id' => '4a1bde96-1040-43a3-b4a6-0544bcc1ccb9',
			'class' => 'Profile',
			'foreign_id' => 1,
		)
	);
}
?>