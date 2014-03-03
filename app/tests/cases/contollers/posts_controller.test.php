<?php
/* SVN FILE: $Id: posts_controller.test.php 95 2009-06-18 12:53:19Z miha.nahtigal $ */
/**
 * PostsControllerTest file
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
 * @version       $Revision: 95 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-06-18 14:53:19 +0200 (čet, 18 jun 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
require dirname(dirname(__FILE__)).DS.'app_test.php';
/**
 * PostsControllerTest class
 *
 * @package       famiree
 * @subpackage    famiree.tests.cases.controllers
 */
class PostsControllerTest extends AppTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
    var $fixtures = array(
		'app.post', 'app.posts_link', 'app.log', 'app.profile', 'app.union', 'app.unit',
		'app.attachment', 'app.attachments_link', 'app.imgnote');
/**
 * testEdit method
 *
 * @access public
 * @return void
 */
    function testEdit() {
		$result = $this->testAction('/posts/edit', array('return' =>'vars',
			'data'=>array('Post'=>array(
				'id' => 1,
				'title' => 'A new title',
				'body' => 'Edited memory.',
				'referer'=>base64_encode('/profiles/tree/1')
			))));
		$this->assertRedirect('/profiles/tree/1/highlight_post:');
		
		$Post =& ClassRegistry::init('Post');
		$data = $Post->read(null, 1);
		$this->assertEqual($data['Post']['body'], 'Edited memory.');
	}
/**
 * testDelete method
 *
 * @access public
 * @return void
 */
	function testDelete() {
		$result = $this->testAction('/posts/delete/1', array(
			'return' =>'vars', 
		));
		$this->assertRedirect();
		
		$Post =& ClassRegistry::init('Post');
		$this->assertFalse($Post->hasAny('Post.id=1'));
	}

}
?>
