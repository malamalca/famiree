<?php
/* SVN FILE: $Id: log_fixture.php 83 2009-06-04 06:03:33Z miha.nahtigal $ */
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
 * @version       $Revision: 83 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-06-04 08:03:33 +0200 (čet, 04 jun 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * LogFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class SettingFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Setting';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'profile_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'date_order' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3),
		'date_separator' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'date_24hr' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'datef_common' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'datef_noyear' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'datef_short' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'locale' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
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
	);
}
?>