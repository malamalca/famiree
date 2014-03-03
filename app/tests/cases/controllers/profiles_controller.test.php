<?php
/* SVN FILE: $Id: profiles_controller.test.php 156 2010-01-15 14:26:08Z miha.nahtigal $ */
/**
 * ProfilesControllerTest file
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 * Licensed under The Open Group Test Suite License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          http://www.nahtigal.com/
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 * @version       $Revision: 156 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-15 15:26:08 +0100 (pet, 15 jan 2010) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
// first, overwrite configuration with default values
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'config' . DS .'core.php';
// import default controller
App::import('Controller', 'Profiles');
/**
 * TestProfilesController class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class TestProfilesController extends ProfilesController {
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
 * ProfilesControllerTest class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class ProfilesControllerTest extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array('app.log', 'app.profile', 'app.union', 'app.unit', 
		'app.post', 'app.posts_link', 'app.comment', 
		'app.attachment', 'app.attachments_link', 'app.imgnote', 'app.setting');
/**
 * startTest method
 *
 * @access public
 * @return void
 */
	function startTest() {
		$this->Profiles =& new TestProfilesController();
		$this->Profiles->constructClasses();
		$this->Profiles->Component->initialize($this->Profiles);
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest() {
		$this->Profiles->Session->destroy();
		unset($this->Profiles);
		ClassRegistry::flush();
	}
/**
 * testView method
 *
 * @access public
 * @return void
 */
	function testView() {
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'view';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->view('1');
		
		$this->assertFalse($this->Profiles->test_404);
		$this->assertEqual($this->Profiles->viewVars['profile']['Profile']['id'], 1);
	}
/**
 * testViewUnexistant method
 *
 * @access public
 * @return void
 */
	function testViewUnexistant() {
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'view';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		$this->Profiles->view('23434');
		
		$this->assertTrue($this->Profiles->test_404);
	}
/**
 * testEdit method
 *
 * @access public
 * @return void
 */
	function testEdit() {
		$Profile =& ClassRegistry::init('Profile');
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'edit';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => 1,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1')
			)
		);
		$this->Profiles->edit('1');
		
		$this->assertFalse($this->Profiles->test_404);
		
		$data = $Profile->read(null, 1);
		$this->assertEqual($data['Profile']['d_n'], 'First Test');
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:1', true));
	}
/**
 * testEditWithUnion method
 *
 * @access public
 * @return void
 */
	function testEditWithUnion() {
		$Profile =& ClassRegistry::init('Profile');
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => 1)));
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'edit';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => 1,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1')
			),
			'Union' => array(
				0 => array(
					'id'  => 1,
					't'   => 'p',
					'loc' => 'Maribor',
				)
			)
		);
		$this->Profiles->edit('1');
		
		$this->assertFalse($this->Profiles->test_404);
		
		$union_count_after = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => 1)));
			
		$this->assertEqual($union_count_before, $union_count_after);
		
		$data = $Profile->read(null, 1);
		$this->assertEqual($data['Profile']['d_n'], 'First Test');
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:1', true));
		
		$data = $Profile->Union->field('loc', array('Union.id' => 1));
		$this->assertEqual($data, 'Maribor');
	}
/**
 * testAddChildToExistingUnion method
 *
 * @access public
 * @return void
 */
	function testAddChildToExistingUnion() {
		$Profile =& ClassRegistry::init('Profile');
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => 1)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'add_child';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => null,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1')
			),
			'Union' => array(
				'id' => 1
			),
			'Unit' => array(
				'kind' => 'c'
			)
		);
		$this->Profiles->add_child('1');
		
		$profile_id = $Profile->getLastInsertID();
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:' . $profile_id, true));
		
		// test creator id
		$data = $Profile->read(null, $profile_id);
		$this->assertEqual($data['Profile']['creator_id'], 1);
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => 1)));
		$this->assertEqual($union_count_before, $union_count_1);
		
		$unit_count_1 = $Profile->Unit->find('count');
		$this->assertEqual($unit_count_before + 1, $unit_count_1);
		
		$profile_count_1 = $Profile->find('count');
		$this->assertEqual($profile_count_before + 1, $profile_count_1);
	}
/**
 * testAddChildToNonExistingUnion method
 *
 * @access public
 * @return void
 */
	function testAddChildToNonExistingUnion() {
		$target_parent_id = 8;
		
		$Profile =& ClassRegistry::init('Profile');
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id )));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'add_child';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => null,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'Second',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/'. $target_parent_id),
				'parent_id' => $target_parent_id,
			),
			'Unit' => array(
				'kind' => 'c'
			)
		);
		$this->Profiles->add_child($target_parent_id);
		
		$profile_id = $Profile->getLastInsertID();
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/' . $target_parent_id . '/highlight:' . $profile_id, true));
		
		$union_count_after = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$this->AssertEqual($union_count_before + 1, $union_count_after);
		
		$unit_count_after = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_before + 2, $unit_count_after);
		
		$profile_count_after = $Profile->find('count');
		$this->AssertEqual($profile_count_before + 1, $profile_count_after);
	}
/**
 * testAddSibling method
 *
 * @access public
 * @return void
 */
	function testAddSibling() {
		$Profile =& ClassRegistry::init('Profile');
		
		$target_sibling_id = 8;
		$target_union_id = 3;
		
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_sibling_id)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$siblings_count = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.union_id' => $target_union_id)
		));
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'add_sibling';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => null,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1'),
				'parent_id' => $target_sibling_id,
			),
			'Union' => array(
				'id' => $target_union_id
			),
			'Unit' => array(
				'kind' => 'c'
			)
		);
		$this->Profiles->add_sibling($target_sibling_id);
		
		$profile_id = $Profile->getLastInsertID();
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:' . $profile_id, true));
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_sibling_id)));
		$this->AssertEqual($union_count_before, $union_count_1);
		
		$unit_count_1 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_before + 1, $unit_count_1);
		
		$profile_count_1 = $Profile->find('count');
		$this->AssertEqual($profile_count_before + 1, $profile_count_1);
		
		// check that new sibling exists
		$siblings_new_count = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.union_id' => $target_union_id)
		));
		$this->assertEqual($siblings_new_count, $siblings_count + 1);
	}
/**
 * testAddPartnerToNonExistingUnion method
 *
 * @access public
 * @return void
 */
	function testAddPartnerToNonExistingUnion() {
		$target_parent_id = 8;
		
		$Profile =& ClassRegistry::init('Profile');
		
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'add_partner';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => null,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1'),
				'parent_id' => $target_parent_id,
			),
			'Unit' => array(
				'kind' => 'p'
			)
		);
		$this->Profiles->add_partner($target_parent_id);
		
		$profile_id = $Profile->getLastInsertID();
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:' . $profile_id, true));
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$this->AssertEqual($union_count_before + 1, $union_count_1);
		
		$unit_count_1 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_before + 2, $unit_count_1);
		
		$profile_count_1 = $Profile->find('count');
		$this->AssertEqual($profile_count_before + 1, $profile_count_1);
	}
/**
 * testAddPartnerToExistingUnion method
 *
 * @access public
 * @return void
 */
	function testAddPartnerToExistingUnion() {
		$target_parent_id = 9;
		
		$Profile =& ClassRegistry::init('Profile');
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$unit_count_1 = $Profile->Unit->find('count');
		$profile_count_1 = $Profile->find('count');
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'add_partner';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => null,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1'),
				'parent_id' => $target_parent_id,
			),
			'Unit' => array(
				'kind' => 'p'
			),
			'Union' => array(
				'id' => 4
			),
		);
		$this->Profiles->add_partner($target_parent_id);
		
		$profile_id = $Profile->getLastInsertID();
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:' . $profile_id, true));
		
		$union_count_2 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$this->AssertEqual($union_count_1, $union_count_2);

		$unit_count_2 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_1 + 1, $unit_count_2);

		$profile_count_2 = $Profile->find('count');
		$this->AssertEqual($profile_count_1 + 1, $profile_count_2);
	}
/**
 * testAddParent method
 *
 * @access public
 * @return void
 */
 /**
 * testAddParentToExistingUnion method
 *
 * @access public
 * @return void
 */
	function testAddParentToExistingUnion() {
		$target_child_id = 1;
		$target_union_id = 3;
		
		$Profile =& ClassRegistry::init('Profile');
		
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_child_id)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'add_parent';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => null,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1'),
				'parent_id' => $target_child_id,
			),
			'Unit' => array(
				'kind' => 'p'
			),
			'Union' => array(
				'id' => $target_union_id
			),
		);
		$this->Profiles->add_parent($target_child_id);
		
		$profile_id = $Profile->getLastInsertID();
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:' . $profile_id, true));
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_child_id)));
		$this->AssertEqual($union_count_before, $union_count_1);
		
		$unit_count_1 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_before + 1, $unit_count_1);
		
		$profile_count_1 = $Profile->find('count');
		$this->AssertEqual($profile_count_before + 1, $profile_count_1);
	}
/**
 * testAddParentToNonExistingUnion method
 *
 * @access public
 * @return void
 */
	function testAddParentToNonExistingUnion() {
		$profile_id = 18;
		
		$Profile =& ClassRegistry::init('Profile');
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $profile_id)));
		$unit_count_1 = $Profile->Unit->find('count');
		$profile_count_1 = $Profile->find('count');
		
		$this->Profiles->Session->write('Auth.User', array(
			'id'  => 1,
			'd_n' => 'John Doe',
		));

		$this->Profiles->params['action'] = 'add_parent';
		$this->Profiles->beforeFilter();
		$this->Profiles->Component->startup($this->Profiles);
		
		$this->Profiles->data = array(
			'Profile' => array(
				'id' => null,
				'ln' => 'Test',
				'mn' => '',
				'fn' => 'First',
				'l'  => 1,
				'referer' => base64_encode('/profiles/tree/1'),
				'parent_id' => $profile_id,
			),
			'Unit' => array(
				'kind' => 'p'
			)
		);
		$this->Profiles->add_parent($profile_id);
		
		$new_profile_id = $Profile->getLastInsertID();
		$new_union_id = $Profile->Union->getLastInsertID();
		$this->assertEqual(Router::url($this->Profiles->test_redirectUrl, true), Router::url('/profiles/tree/1/highlight:' . $new_profile_id, true));
		
		$result = $Profile->Unit->find('count', array('conditions' => array(
			'Unit.profile_id' => $profile_id,
			'Unit.kind' => 'c',
			'Unit.union_id' => $new_union_id
		)));
		$this->AssertEqual($result, 1);
		
		$result = $Profile->Unit->find('count', array('conditions' => array(
			'Unit.profile_id' => $new_profile_id,
			'Unit.kind' => 'p',
			'Unit.union_id' => $new_union_id
		)));
		$this->AssertEqual($result, 1);
		
		$unit_count_2 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_1 + 2, $unit_count_2);
		
		$profile_count_2 = $Profile->find('count');
		$this->AssertEqual($profile_count_1 + 1, $profile_count_2);
	}
}
?>