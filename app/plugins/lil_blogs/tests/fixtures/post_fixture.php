<?php
/* SVN FILE: $Id: post_fixture.php 126 2009-07-02 07:21:52Z miha@nahtigal.com $ */
/**
 * Short description for post_fixture.php
 *
 * Long description for post_fixture.php
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
 * PostFixture class
 *
 * @uses          CakeTestFixture
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.fixtures
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
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'blog_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'author_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'category_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 4),
			'title' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'slug' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 255),
			'body' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'allow_comments' => array('type'=>'boolean', 'null' => false, 'default' => 1),
			'allow_pingback' => array('type'=>'boolean', 'null' => false, 'default' => 1),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'status' => array('type'=>'boolean', 'null' => false, 'default' => 0),
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
			'blog_id' => 1,
			'author_id' => 1,
			'category_id' => 1,
			'title' => 'My First Post',
			'slug' => 'my-first-post',
			'body' => 'Here comes my first post: Hello World.',
			'allow_comments' => 1,
			'allow_pingback' => 1,
			'created'  => '2008-01-23 12:34:56',
			'modified'  => '2008-03-21 12:34:56',
			'status' => 1,
		),
		array(
			'id' => 2,
			'blog_id' => 1,
			'author_id' => 1,
			'category_id' => 1,
			'title' => 'My Second Post',
			'slug' => 'my-second-post',
			'body' => 'Here comes my second post: I got it now.',
			'allow_comments' => 1,
			'allow_pingback' => 1,
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33',
			'status' => 1,
		),
		array(
			'id' => 3,
			'blog_id' => 2,
			'author_id' => 2,
			'category_id' => 3,
			'title' => 'About My Son',
			'slug' => 'about-my-son',
			'body' => 'I love him much.',
			'allow_comments' => 0,
			'allow_pingback' => 1,
			'created'  => '2008-02-03 02:34:56',
			'modified'  => '2008-02-03 02:34:56',
			'status' => 1,
		),
	);
}
?>
