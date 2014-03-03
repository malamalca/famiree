<?php
/* SVN FILE: $Id: comment.test.php 194 2009-11-29 18:40:59Z miha@nahtigal.com $ */
/**
 * Short description for comment.test.php
 *
 * Long description for comment.test.php
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
App::import('Model', 'LilBlogs.Comment');
/**
 * CommentTestCase class
 *
 * @uses          CakeTestCase
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.models
 */
class CommentTestCase extends CakeTestCase {
/**
 * Comment property
 *
 * @var object
 * @access public
 */
	var $Comment = null;
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
		$this->Comment =& ClassRegistry::init('Comment');
	}
/**
 * testCommentInstance method
 *
 * @access public
 * @return void
 */
	function testCommentInstance() {
		$this->assertTrue(is_a($this->Comment, 'Comment'));
	}
/**
 * testCommentFind method
 *
 * @access public
 * @return void
 */
	function testCommentFind() {
		$results = $this->Comment->recursive = -1;
		$results = $this->Comment->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Comment' => array(
			'id'  => 2,
			'post_id'  => 1,
			'body'  => 'Go go Truman Show.',
			'author' => 'Jane Truman',
			'url' => NULL,
			'email' => NULL,
			'ip' => '80.77.112.99',
			'status' => LILCOMMENT_PENDING,
			'created'  => '2008-04-05 11:22:33',
			'modified'  => '2008-11-22 11:22:33'
		));
		$this->assertEqual($results, $expected);
	}
/**
 * testAdd method
 *
 * @access public
 * @return void
 */
	function testAdd() {
		$before = $this->Comment->find('count', array('conditions' => array('Comment.post_id' => 1)));
		
		$data = array('Comment' => array(
			'post_id' => 1,
			'body'    => 'Added by TestSuite.',
			'author'  => 'TestSuite',
			'url'     => 'http://www.simpletest.org',
			'email'   => 'test@simpletest.org',
		));
		
		$result = $this->Comment->add($data);
		$this->assertFalse(empty($result));
		
		$after = $this->Comment->find('count', array('conditions' => array('Comment.post_id' => 1)));
		$this->assertEqual($before + 1, $after);
	}
/**
 * testSetStatus method
 *
 * @access public
 * @return void
 */
	function testSetStatus() {
		$expected = true;
		$result = $this->Comment->hasAny(array('Comment.id' => 1, 'Comment.status' => LILCOMMENT_APPROVED));
		$this->assertEqual($result, $expected);
		
		$expected = true;
		$result = $this->Comment->setStatus(1, LILCOMMENT_PENDING);
		$this->assertEqual($result, $expected);
		
		$expected = false;
		$result = $this->Comment->setStatus(1, -27);
		$this->assertEqual($result, $expected);
		
		$expected = false;
		$result = $this->Comment->setStatus(-31, LILCOMMENT_APPROVED);
		$this->assertEqual($result, $expected);
		
		$expected = false;
		$result = $this->Comment->setStatus('dasjfdskj', 'fdsfsdf');
		$this->assertEqual($result, $expected);
	}
}
?>