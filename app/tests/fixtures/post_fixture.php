<?php
/* SVN FILE: $Id: post_fixture.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
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
 * PostFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class PostFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Post';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
		'blog_id' => array('type' => 'integer', 'null' => false),
		'status' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 2),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'slug' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'no_comments' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4),
		'allow_comments' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'creator_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modifier_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
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
			'title' => 'Hello world',
			'body' => 'This is a first memory.',
			'created'  => '2009-03-01 11:22:33',
			'modified'  => '2008-03-01 22:33:44'
		)
	);
}
?>