<?php
/* SVN FILE: $Id: author_fixture.php 141 2009-08-30 17:13:28Z miha@nahtigal.com $ */
/**
 * Short description for author_fixture.php
 *
 * Long description for author_fixture.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          http://www.nahtigal.com/
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.fixtures
 * @since         v 1.0
 * @version       $Revision: 141 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-08-30 19:13:28 +0200 (ned, 30 avg 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * AuthorFixture class
 *
 * @uses          CakeTestFixture
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.fixtures
 */
class AuthorFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Author';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
			'admin' => array('type'=>'boolean', 'null' => false, 'default' => 0),
			'name' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'email' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 255),
			'username' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 50),
			'passwd' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 50),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
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
			'id'  => 1,
			'admin' => true,
			'name'  => 'John Doe',
			'email' => 'john@doe.com',
			'username' => 'johndoe',
			'passwd' => '',
			'created'  => '2008-01-23 12:34:56',
			'modified'  => '2008-03-21 12:34:56'
		),
		array(
			'id'  => 2,
			'admin' => false,
			'name'  => 'Jane Dean',
			'email' => 'jane.dean@home.com',
			'username' => 'janedean',
			'passwd' => '',
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33'
		),
	);
}
?>
