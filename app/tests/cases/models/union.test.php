<?php
/* SVN FILE: $Id: union.test.php 69 2009-05-27 12:48:15Z miha.nahtigal $ */
/**
 * UnionTest file
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
 * @version       $Revision: 69 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-05-27 14:48:15 +0200 (sre, 27 maj 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
App::import('Model', 'Union');
/**
 * UnionTestCase class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.models
 */
class UnionTestCase extends CakeTestCase {
/**
 * Union property
 *
 * @var object
 * @access public
 */
	var $Union = null;
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'app.log', 'app.profile', 'app.setting',
		'app.post', 'app.posts_link', 'app.comment', 'app.attachment', 'app.attachments_link',
		'app.unit', 'app.union', 'app.imgnote'
	);
/**
 * start method
 *
 * @param string $method 
 * @access public
 * @return void
 */
	function start() {
		parent::start();
		$this->Union =& ClassRegistry::init('Union');
	}
/**
 * testUnionInstance method
 *
 * @access public
 * @return void
 */
	function testUnionInstance() {
		$this->assertTrue(is_a($this->Union, 'Union'));
	}
/**
 * testUnionFetchAsChild method
 *
 * @access public
 * @return void
 */
	function testUnionFetchAsChild() {
		$expected = array(
			'3' => array(
				'p' => array(),
				'c' => array('1', '5', '8', '9'),
			)
		);
		$this->assertEqual($this->Union->fetchAsChild2(1), $expected);
		
		$expected = array(
			'2' => array(
				'p' => array('4', '5'),
				'c' => array('6', '7'),
			)
		);
		$this->assertEqual($this->Union->fetchAsChild2(6), $expected);
	}
}
?>