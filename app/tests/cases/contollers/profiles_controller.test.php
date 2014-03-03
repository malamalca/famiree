<?php
/* SVN FILE: $Id: profiles_controller.test.php 104 2009-07-05 18:07:47Z miha.nahtigal $ */
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
 * @version       $Revision: 104 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-07-05 20:07:47 +0200 (ned, 05 jul 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
require dirname(dirname(__FILE__)).DS.'app_test.php';
/**
 * ProfilesControllerTest class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class ProfilesControllerTest extends AppTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array('app.log', 'app.profile', 'app.union', 'app.unit', 
		'app.post', 'app.posts_link', 'app.comment', 
		'app.attachment', 'app.attachments_link', 'app.imgnote');
/**
 * testView method
 *
 * @access public
 * @return void
 */
	function testView() {
		$result = $this->testAction('/profiles/view/1', array('return' =>'vars'));
	}
/**
 * testEdit method
 *
 * @access public
 * @return void
 */
	function testEdit() {
		$Profile =& ClassRegistry::init('Profile');
		
		$result = $this->testAction('/profiles/edit/1', array('return' =>'vars',
			'data'=>array('Profile'=>array('id'=>null, 'ln'=>'test', 'fn'=>'First', 'l'=>1,
				'referer'=>base64_encode('/profiles/tree/1')))));
		
		$profile_id = $Profile->getLastInsertID();
		$this->assertRedirect('/profiles/tree/1/highlight:');
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
		
		$result = $this->testAction('/profiles/edit/3', array('return' =>'vars',
			'data'=>array(
				'Profile' => array(
					'id'=>null, 
					'ln'=>'test', 
					'fn'=>'First', 
					'l'=>1,
					'referer'=>base64_encode('/profiles/tree/1')
				),
				'Union' => array(
					0 => array(
						'id' => 1,
						't' => 'p',
						'loc' => 'Maribor',
					)
				)
			)
		));
		$this->assertRedirect('/profiles/tree/1/highlight:');
		
		$union_count_after = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => 1)));
		$this->AssertEqual($union_count_before, $union_count_after);
		
		$data = $Profile->Union->read(null, 1);
		$this->AssertEqual($data['Union']['loc'], 'Maribor');
		
	}
/**
 * testAddChild method
 *
 * @access public
 * @return void
 */
	function testAddChild() {
		$Profile =& ClassRegistry::init('Profile');
		
		/* --- EXISTING UNION ------------------------------------------------------- */
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => 1)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$result = $this->testAction('/profiles/add_child/1', array('return' =>'vars',
			'data'=>array(
				'Profile' => array(
					'id' => null, 
					'ln' => 'test', 
					'fn' => 'First', 
					'l' => 1,
					'referer' => base64_encode('/profiles/tree/1'),
					'parent_id' => 1,
				),
				'Union' => array(
					'id' => 1
				),
				'Unit' => array(
					'kind' => 'c'
				)
			)
		));
		$profile_id = $Profile->getLastInsertID();
		$this->assertRedirect('/profiles/tree/1/highlight:');
		
		// test creator id
		$data = $Profile->read(null, $profile_id);
		$this->assertEqual($data['Profile']['creator_id'], 1);
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => 1)));
		$this->AssertEqual($union_count_before, $union_count_1);
		
		$unit_count_1 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_before + 1, $unit_count_1);
		
		$profile_count_1 = $Profile->find('count');
		$this->AssertEqual($profile_count_before + 1, $profile_count_1);
		
		/* --- NON EXISTING UNION ------------------------------------------------------- */
		$target_parent_id = 8;
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		
		$result = $this->testAction('/profiles/add_child/'.$target_parent_id, 
			array('return' =>'vars',
				'data'=>array(
					'Profile' => array(
						'id' => null, 
						'ln' => 'test', 
						'fn' => 'Second', 
						'l' => 1,
						'referer' => base64_encode('/profiles/tree/'.$target_parent_id),
						'parent_id' => $target_parent_id,
					),
					'Unit' => array(
						'kind' => 'c'
					)
				)
			)
		);
		$this->assertRedirect('/profiles/tree/'.$target_parent_id.'/highlight:');
		
		$union_count_after = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$this->AssertEqual($union_count_1 + 1, $union_count_after);
		
		$unit_count_after = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_1 + 2, $unit_count_after);
		
		$profile_count_after = $Profile->find('count');
		$this->AssertEqual($profile_count_1 + 1, $profile_count_after);
	}
/**
 * testAddSibling method
 *
 * @access public
 * @return void
 */
	function testAddSibling() {
		$Profile =& ClassRegistry::init('Profile');
		
		/* --- UNION ALWAYS EXIST ----------------------------------------------------- */
		$target_sibling_id = 8;
		$target_union_id = 3;
		
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_sibling_id)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$siblings_count = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.union_id' => $target_union_id)
		));
		
		$result = $this->testAction('/profiles/add_sibling/' . $target_sibling_id, 
			array('return' =>'vars',
				'data'=>array(
					'Profile' => array(
						'id' => null, 
						'ln' => 'test', 
						'fn' => 'First', 
						'l' => 1,
						'referer' => base64_encode('/profiles/tree/' . $target_sibling_id),
						'parent_id' => $target_sibling_id,
					),
					'Union' => array(
						'id' => $target_union_id
					),
					'Unit' => array(
						'kind' => 'c'
					)
				)
			)
		);
        $profile_id = $Profile->getLastInsertID();
		$this->assertRedirect('/profiles/tree/' . $target_sibling_id . '/highlight:');
		
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
 * testAddPartner method
 *
 * @access public
 * @return void
 */
	function testAddPartner() {
		$Profile =& ClassRegistry::init('Profile');
		
		/* --- NON EXISTING UNION ------------------------------------------------------- */
		$target_parent_id = 8;
		
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$result = $this->testAction('/profiles/add_partner/'.$target_parent_id, 
			array('return' =>'vars',
				'data'=>array(
					'Profile' => array(
						'id' => null, 
						'ln' => 'test', 
						'fn' => 'First', 
						'l' => 1,
						'referer' => base64_encode('/profiles/tree/'.$target_parent_id),
						'parent_id' => $target_parent_id,
					),
					'Unit' => array(
						'kind' => 'p'
					)
				)
			)
		);
        $profile_id = $Profile->getLastInsertID();
		$this->assertRedirect('/profiles/tree/'.$target_parent_id.'/highlight:');
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));
		$this->AssertEqual($union_count_before + 1, $union_count_1);
		
		$unit_count_1 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_before + 2, $unit_count_1);
		
		$profile_count_1 = $Profile->find('count');
		$this->AssertEqual($profile_count_before + 1, $profile_count_1);

		/* --- ADD TO EXISTING UNION ------------------------------------------------------- */
        $target_parent_id = 9;

        $union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_parent_id)));

		$result = $this->testAction('/profiles/add_partner/'.$target_parent_id,
			array('return' =>'vars',
				'data'=>array(
					'Profile' => array(
						'id' => null,
						'ln' => 'test',
						'fn' => 'Second',
						'l' => 1,
						'referer' => base64_encode('/profiles/tree/'.$target_parent_id),
						'parent_id' => $target_parent_id,
					),
					'Unit' => array(
						'kind' => 'p'
					),
					'Union' => array(
						'id' => 4
					),
				)
			)
		);
        $profile_id = $Profile->getLastInsertID();
        $this->assertRedirect('/profiles/tree/'.$target_parent_id.'/highlight:');

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
	function testAddParent() {
		$Profile =& ClassRegistry::init('Profile');
		
		/* --- ADD TO EXISTING UNION ------------------------------------------------------- */
		$target_child_id = 1;
		$target_union_id = 3;
		
		$union_count_before = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_child_id)));
		$unit_count_before = $Profile->Unit->find('count');
		$profile_count_before = $Profile->find('count');
		
		$result = $this->testAction('/profiles/add_parent/'.$target_child_id, 
			array('return' =>'vars',
				'data'=>array(
					'Profile' => array(
						'id' => null, 
						'ln' => 'test', 
						'fn' => 'First', 
						'l' => 1,
						'referer' => base64_encode('/profiles/tree/'.$target_child_id),
						'parent_id' => $target_child_id,
					),
					'Union' => array(
						'id' => $target_union_id
					),
					'Unit' => array(
						'kind' => 'p'
					)
				)
			)
		);
        $profile_id = $Profile->getLastInsertID();
		$this->assertRedirect('/profiles/tree/'.$target_child_id.'/highlight:');
		
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_child_id)));
		$this->AssertEqual($union_count_before, $union_count_1);
		
		$unit_count_1 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_before + 1, $unit_count_1);
		
		$profile_count_1 = $Profile->find('count');
		$this->AssertEqual($profile_count_before + 1, $profile_count_1);
		
		unset($Profile);
		/* --- NON EXISTING UNION ------------------------------------------------------- */
		$Profile =& ClassRegistry::init('Profile');
		$union_count_1 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $profile_id)));
		
		$result = $this->testAction('/profiles/add_parent/'.$profile_id, 
			array('return' =>'vars',
				'data'=>array(
					'Profile' => array(
						'id' => null, 
						'ln' => 'test', 
						'fn' => 'Second', 
						'l' => 1,
						'referer' => base64_encode('/profiles/tree/'.$profile_id),
						'parent_id' => $profile_id,
					),
					'Unit' => array(
						'kind' => 'p'
					)
				)
			)
		);
		
		$new_profile_id = $Profile->getLastInsertID();
		$new_union_id = $Profile->Union->getLastInsertID();
		$this->assertRedirect('/profiles/tree/'.$profile_id.'/highlight:'.$new_profile_id);
		
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
		
		$union_count_2 = $Profile->Unit->find('count', array(
			'conditions' => array('Unit.profile_id' => $target_child_id)));
		$this->AssertEqual($union_count_1 + 1, $union_count_2);
		
		$unit_count_2 = $Profile->Unit->find('count');
		$this->AssertEqual($unit_count_1 + 2, $unit_count_2);
		
		$profile_count_2 = $Profile->find('count');
		$this->AssertEqual($profile_count_1 + 1, $profile_count_2);
	}
}
?>
