<?php
/* SVN FILE: $Id: comments_controller.test.php 184 2009-10-21 18:52:07Z miha@nahtigal.com $ */
/**
 * Short description for comments_controller.test.php
 *
 * Long description for comments_controller.test.php
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
 * @subpackage    lil_blogs.tests.cases.controllers
 * @since         v 1.0
 * @version       $Revision: 184 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-21 20:52:07 +0200 (sre, 21 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Controller', 'LilBlogs.Comments');
/**
 * TestCommentsController class
 *
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.controllers
 */
class TestCommentsController extends CommentsController {
/**
 * autoRender property
 *
 * @var boolean
 * @access public
 */
	var $autoRender = false;
/**
 * redirectUrl property
 *
 * @var boolean
 * @access public
 */
	var $test_redirectUrl = null;
/**
 * renderedAction property
 *
 * @var boolean
 * @access public
 */
	var $test_renderedAction = null;
/**
 * stopped property
 *
 * @var boolean
 * @access public
 */
	var $test_stopped = null;
/**
 * error404 property
 *
 * @var boolean
 * @access public
 */
	var $test_404 = false;
/**
 * redirect method
 *
 * @access public
 * @return void
 */
	function redirect($url, $status = null, $exit = true) {
		$this->test_redirectUrl = $url;
	}
/**
 * render method
 *
 * @access public
 * @return void
 */
	function render($action = null, $layout = null, $file = null) {
		$this->test_renderedAction = $action;
	}
/**
 * _stop method
 *
 * @access private
 * @return void
 */
	function _stop($status = 0) {
		$this->test_stopped = $status;
	}
/**
 * error404 method
 *
 * @access private
 * @return void
 */
	function error404() {
		$this->test_404 = true;
	}
/**
 * test_reset method
 *
 * This method resets all test variables to default state;
 *
 * @access private
 * @return void
 */
	function test_reset() {
		$this->test_redirectUrl = null;
		$this->test_renderedAction = null;
		$this->test_stopped = null;
		$this->test_404 = false;
	}
}
/**
 * CommentsControllerTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class CommentsControllerTestCase extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'plugin.lil_blogs.comment', 'plugin.lil_blogs.blog', 'plugin.lil_blogs.category',
		'plugin.lil_blogs.author', 'plugin.lil_blogs.authors_blog', 
		'plugin.lil_blogs.categories_post', 'plugin.lil_blogs.post'
	);
/**
 * startCase method
 *
 * @access public
 * @return void
 */
	function startCase() {
	    // overwrite configuration with default values
		require dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'config' . DS . 'core.php';
	}
/**
 * startTest method
 *
 * @access public
 * @return void
 */
	function startTest() {
		$this->Comments =& new TestCommentsController();
		$this->Comments->constructClasses();
		$this->Comments->Component->initialize($this->Comments);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Comments->Session->destroy();
		unset($this->Comments);
		ClassRegistry::flush();
	}
/**
 * testAdminIndex method
 *
 * @access public
 * @return void
 */
	function testAdminIndex() {
		$this->Comments->params['controller'] = 'comments';
		$this->Comments->params['action'] = 'admin_index';
		$this->Comments->params['prefix'] = 'admin';
		$this->Comments->params['admin.blog_id'] = 1;
		$this->Comments->params['url'] = array('url' => 'admin/lil_blogs/comments'); // needed for Paginate
		
		$this->Comments->beforeFilter();
		$this->Comments->Component->startup($this->Comments);
		
		$this->Comments->admin_index();
		$this->assertTrue(empty($this->Comments->Comment->id));
	}
/**
 * testAdminEdit method
 *
 * @access public
 * @return void
 */
	function testAdminEdit() {
		$this->Comments->params['controller'] = 'comments';
		$this->Comments->params['action'] = 'admin_edit';
		$this->Comments->params['prefix'] = 'admin';
		$this->Comments->params['admin.blog_id'] = 1;

		$this->Comments->beforeFilter();
		$this->Comments->Component->startup($this->Comments);
		
		// check for existing value
		$this->assertEqual('Arthur C. Clarke', $this->Comments->Comment->field('author', array('Comment.id' => 1)));

		$this->Comments->data = array(
			'Comment' => array(
			    'id'     => 1,
			   	'post_id'  => 1,
				'body'  => 'Here comes my first comment: Hello World.',
				'author' => 'Arthur J. Clarke',
				'url' => 'http://www.arthur-clarke.com/',
				'email' => 'arthur@clarke.com',
				'ip' => '213.143.80.52',
				'status' => 1,
			)
	    );

		$this->Comments->admin_edit(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Comments->Session->check('Message.flash.message'));

		$expected_url = Router::normalize(array('action' => 'admin_index', 1));
		$this->assertEqual(Router::normalize($this->Comments->test_redirectUrl), $expected_url);

		// a new setting has been added
		$this->assertEqual('Arthur J. Clarke', $this->Comments->Comment->field('author', array('Comment.id' => 1)));
	}
/**
 * testAdminDelete method
 *
 * @access public
 * @return void
 */
	function testAdminDelete() {
		$this->Comments->params['controller'] = 'comments';
		$this->Comments->params['action'] = 'admin_delete';
		$this->Comments->params['prefix'] = 'admin';
		$this->Comments->params['admin.blog_id'] = 1;

		$this->Comments->beforeFilter();
		$this->Comments->Component->startup($this->Comments);
		
		////////////////////////////////
		// 1. check normal delete
		$this->assertTrue($this->Comments->Comment->hasAny(array('Comment.id' => 1)));

		$this->Comments->admin_delete(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Comments->Session->check('Message.flash.message'));

		// redirect url must not be empty
		$this->assertFalse(empty($this->Comments->test_redirectUrl));

		$this->assertFalse($this->Comments->Comment->hasAny(array('Comment.id' => 1)));
		
		////////////////////////////////
		// 2. try to delete unexisting
		$this->Comments->admin_delete(-30);

		$this->assertTrue($this->Comments->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Comments->test_redirectUrl));
		$this->assertTrue($this->Comments->test_404);
	}
/**
 * testAdminCategorize method
 *
 * @access public
 * @return void
 */
	function testAdminCategorize() {
		$this->Comments->params['controller'] = 'comments';
		$this->Comments->params['action'] = 'admin_categorize';
		$this->Comments->params['prefix'] = 'admin';
		$this->Comments->params['admin.blog_id'] = 1;

		$this->Comments->beforeFilter();
		$this->Comments->Component->startup($this->Comments);
		
		$this->assertTrue($this->Comments->Comment->hasAny(array('Comment.id' => 1, 'Comment.status' => LILCOMMENT_APPROVED)));
		
		$this->Comments->admin_categorize(1, LILCOMMENT_PENDING);
		$this->assertTrue($this->Comments->Comment->hasAny(array('Comment.id' => 1, 'Comment.status' => LILCOMMENT_PENDING)));
		
		$this->Comments->test_reset();
		
		$this->Comments->admin_categorize(1, -27);
		$this->assertTrue($this->Comments->Comment->hasAny(array('Comment.id' => 1, 'Comment.status' => LILCOMMENT_PENDING)));
		
		$this->Comments->test_reset();

		$this->Comments->admin_categorize(-30, LILCOMMENT_PENDING);
		$this->assertTrue($this->Comments->test_404);
	}
} 
?>