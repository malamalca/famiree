<?php
/* SVN FILE: $Id: category_fixture.php 126 2009-07-02 07:21:52Z miha@nahtigal.com $ */
/**
 * Short description for category_fixture.php
 *
 * Long description for category_fixture.php
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
 * CategoryFixture class
 *
 * @uses          CakeTestFixture
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.fixtures
 */
class CategoryFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Category';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
			'blog_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4),
			'name' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
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
			'blog_id'  => 1,
			'name'  => 'Programming',
			'created'  => '2008-01-23 12:34:56',
			'modified'  => '2008-03-21 12:34:56'
		),
		array(
			'id'  => 2,
			'blog_id'  => 1,
			'name'  => 'Rant',
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33'
		),
		array(
			'id'  => 3,
			'blog_id'  => 2,
			'name'  => 'Children',
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33'
		),
		array(
			'id'  => 4,
			'blog_id'  => 2,
			'name'  => 'Spouse',
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33'
		),
		array(
			'id'  => 5,
			'blog_id'  => 2,
			'name'  => 'Parents',
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33'
		),
	);
}
?>
