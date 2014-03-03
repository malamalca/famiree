<?php
/* SVN FILE: $Id: blog_fixture.php 126 2009-07-02 07:21:52Z miha@nahtigal.com $ */
/**
 * Short description for blog_fixture.php
 *
 * Long description for blog_fixture.php
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
 * @version       $Revision: 126 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-07-02 09:21:52 +0200 (čet, 02 jul 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * BlogFixture class
 *
 * @uses          CakeTestFixture
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.fixtures
 */
class BlogFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Blog';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
			'name' => array('type'=>'string', 'null' => false, 'default' => '', 'length' => 255),
			'short_name' => array('type'=>'string', 'null' => false, 'default' => '', 'length' => 100),
			'description' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'theme' => array('type'=>'string', 'null' => false, 'default' => '', 'length' => 100),
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
			'name'  => 'My First Blog',
			'short_name' => 'first',
			'description' => 'This is my first blog.',
			'theme' => null,
			'created'  => '2008-01-23 12:34:56',
			'modified'  => '2008-03-21 12:34:56'
		),
		array(
			'id'  => 2,
			'name'  => 'No. 2',
			'short_name' => 'second',
			'description' => 'Blog no. 2.',
			'theme' => null,
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33'
		),
	);
}
?>
