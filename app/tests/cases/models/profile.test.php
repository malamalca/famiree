<?php
/* SVN FILE: $Id: profile.test.php 141 2010-01-04 19:31:18Z miha.nahtigal $ */
/**
 * ProfileTest file
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
 * @subpackage    famiree.tests.cases.models
 * @version       $Revision: 141 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-04 20:31:18 +0100 (pon, 04 jan 2010) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
 /**
 * ProfileTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.models
 */
class ProfileTestCase extends CakeTestCase {
/**
 * Profile property
 *
 * @var object
 * @access public
 */
	var $Profile = null;
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'app.log', 'app.profile',
		'app.post', 'app.posts_link', 'app.comment', 
		'app.attachment', 'app.attachments_link',
		'app.unit', 'app.union', 'app.imgnote',
		'app.setting'
	);
/**
 * start method
 *
 * @access public
 * @return void
 */
	function start() {
		parent::start();
		$this->Profile =& ClassRegistry::init('Profile');
	}
/**
 * testProfileInstance method
 *
 * @access public
 * @return void
 */
	function testProfileInstance() {
		$this->assertTrue(is_a($this->Profile, 'Profile'));
	}
/**
 * testProfileDeleteAttachmentsLink method
 *
 * @access public
 * @return void
 */
	function testProfileDeleteAttachmentsLink() {
		$AttachmentsLink =& ClassRegistry::init('AttachmentsLink');
		$result = $AttachmentsLink->hasAny(array(
			'AttachmentsLink.class' => 'Profile',
			'AttachmentsLink.foreign_id' => 1
		));
		$this->assertTrue($result);
		
		$this->Profile->delete(1);
		
		$result = $AttachmentsLink->hasAny(array(
			'AttachmentsLink.class' => 'Profile',
			'AttachmentsLink.foreign_id' => 1
		));
		$this->assertFalse($result);
	}
/**
 * testFamily method
 *
 * @access public
 * @return void
 */
	function testFamily() {
		$data = $this->Profile->family(1, 'spouses');
		$this->assertEqual($data[0]['Profile']['id'], 2);
		
		$data = $this->Profile->family(1, 'children');
		$this->assertEqual(sizeof($data), 1);
		$this->assertEqual($data[0]['Profile']['id'], 3);
		
		$data = $this->Profile->family(1, 'parents');
		$this->assertEqual(sizeof($data), 0);
		
		$data = $this->Profile->family(1, 'siblings');
		$this->assertEqual(sizeof($data), 3);
		$this->assertEqual(asort(Set::extract('{n}.Profile.id', $data)), array(5,8,9));
	}
/**
 * testFamilyAssoc method
 *
 * @access public
 * @return void
 */
	function testFamilyAssoc() {
		$data = $this->Profile->family(1, 'spouses', true);
		$this->assertEqual(@$data[2]['Profile']['id'], 2);
		
		$data = $this->Profile->family(1, 'children', true);
		$this->assertEqual(sizeof($data), 1);
		$this->assertEqual(@$data[3]['Profile']['id'], 3);
		
		$data = $this->Profile->family(1, 'parents', true);
		$this->assertEqual(sizeof($data), 0);
		
		$data = $this->Profile->family(1, 'siblings', true);
		$this->assertEqual(sizeof($data), 3);
		$this->assertEqual(@$data[5]['Profile']['id'], 5);
		$this->assertEqual(@$data[8]['Profile']['id'], 8);
		$this->assertEqual(@$data[9]['Profile']['id'], 9);
	}
/**
 * testTree1 method
 *
 * @access public
 * @return void
 */
	function testTree1() {
		// parent with one child
		$data = $this->Profile->tree(1);
		$this->assertEqual($data['p'][1]['Profile']['x'], 0);
		
		// parent with two children
		$data = $this->Profile->tree(5);
		$this->assertEqual($data['p'][5]['Profile']['x'], 0);
		
		// parent with no children
		$data = $this->Profile->tree(8);
		$this->assertEqual($data['p'][8]['Profile']['x'], 0);
		
		// single child as bottom-most node
		$data = $this->Profile->tree(3);
		
		$this->assertEqual($data['p'][3]['Profile']['x'], 0);
		$this->assertEqual($data['p'][1]['Profile']['x'], -3);
		$this->assertEqual($data['p'][2]['Profile']['x'], 3);
		
		// two children as bottom-most node
		$data = $this->Profile->tree(6);
		$this->assertEqual($data['p'][6]['Profile']['x'], 0);
		$data = $this->Profile->tree(7);
		$this->assertEqual($data['p'][7]['Profile']['x'], 0);
	}
/**
 * testTree2 method
 *
 * @access public
 * @return void
 */
	function testTree2() {
		$data = $this->Profile->tree(9);
		
		$this->assertEqual($data['p'][9]['Profile']['x'], 0);
		$this->assertEqual($data['p'][$data['u'][4]['p'][1]]['Profile']['x'], 6);
		
		$this->assertEqual($data['p'][10]['Profile']['x'], -3);
		$this->assertEqual($data['p'][11]['Profile']['x'], 3);
		$this->assertEqual($data['p'][12]['Profile']['x'], 9);
		
		$this->assertEqual($data['p'][13]['Profile']['x'], 15);
		$this->assertEqual($data['p'][14]['Profile']['x'], 15);
	}
/**
 * testTree3 method
 *
 * @access public
 * @return void
 */
	function testTree3() {
		$data = $this->Profile->tree(4);
		$this->assertEqual($data['p'][4]['Profile']['x'], 0);
		$this->assertEqual($data['p'][5]['Profile']['x'], 6);
		
		$this->assertEqual($data['p'][15]['Profile']['x'], -3);
		$this->assertEqual($data['p'][16]['Profile']['x'], 3);
		
		$this->assertEqual($data['p'][17]['Profile']['x'], -9);
		$this->assertEqual($data['p'][18]['Profile']['x'], 3);
	}
/**
 * testTree4 method
 *
 * @access public
 * @return void
 */
	function testTree4() {
		$data = $this->Profile->tree(6);
		$this->assertEqual($data['p'][6]['Profile']['x'], 0);
		$this->assertEqual($data['p'][7]['Profile']['x'], 6);
		
		$this->assertEqual($data['p'][4]['Profile']['x'], -3);
		$this->assertEqual($data['p'][5]['Profile']['x'], 3);
		
		$this->assertEqual($data['p'][16]['Profile']['x'], -6);
		$this->assertEqual($data['p'][15]['Profile']['x'], -18);
		
		$this->assertEqual($data['p'][18]['Profile']['x'], -9);
		
		$this->assertEqual($data['p'][17]['Profile']['x'], -21);
		
		/////
		$this->assertEqual($data['p'][19]['Profile']['x'], 15);
	}
}
?>