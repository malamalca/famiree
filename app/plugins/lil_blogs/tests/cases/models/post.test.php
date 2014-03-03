<?php
/* SVN FILE: $Id: post.test.php 194 2009-11-29 18:40:59Z miha@nahtigal.com $ */
/**
 * Short description for post.test.php
 *
 * Long description for post.test.php
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
 * @subpackage    lil_blogs.tests.cases.models
 * @since         v 1.0
 * @version       $Revision: 194 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-11-29 19:40:59 +0100 (ned, 29 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Model', 'LilBlogs.Post');
/**
 * PostTestCase class
 *
 * @uses          CakeTestCase
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.models
 */
class PostTestCase extends CakeTestCase {
/**
 * Post property
 *
 * @var object
 * @access public
 */
	var $Post = null;
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'plugin.lil_blogs.category', 'plugin.lil_blogs.author', 'plugin.lil_blogs.authors_blog',
		'plugin.lil_blogs.blog', 'plugin.lil_blogs.post', 'plugin.lil_blogs.comment',
		'plugin.lil_blogs.categories_post'
	);
/**
 * start method
 *
 * @access public
 * @return void
 */
	function start() {
		// reset variables
		require dirname(dirname(dirname(dirname(__FILE__)))).DS.'config'.DS.'core.php';
		
		parent::start();
		$this->Post =& ClassRegistry::init('Post');
	}
/**
 * testPostInstance method
 *
 * @access public
 * @return void
 */
	function testPostInstance() {
		$this->assertTrue(is_a($this->Post, 'Post'));
	}
/**
 * testPostFind method
 *
 * @access public
 * @return void
 */
	function testPostFind() {
		$results = $this->Post->recursive = -1;
		$results = $this->Post->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Post' => array(
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
		));
		$this->assertEqual($results, $expected);
	}
}
?>