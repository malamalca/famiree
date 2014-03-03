<?php
/* SVN FILE: $Id: categories_controller.test.php 193 2009-11-29 16:33:35Z miha@nahtigal.com $ */
/**
 * Short description for categories_controller.test.php
 *
 * Long description for categories_controller.test.php
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
App::import('Controller', 'LilBlogs.Categories');
/**
 * TestCategoriesController class
 *
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.controllers
 */
class TestCategoriesController extends CategoriesController {
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
 * CategoriesControllerTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class CategoriesControllerTestCase extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'plugin.lil_blogs.category', 'plugin.lil_blogs.blog', 'plugin.lil_blogs.author',
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
		$this->Categories =& new TestCategoriesController();
		$this->Categories->constructClasses();
		$this->Categories->Component->initialize($this->Categories);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Categories->Session->destroy();
		unset($this->Categories);
		ClassRegistry::flush();
	}
/**
 * testAdminIndex method
 *
 * @access public
 * @return void
 */
	function testAdminIndex() {
		$this->Categories->params['controller'] = 'categories';
		$this->Categories->params['action'] = 'admin_index';
		$this->Categories->params['prefix'] = 'admin';
		$this->Categories->params['admin.blog_id'] = 1;
		
		$this->Categories->beforeFilter();
		$this->Categories->Component->startup($this->Categories);
		
		$this->Categories->admin_index();
		$this->assertTrue(empty($this->Categories->Category->id));
	}
/**
 * testAdminAdd method
 *
 * @access public
 * @return void
 */
	function testAdminAdd() {
		$this->Categories->params['controller'] = 'categories';
		$this->Categories->params['action'] = 'admin_add';
		$this->Categories->params['prefix'] = 'admin';
		$this->Categories->params['admin.blog_id'] = 1;

		$this->Categories->beforeFilter();
		$this->Categories->Component->startup($this->Categories);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal add
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Categories->data = array(
			'blog_id' => 1,
			'name'    => 'New Category From TestSuite'
	    );
		
		$this->Categories->admin_add();
		
		//assert that some sort of session flash was set.
		$this->assertTrue($this->Categories->Session->check('Message.flash.message'));
		
		$expected_url = Router::normalize($this->Categories->test_redirectUrl);
		$this->assertEqual(Router::normalize(array('action' => 'admin_index')), $expected_url);

		// a new setting has been added
		$this->assertFalse(empty($this->Categories->Category->id));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. check normal add
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Categories->test_reset();
		$this->Categories->data = null;
		$this->Categories->admin_add();
		$this->assertFalse($this->Categories->test_404);
		$this->assertTrue(empty($this->Categories->test_redirectUrl));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 3. Check invalid data
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Categories->test_reset();
		$this->Categories->data = array(
			'Category' => array(
				'blog_id' => 1,
				'name'    => ''
			)
	    );
	    
	    $this->Categories->admin_add();
	    
	    $this->assertEqual($this->Categories->Session->read('Message.flash.layout'), 'error');
	    $this->assertFalse($this->Categories->test_404);
	}
/**
 * testAdminEdit method
 *
 * @access public
 * @return void
 */
	function testAdminEdit() {
		$this->Categories->params['controller'] = 'categories';
		$this->Categories->params['action'] = 'admin_edit';
		$this->Categories->params['prefix'] = 'admin';
		$this->Categories->params['admin.blog_id'] = 1;

		$this->Categories->beforeFilter();
		$this->Categories->Component->startup($this->Categories);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal edit
		////////////////////////////////////////////////////////////////////////////////////////////
		// check for existing value
		$this->assertEqual('Programming', $this->Categories->Category->field('name', array('Category.id' => 1)));

		$this->Categories->data = array(
			'Category' => array(
			    'id' => 1,
				'blog_id' => 1,
				'name'    => 'Computers'
			)
	    );

		$this->Categories->admin_edit(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Categories->Session->check('Message.flash.message'));

		$expected_url = Router::normalize($this->Categories->test_redirectUrl);
		$this->assertEqual(Router::normalize(array('action' => 'admin_index')), $expected_url);

		// a new setting has been added
		$this->assertEqual('Computers', $this->Categories->Category->field('name', array('Category.id' => 1)));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. check redirect on invalid category
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Categories->test_reset();
		$this->Categories->data = null;
		
		$this->Categories->admin_edit(-30);
		$this->assertTrue($this->Categories->test_404);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 3. Check invalid data
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Categories->test_reset();
		$this->Categories->data = array(
			'Category' => array(
			    'id' => 1,
				'blog_id' => 1,
				'name'    => ''
			)
	    );
	    
	    $this->Categories->admin_edit(1);
	    
	    $this->assertEqual($this->Categories->Session->read('Message.flash.layout'), 'error');
	    $this->assertFalse($this->Categories->test_404);
	}
/**
 * testAdminDelete method
 *
 * @access public
 * @return void
 */
	function testAdminDelete() {
		$this->Categories->params['controller'] = 'categories';
		$this->Categories->params['action'] = 'admin_delete';
		$this->Categories->params['prefix'] = 'admin';
		$this->Categories->params['admin.blog_id'] = 1;

		$this->Categories->beforeFilter();
		$this->Categories->Component->startup($this->Categories);
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 1. check normal delete
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->assertTrue($this->Categories->Category->hasAny(array('Category.id' => 1)));

		$this->Categories->admin_delete(1);

		//assert that some sort of session flash was set.
		$this->assertTrue($this->Categories->Session->check('Message.flash.message'));

		// redirect url must not be empty
		$this->assertFalse(empty($this->Categories->test_redirectUrl));

		$this->assertFalse($this->Categories->Category->hasAny(array('Category.id' => 1)));
		
		////////////////////////////////////////////////////////////////////////////////////////////
		// 2. try to delete unexisting
		////////////////////////////////////////////////////////////////////////////////////////////
		$this->Categories->admin_delete(-30);

		$this->assertTrue($this->Categories->Session->check('Message.flash.message'));
		$this->assertFalse(empty($this->Categories->test_redirectUrl));
		$this->assertTrue($this->Categories->test_404);
	}
/**
 * testIsAuthorized method
 *
 * @access public
 * @return void
 */
	function testIsAuthorized() {
		$this->Categories->params['controller'] = 'blogs';
		$this->Categories->params['action'] = 'admin_edit';

		$this->Categories->beforeFilter();
		$this->Categories->Component->startup($this->Categories);
		
		Configure::write('LilBlogs.allowAuthorsAnything', true);
		$result = $this->Categories->isAuthorized();
		$this->assertTrue($result);
		
		Configure::write('LilBlogs.allowAuthorsAnything', false);
		$result = $this->Categories->isAuthorized();
		$this->assertFalse($result);
		
		$this->Categories->params['admin.blog_id'] = 1;
		$result = $this->Categories->isAuthorized();
		$this->assertFalse($result);
		
		// authenticate as user 1
		$this->Categories->Session->write('Auth', $this->Categories->Category->Blog->Author->read(null, 1));
		$result = $this->Categories->isAuthorized();
		$this->assertFalse($result);
		
		$this->Categories->params['pass'][0] = 3;
		$result = $this->Categories->isAuthorized();
		$this->assertFalse($result);
		
		$this->Categories->params['pass'][0] = 1;
		$result = $this->Categories->isAuthorized();
		$this->assertTrue($result);
		
		// authenticate as user 2 which is not admin
		$this->Categories->params['admin.blog_id'] = 2;
		$this->Categories->params['pass'][0] = 3;
		$this->Categories->Session->write('Auth', $this->Categories->Category->Blog->Author->read(null, 2));
		$result = $this->Categories->isAuthorized();
		$this->assertTrue($result);
		
		$this->Categories->params['pass'][0] = 2;
		$this->Categories->params['admin.blog_id'] = 1;
		$result = $this->Categories->isAuthorized();
		$this->assertFalse($result);
	}
} 
?>