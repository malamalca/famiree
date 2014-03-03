<?php
/* SVN FILE: $Id: blogs_controller.test.php 193 2009-11-29 16:33:35Z miha@nahtigal.com $ */
/**
 * Short description for blogs_controller.test.php
 *
 * Long description for blogs_controller.test.php
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
 * @version       $Revision: 193 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-11-29 17:33:35 +0100 (ned, 29 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Controller', 'LilBlogs.Blogs');
/**
 * TestBlogsController class
 *
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.controllers
 */
class TestBlogsController extends BlogsController {
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
 * BlogsControllerTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class BlogsControllerTestCase extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'plugin.lil_blogs.blog', 'plugin.lil_blogs.author', 'plugin.lil_blogs.category',
		'plugin.lil_blogs.categories_post', 'plugin.lil_blogs.authors_blog', 
		'plugin.lil_blogs.comment', 'plugin.lil_blogs.post'
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
		$this->Blogs =& new TestBlogsController();
		$this->Blogs->constructClasses();
		$this->Blogs->Component->initialize($this->Blogs);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Blogs->Session->destroy();
		unset($this->Blogs);
		ClassRegistry::flush();
	}
/**
 * testIndex method
 *
 * @access public
 * @return void
 */
	function testIndex() {
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal index
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'index';
		$this->Blogs->params['url'] = array('url' => 'lil_blogs/blogs'); // needed for Paginate
		
		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		$this->Blogs->index();
		$this->assertTrue(empty($this->Blogs->Blog->id));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. check index with single blog
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Blogs->Blog->deleteAll(array('Blog.id <>' => 1));
		$this->Blogs->index();
		$expected_url = Router::normalize($this->Blogs->test_redirectUrl);
		$this->assertEqual(Router::normalize(array(
			'admin'      => false,
			'plugin'     => 'lil_blogs',
			'controller' => 'posts',
			'action'     => 'index',
			'blogname'   => 'first'
		)), $expected_url);
	}
/**
 * testView method
 *
 * @access public
 * @return void
 */
	function testView() {
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'view';
		$this->Blogs->params['plugin'] = 'lil_blogs';
		
		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		$this->Blogs->view('first');
		$this->assertTrue(empty($this->Blogs->Blog->id));
	}
/**
 * testAdminIndex method
 *
 * @access public
 * @return void
 */
	function testAdminIndex() {
		$this->Blogs->Session->write('Auth.Author', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));
		
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'admin_index';
		$this->Blogs->params['prefix'] = 'admin';
		$this->Blogs->params['admin.blog_id'] = 1;
		$this->Blogs->params['url'] = array('url' => 'admin/lil_blogs/blogs'); // needed for Paginate
		
		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		$this->Blogs->admin_index();
		$this->assertTrue(empty($this->Blogs->Blog->id));
	}
/**
 * testAdminAdd method
 *
 * @access public
 * @return void
 */
	function testAdminAdd() {
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'admin_add';
		$this->Blogs->params['prefix'] = 'admin';
		$this->Blogs->params['admin.blog_id'] = 1;

		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);

		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal edit
		////////////////////////////////////////////////////////////////////////////////////////////		
		$this->Blogs->data = array(
			'Blog' => array(
				'name' => 'My First Blog added from Testsuite',
				'short_name' => 'my_first_blog_added_from_testsuite',
				'description' => 'This is my first blog added from Testsuite.'
			)
	    );
		
		$this->Blogs->admin_add();
		
		//assert that some sort of session flash was set.
		$this->assertTrue($this->Blogs->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Blogs->test_redirectUrl));
		
		$expected_url = Router::normalize($this->Blogs->test_redirectUrl);
		$this->assertEqual(Router::normalize(array(
			'admin'      => true,
			'plugin'     => 'lil_blogs',
			'controller' => 'blogs',
			'action'     => 'admin_index')), $expected_url);

		// a new setting has been added
		$this->assertFalse(empty($this->Blogs->Blog->id));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. Check invalid data
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Blogs->test_reset();
		$this->Blogs->data = array(
			'Blog' => array(
				'name' => '',
				'short_name' => 'my_first_blog_added_from_testsuite',
				'description' => 'This is my first blog added from Testsuite.'
			)
	    );
	    
	    $this->Blogs->admin_add();
	    
	    $this->assertEqual($this->Blogs->Session->read('Message.flash.layout'), 'error');
	    $this->assertFalse($this->Blogs->test_404);
	}
/**
 * testAdminEdit method
 *
 * @access public
 * @return void
 */
	function testAdminEdit() {
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'admin_edit';
		$this->Blogs->params['prefix'] = 'admin';
		$this->Blogs->params['admin.blog_id'] = 1;

		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal edit
		////////////////////////////////////////////////////////////////////////////////////////////
		
		// check for existing value
		$this->assertEqual('My First Blog', $this->Blogs->Blog->field('name', array('Blog.id' => 1)));

		$this->Blogs->data = array(
			'Blog' => array(
			    'id'   => 1,
				'name' => 'My First Blog edited from Testsuite',
				'short_name' => 'test',
				'description' => 'This is my first blog edited from Testsuite.'
			)
	    );

		$this->Blogs->admin_edit(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Blogs->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Blogs->test_redirectUrl));

		$expected_url = Router::normalize($this->Blogs->test_redirectUrl);
		$this->assertEqual(Router::normalize(array(
			'admin'      => true,
			'plugin'     => 'lil_blogs',
			'controller' => 'blogs',
			'action'     => 'admin_index')), $expected_url);

		// a new setting has been added
		$this->assertEqual('My First Blog edited from Testsuite', $this->Blogs->Blog->field('name', array('Blog.id' => 1)));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. check redirect on invalid blog
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Blogs->test_reset();
		$this->Blogs->data = null;
		
		$this->Blogs->admin_edit(-30);
		$this->assertTrue($this->Blogs->test_404);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 3. Check invalid data
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Blogs->test_reset();
		$this->Blogs->data = array(
			'Blog' => array(
			    'id'   => 1,
				'name' => '',
				'short_name' => 'test',
				'description' => 'This is my first blog edited from Testsuite.'
			)
	    );
	    
	    $this->Blogs->admin_edit(1);
	    
	    $this->assertEqual($this->Blogs->Session->read('Message.flash.layout'), 'error');
	    $this->assertFalse($this->Blogs->test_404);
	}
/**
 * testAdminDelete method
 *
 * @access public
 * @return void
 */
	function testAdminDelete() {
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'admin_delete';
		$this->Blogs->params['prefix'] = 'admin';
		$this->Blogs->params['admin.blog_id'] = 1;

		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal delete
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->assertTrue($this->Blogs->Blog->hasAny(array('Blog.id' => 1)));

		$this->Blogs->admin_delete(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Blogs->Session->check('Message.flash.message'));

		// redirect url must not be empty
		$this->assertFalse(empty($this->Blogs->test_redirectUrl));

		$this->assertFalse($this->Blogs->Blog->hasAny(array('Blog.id' => 1)));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. try to delete unexisting
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Blogs->admin_delete(-30);

		$this->assertTrue($this->Blogs->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Blogs->test_redirectUrl));
		$this->assertTrue($this->Blogs->test_404);
	}
/**
 * testAdminSelect method
 *
 * @access public
 * @return void
 */
	function testAdminSelect() {
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'admin_select';
		$this->Blogs->params['prefix'] = 'admin';

		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal select
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Blogs->admin_select(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Blogs->Session->check('Message.flash.message'));

		// redirect url must not be empty
		$this->assertFalse(empty($this->Blogs->test_redirectUrl));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. try to select unexisting blog
		////////////////////////////////////////////////////////////////////////////////////////////
		unset($this->Blogs->params['admin.blog_id']);
		$this->Blogs->admin_select(-30);
		$this->assertTrue($this->Blogs->test_404);
	}
/**
 * testAdminList method
 *
 * @access public
 * @return void
 */
	function testAdminList() {
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'admin_list';
		$this->Blogs->params['prefix'] = 'admin';
		$this->Blogs->params['url'] = array('url' => 'admin/lil_blogs/blogs/list'); // needed for Paginate

		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		////////////////////////////////
		// 1. check normal seelct
		$this->Blogs->admin_list();
	}
/**
 * testIsAuthorized method
 *
 * @access public
 * @return void
 */
	function testIsAuthorized() {
		$this->Blogs->params['controller'] = 'blogs';
		$this->Blogs->params['action'] = 'admin_edit';
		$this->Blogs->params['prefix'] = 'admin';
		$this->Blogs->params['pass'][0] = 1;
		$this->Blogs->params['url'] = array('url' => 'admin/lil_blogs/blogs/list'); // needed for Paginate

		$this->Blogs->beforeFilter();
		$this->Blogs->Component->startup($this->Blogs);
		
		Configure::write('LilBlogs.allowAuthorsAnything', true);
		$result = $this->Blogs->isAuthorized();
		$this->assertTrue($result);
		
		Configure::write('LilBlogs.allowAuthorsAnything', false);
		$result = $this->Blogs->isAuthorized();
		$this->assertFalse($result);
		
		// authenticate as user 1
		$this->Blogs->Session->write('Auth', $this->Blogs->Blog->Author->read(null, 1));
		
		$result = $this->Blogs->isAuthorized();
		$this->assertTrue($result);
		
		unset($this->Blogs->params['pass'][0]);
		$result = $this->Blogs->isAuthorized();
		$this->assertFalse($result);
		
		$this->Blogs->params['action'] = 'admin_add';
		$result = $this->Blogs->isAuthorized();
		$this->assertTrue($result);
		
		// authenticate as user 2
		$this->Blogs->Session->write('Auth', $this->Blogs->Blog->Author->read(null, 2));
		
		$result = $this->Blogs->isAuthorized();
		$this->assertFalse($result);
	}
} 
?>