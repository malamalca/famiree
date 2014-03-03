<?php
/* SVN FILE: $Id: unit_fixture.php 128 2009-12-02 17:16:53Z miha.nahtigal $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          http://www.nahtigal.com/
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 * @version       $Revision: 128 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-12-02 18:16:53 +0100 (sre, 02 dec 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * UnitFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class UnitFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Unit';
/**
 * fields property
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'union_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'profile_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'kind' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'sort_order' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'IX_UNION' => array('column' => 'union_id', 'unique' => 0), 'IX_PROFILE' => array('column' => array('profile_id', 'kind'), 'unique' => 0))
	);
/**
 * records property
 * 	
 * 						---------		---------
 * 						|	NN	| --3--	|	NN	|
 * 						---------	|	---------	
 * 									|
 * 		---------------------------------------------------------------------------------
 * 		|												|				|				|
 * 	---------		---------		---------		---------		---------		-----------------------------5-----------
 * 	|	1	| --1--	|	2	|		|	4	| --2--	|	5	|		|	8	|		|	9	| --4--	|	NN	|		|	13	|
 * 	---------	|	---------		---------	|	---------		---------		---------	|	---------		--------
 * 				|						|---------------|							|-----------|-----------|			|
 * 			---------				---------		---------					---------	---------	---------	--------
 * 			|	3	|				|	6	|		|	7	|					|	10	|	|	11	|	|	12	|	|	14	|
 * 			---------				---------		---------					---------	---------	---------	--------
 *
 *	--------		--------		--------		--------
 *	|	17	|---7---|	NN	|		|	18	|---8---|	NN	|
 *	--------	|	---------		---------	|	---------
 *				|								|
 *			--------						--------
 *			|	15	|-----------6----------|	16	|
 *			--------			|			--------
 *							--------		--------		--------
 *							|	4	| --2--	|	5	| --9--	|	19	|
 *							--------		--------		--------
 *
 * @var array
 * @access public
 */
	var $records = array(
		array('id'=>1, 'union_id'=>1, 'profile_id'=>1, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>2, 'union_id'=>1, 'profile_id'=>2, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>3, 'union_id'=>1, 'profile_id'=>3, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>4, 'union_id'=>2, 'profile_id'=>4, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>5, 'union_id'=>2, 'profile_id'=>5, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>6, 'union_id'=>2, 'profile_id'=>6, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>7, 'union_id'=>2, 'profile_id'=>7, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>8, 'union_id'=>3, 'profile_id'=>1, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>9, 'union_id'=>3, 'profile_id'=>5, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>10, 'union_id'=>3, 'profile_id'=>8, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>11, 'union_id'=>3, 'profile_id'=>9, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>12, 'union_id'=>4, 'profile_id'=>9, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>13, 'union_id'=>4, 'profile_id'=>10, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>14, 'union_id'=>4, 'profile_id'=>11, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>15, 'union_id'=>4, 'profile_id'=>12, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>16, 'union_id'=>5, 'profile_id'=>9, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>17, 'union_id'=>5, 'profile_id'=>13, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>18, 'union_id'=>5, 'profile_id'=>14, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>19, 'union_id'=>6, 'profile_id'=>15, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>20, 'union_id'=>6, 'profile_id'=>16, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>21, 'union_id'=>6, 'profile_id'=>4, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>22, 'union_id'=>7, 'profile_id'=>17, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>23, 'union_id'=>7, 'profile_id'=>15, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>24, 'union_id'=>8, 'profile_id'=>18, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>25, 'union_id'=>8, 'profile_id'=>16, 'kind'=>'c', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		
		array('id'=>26, 'union_id'=>9, 'profile_id'=>4, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
		array('id'=>27, 'union_id'=>9, 'profile_id'=>19, 'kind'=>'p', 'sort_order'=>NULL, 'created'=>'2009-03-01 11:22:33', 'modified'  => '2009-03-01 11:22:33'),
	);
}
?>