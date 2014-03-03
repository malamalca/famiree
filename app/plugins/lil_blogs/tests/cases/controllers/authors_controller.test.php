<?php
/* SVN FILE: $Id: authors_controller.test.php 193 2009-11-29 16:33:35Z miha@nahtigal.com $ */
/**
 * Short description for authors_controller.test.php
 *
 * Long description for authors_controller.test.php
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
App::import('Controller', 'LilBlogs.Authors');
/**
 * TestAuthorsController class
 *
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.controllers
 */
class TestAuthorsController extends AuthorsController {
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
 * AuthorsControllerTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class AuthorsControllerTestCase extends CakeTestCase {
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
		$this->Authors =& new TestAuthorsController();
		$this->Authors->constructClasses();
		$this->Authors->Component->initialize($this->Authors);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Authors->Session->destroy();
		unset($this->Authors);
		ClassRegistry::flush();
	}
/**
 * testAdminIndex method
 *
 * @access public
 * @return void
 */
	function testAdminIndex() {
		$this->Authors->params['controller'] = 'authors';
		$this->Authors->params['action'] = 'admin_index';
		$this->Authors->params['prefix'] = 'admin';
		$this->Authors->params['admin.blog_id'] = 1;
		
		$this->Authors->beforeFilter();
		$this->Authors->Component->startup($this->Authors);
		
		$this->Authors->admin_index();
		$this->assertTrue(empty($this->Authors->Author->id));
	}
/**
 * testAdminAdd method
 *
 * @access public
 * @return void
 */
	function testAdminAdd() {
		$this->Authors->params['controller'] = 'authors';
		$this->Authors->params['action'] = 'admin_add';
		$this->Authors->params['prefix'] = 'admin';
		$this->Authors->params['admin.blog_id'] = 1;

		$this->Authors->beforeFilter();
		$this->Authors->Component->startup($this->Authors);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. Check normal add
		////////////////////////////////////////////////////////////////////////////////////////////
		
		$this->Authors->data = array(
			'Author' => array(
				'admin'    => true,
				'name'     => 'New Author From TestSuite',
				'email'    => 'author@testsuite.com',
				'username' => 'testsuite_author',
				'passwd'   => '',
			)
	    );
	    
		$this->Authors->admin_add();
		
		//assert that some sort of session flash was set.
		$this->assertTrue($this->Authors->Session->check('Message.flash.message'));
		
		$expected_url = Router::normalize($this->Authors->test_redirectUrl);
		$this->assertEqual(Router::normalize(array('action' => 'admin_index')), $expected_url);

		// a new setting has been added
		$this->assertFalse(empty($this->Authors->Author->id));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. Check invalid data
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Authors->test_reset();
		$this->Authors->data = array(
			'Author' => array(
				'name' => '',
			)
	    );
	    
	    $this->Authors->admin_add();
	    
	    $this->assertEqual($this->Authors->Session->read('Message.flash.layout'), 'error');
	    $this->assertFalse($this->Authors->test_404);
	}
/**
 * testAdminEdit method
 *
 * @access public
 * @return void
 */
	function testAdminEdit() {
		$this->Authors->params['controller'] = 'authors';
		$this->Authors->params['action'] = 'admin_edit';
		$this->Authors->params['prefix'] = 'admin';
		$this->Authors->params['admin.blog_id'] = 1;

		$this->Authors->beforeFilter();
		$this->Authors->Component->startup($this->Authors);

		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. Check normal edit
		////////////////////////////////////////////////////////////////////////////////////////////
		
		// check for existing value
		$this->assertEqual('John Doe', $this->Authors->Author->field('name', array('Author.id' => 1)));

		$this->Authors->data = array(
			'Author' => array(
			    'id'   => 1,
				'name' => 'John Malkovic'
			)
	    );
	    
		$this->Authors->admin_edit(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Authors->Session->check('Message.flash.message'));

		$expected_url = Router::normalize($this->Authors->test_redirectUrl);
		$this->assertEqual(Router::normalize(array('action' => 'admin_index')), $expected_url);

		// a new setting has been added
		$this->assertEqual('John Malkovic', $this->Authors->Author->field('name', array('Author.id' => 1)));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. Check redirect on invalid author
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Authors->test_reset();
		$this->Authors->data = null;
		
		$this->Authors->admin_edit(-30);
		$this->assertTrue($this->Authors->test_404);


		////////////////////////////////////////////////////////////////////////////////////////////
		// 3. Check invalid data
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Authors->test_reset();
		$this->Authors->data = array(
			'Author' => array(
			    'id'   => 1,
				'name' => '',
			)
	    );
	    
	    $this->Authors->admin_edit(1);
	    
	    $this->assertEqual($this->Authors->Session->read('Message.flash.layout'), 'error');
	    $this->assertFalse($this->Authors->test_404);
	}
/**
 * testAdminDelete method
 *
 * @access public
 * @return void
 */
	function testAdminDelete() {
		$this->Authors->params['controller'] = 'authors';
		$this->Authors->params['action'] = 'admin_delete';
		$this->Authors->params['prefix'] = 'admin';
		$this->Authors->params['admin.blog_id'] = 1;

		$this->Authors->beforeFilter();
		$this->Authors->Component->startup($this->Authors);
		
		////////////////////////////////
		// 1. check normal delete
		$this->assertTrue($this->Authors->Author->hasAny(array('Author.id' => 1)));

		$this->Authors->admin_delete(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Authors->Session->check('Message.flash.message'));

		// redirect url must not be empty
		$this->assertFalse(empty($this->Authors->test_redirectUrl));

		$this->assertFalse($this->Authors->Author->hasAny(array('Author.id' => 1)));
		
		////////////////////////////////
		// 2. try to delete unexisting
		$this->Authors->admin_delete(-30);

		$this->assertTrue($this->Authors->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Authors->test_redirectUrl));
		$this->assertTrue($this->Authors->test_404);
	}
/**
 * testIsAuthorized method
 *
 * @access public
 * @return void
 */
	function testIsAuthorized() {
		$this->Authors->params['controller'] = 'authors';
		$this->Authors->params['action'] = 'admin_index';

		$this->Authors->beforeFilter();
		$this->Authors->Component->startup($this->Authors);
		
		Configure::write('LilBlogs.allowAuthorsAnything', true);
		$result = $this->Authors->isAuthorized();
		$this->assertTrue($result);
		
		Configure::write('LilBlogs.allowAuthorsAnything', false);
		$result = $this->Authors->isAuthorized();
		$this->assertFalse($result);
		
		// authenticate as user 1
		$this->Authors->Session->write('Auth', $this->Authors->Author->read(null, 1));
		$result = $this->Authors->isAuthorized();
		$this->assertTrue($result);
		
		// authenticate as user 2 who is not admin
		$this->Authors->Session->write('Auth', $this->Authors->Author->read(null, 2));
		$result = $this->Authors->isAuthorized();
		$this->assertFalse($result);
	}
/**
 * testLogin method
 *
 * @access public
 * @return void
 */
	function testLogin() {
		$this->Authors->params['controller'] = 'authors';
		$this->Authors->params['action'] = 'login';

		$this->Authors->beforeFilter();
		$this->Authors->Component->startup($this->Authors);
		
		$this->Authors->login();
		$user = $this->Authors->Auth->user();
		$this->assertTrue(empty($user));
		$user = null;
		
		// authenticate as user 1
		$this->Authors->Session->write('Auth', $this->Authors->Author->read(null, 1));
		$this->Authors->login();
		$this->assertFalse(empty($this->Authors->test_redirectUrl));
	}
/**
 * testLogout method
 *
 * @access public
 * @return void
 */
	function testLogout() {
		$this->Authors->params['controller'] = 'authors';
		$this->Authors->params['action'] = 'logout';

		$this->Authors->beforeFilter();
		$this->Authors->Component->startup($this->Authors);
		
		$this->Authors->logout();
		$this->assertTrue($this->Authors->Session->check('Message.auth.message'));
		$this->assertFalse(empty($this->Authors->test_redirectUrl));
	}
} 
?>