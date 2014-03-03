<?php
/* SVN FILE: $Id: posts_controller.test.php 181 2009-10-18 19:18:48Z miha@nahtigal.com $ */
/**
 * Short description for posts_controller.test.php
 *
 * Long description for posts_controller.test.php
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
 * @version       $Revision: 181 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-18 21:18:48 +0200 (ned, 18 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Controller', 'LilBlogs.Posts');
/**
 * TestPostsController class
 *
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.controllers
 */
class TestPostsController extends PostsController {
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
 * PostsControllerTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class PostsControllerTestCase extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'plugin.lil_blogs.post', 'plugin.lil_blogs.blog', 'plugin.lil_blogs.category',
		'plugin.lil_blogs.author', 'plugin.lil_blogs.comment', 'plugin.lil_blogs.authors_blog',
		'plugin.lil_blogs.categories_post'
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
		$this->Posts =& new TestPostsController();
		$this->Posts->constructClasses();
		$this->Posts->Component->initialize($this->Posts);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Posts->Session->destroy();
		unset($this->Posts);
		ClassRegistry::flush();
	}
/**
 * testIndex method
 *
 * @access public
 * @return void
 */
	function testIndex() {
		$this->Posts->params['controller'] = 'posts';
		$this->Posts->params['action'] = 'index';
		$this->Posts->params['url'] = array('url' => 'admin/lil_blogs/posts'); // needed for Paginate
		
		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. Check without specified index
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Posts->index();
		$this->assertTrue(empty($this->Posts->test_redirectUrl));
		$this->assertTrue($this->Posts->test_404);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. Check non existing blog
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Posts->test_reset();
		
		Configure::write('defaultBlog', 'doesnotexist');
		$this->Posts->index();
		$this->assertTrue(empty($this->Posts->test_redirectUrl));
		$this->assertTrue($this->Posts->test_404);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 3. Check ordinary blog
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Posts->test_reset();
		
		Configure::write('defaultBlog', null);
		$this->Posts->params['blogname'] = 'first';
		$this->Posts->index();
		$this->assertTrue(empty($this->Posts->test_redirectUrl));
		$this->assertFalse($this->Posts->test_404);
		
		Configure::write('defaultBlog', null);
		$this->Posts->params['blogname'] = 'all';
		$this->Posts->index();
		$this->assertTrue(empty($this->Posts->test_redirectUrl));
		$this->assertFalse($this->Posts->test_404);
	}
/**
 * testAdminIndex method
 *
 * @access public
 * @return void
 */
	function testAdminIndex() {
		$this->Posts->params['controller'] = 'posts';
		$this->Posts->params['action'] = 'admin_index';
		$this->Posts->params['prefix'] = 'admin';
		$this->Posts->params['admin.blog_id'] = 1;
		$this->Posts->params['url'] = array('url' => 'admin/lil_blogs/posts'); // needed for Paginate
		
		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		
		$this->Posts->admin_index();
		$this->assertTrue(empty($this->Posts->Post->id));
	}
/**
 * testAdminAdd method
 *
 * @access public
 * @return void
 */
	function testAdminAdd() {
		$this->Posts->params['controller'] = 'posts';
		$this->Posts->params['action'] = 'admin_add';
		$this->Posts->params['prefix'] = 'admin';
		$this->Posts->params['admin.blog_id'] = 1;

		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		
		$this->Posts->data = array(
			'Post' => array(
				'blog_id' => 1,
				'author_id' => 1,
				'category_id' => 1,
				'title' => 'My First Testsuite Post',
				'slug' => 'my-first-testsuite-post',
				'body' => 'Here comes my first post: Hello Testsuite.',
				'allow_comments' => 1,
				'allow_pingback' => 1,
				'status' => 1,
			)
	    );
		
		$this->Posts->admin_add();
		
		//assert that some sort of session flash was set.
		$this->assertTrue($this->Posts->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Posts->test_redirectUrl));
		
		$expected_url = Router::normalize(array('action' => 'admin_index', 1));
		$this->assertEqual(Router::normalize($this->Posts->test_redirectUrl), $expected_url);

		// a new setting has been added
		$this->assertFalse(empty($this->Posts->Post->id));
	}
/**
 * testAdminEdit method
 *
 * @access public
 * @return void
 */
	function testAdminEdit() {
		$this->Posts->params['controller'] = 'posts';
		$this->Posts->params['action'] = 'admin_edit';
		$this->Posts->params['prefix'] = 'admin';
		$this->Posts->params['admin.blog_id'] = 1;

		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		
		// check for existing value
		$this->assertEqual('My First Post', $this->Posts->Post->field('title', array('Post.id' => 1)));

		$this->Posts->data = array(
			'Post' => array(
			    'id'     => 1,
				'blog_id' => 1,
				'author_id' => 1,
				'category_id' => 1,
				'title' => 'My First Testsuite Edited Post',
				'slug' => 'my-first-post',
				'body' => 'Here comes my first post: Hello Testsuite.',
				'allow_comments' => 1,
				'allow_pingback' => 1,
				'status' => 1,
			)
	    );

		$this->Posts->admin_edit(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Posts->Session->check('Message.flash.message'));

		$expected_url = Router::normalize(array('action' => 'admin_index', 1));
		$this->assertEqual(Router::normalize($this->Posts->test_redirectUrl), $expected_url);

		// a new setting has been added
		$this->assertEqual('My First Testsuite Edited Post', $this->Posts->Post->field('title', array('Post.id' => 1)));
	}
/**
 * testAdminDelete method
 *
 * @access public
 * @return void
 */
	function testAdminDelete() {
		$this->Posts->params['controller'] = 'posts';
		$this->Posts->params['action'] = 'admin_delete';
		$this->Posts->params['prefix'] = 'admin';
		$this->Posts->params['admin.blog_id'] = 1;

		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		
		////////////////////////////////
		// 1. check normal delete
		$this->assertTrue($this->Posts->Post->hasAny(array('Post.id' => 1)));

		$this->Posts->admin_delete(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Posts->Session->check('Message.flash.message'));

		// redirect url must not be empty
		$this->assertFalse(empty($this->Posts->test_redirectUrl));

		$this->assertFalse($this->Posts->Post->hasAny(array('Post.id' => 1)));
		
		////////////////////////////////
		// 2. try to delete unexisting
		$this->Posts->admin_delete(-30);

		$this->assertTrue($this->Posts->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Posts->test_redirectUrl));
		$this->assertTrue($this->Posts->test_404);
	}
/**
 * testIsAuthorized method
 *
 * @access public
 * @return void
 */
	function testIsAuthorized() {
		$this->Posts->params['controller'] = 'posts';
		$this->Posts->params['action'] = 'admin_edit';
		$this->Posts->params['admin'] = true;
		$this->Posts->params['pass'][0] = 1;
		$this->Posts->params['url'] = array('url' => 'admin/lil_blogs/posts/admin_edit'); // needed for Paginate

		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		
		Configure::write('LilBlogs.allowAuthorsAnything', true);
		$result = $this->Posts->isAuthorized();
		$this->assertTrue($result);
		
		Configure::write('LilBlogs.allowAuthorsAnything', false);
		$result = $this->Posts->isAuthorized();
		$this->assertFalse($result);
		
		// authenticate as user 1
		$this->Posts->Session->write('Auth', $this->Posts->Post->Author->read(null, 1));
		
		$this->Posts->params['admin.blog_id'] = 1;
		$result = $this->Posts->isAuthorized();
		$this->assertTrue($result);
		
		$this->Posts->params['pass'][0] = 3;
		$result = $this->Posts->isAuthorized();
		$this->assertFalse($result);
		
		$this->Posts->params['pass'][0] = -30;
		$result = $this->Posts->isAuthorized();
		$this->assertFalse($result);
		
		$this->Posts->params['action'] = 'admin_index';
		unset($this->Posts->params['pass'][0]);
		$result = $this->Posts->isAuthorized();
		$this->assertTrue($result);
		
		// authenticate as user 2 not admin
		$this->Posts->Session->write('Auth', $this->Posts->Post->Author->read(null, 2));
		$this->Posts->params['action'] = 'admin_edit';
		$this->Posts->params['pass'][0] = 1;
		$this->Posts->params['admin.blog_id'] = 1;
		
		$result = $this->Posts->isAuthorized();
		$this->assertFalse($result);
		
		$this->Posts->params['pass'][0] = -30;
		$result = $this->Posts->isAuthorized();
		$this->assertFalse($result);
		
		$this->Posts->params['action'] = 'admin_index';
		unset($this->Posts->params['pass'][0]);
		$result = $this->Posts->isAuthorized();
		$this->assertFalse($result);
		
		$this->Posts->params['admin.blog_id'] = 2;
		$result = $this->Posts->isAuthorized();
		$this->assertTrue($result);

	}
} 
?>