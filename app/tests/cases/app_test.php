<?php
/* SVN FILE: $Id: app_test.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
/**
 * AppTestCase file
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
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.nahtigal.com/
 * @package       famiree
 * @subpackage    famiree.tests.cases
 * @version       $Revision: 113 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-08-16 12:09:41 +0200 (ned, 16 avg 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
if (!defined('CAKE_UNIT_TEST_USER_ID')) {
	define('CAKE_UNIT_TEST_USER_ID', '1');
}

if (!class_exists('AppTestCase')) {
/**
 * AppTestCase class
 * 
 * This class is extended from CakeTestCase. It has two main functionalities:
 * 1. assert404 and assertRedirect tests
 * 2. fixture implementation for testAction() controller testing  
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases
 */
class AppTestCase extends CakeTestCase {
/**
 * assert404 method
 *
 * @access public
 * @return void
 */
	function assert404() {
		$this->assertError(true, 'error404');
	}
/**
 * assertRedirect method
 *
 * @param string $url  
 * @access public
 * @return void
 */
	function assertRedirect($url='') {
		if (!empty($url)) {
			$this->assertError(new PatternExpectation('/^redirect:'.
				str_replace('/', '\/', Router::url($url, true)).'/i'));
		} else {
			$this->assertError(new PatternExpectation('/redirect:/i'));
		}
	}
}
}
?>
