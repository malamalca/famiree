<?php
/* SVN FILE: $Id: union_fixture.php 128 2009-12-02 17:16:53Z miha.nahtigal $ */
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
 * UnionFixture class
 *
 * @package       famiree
 * @subpackage    famiree.tests.fixtures
 */
class UnionFixture extends CakeTestFixture {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Union';
/**
 * fields property
 * 
 * field "t"  is an union type
 * t - wife/husband (true marriage)
 * f - fiancee
 * p - partner
 * d - ex-wife/husband - deceased
 * e - ex-wife/husband
 *
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		't' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'dom_d' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'dom_m' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'dom_y' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'loc' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
/**
 * records property
 *
 * @var array
 * @access public
 */
	var $records = array(
		// family 1
		array(
			'id' => 1,
			't' => 't',
			'loc' => 'Ljubljana',
			'dom_y' => '2006',
			'dom_m' => 4,
			'dom_d' => 29,
			'created'  => '2009-03-01 11:22:33',
			'modified'  => '2008-03-01 22:33:44'
		),
		
		// family 2
		array(
			'id' => 2,
			't' => 'p',
			'loc' => NULL,
			'dom_y' => NULL,
			'dom_m' => NULL,
			'dom_d' => NULL,
			'created'  => '2009-03-01 11:22:33',
			'modified'  => '2008-03-01 22:33:44'
		),
		
		// parent family of brother and sister from family 1 and 2
		array(
			'id' => 3,
			't' => 't',
			'loc' => 'Trebnje',
			'dom_y' => '1978',
			'dom_m' => 5,
			'dom_d' => 1,
			'created'  => '2009-03-01 11:22:33',
			'modified'  => '2008-03-01 22:33:44'
		),
		
		// fourth child of family 1 has 3 children of its own
		array(
			'id' => 4,
			't' => 't',
			'loc' => 'Maribor',
			'dom_y' => NULL,
			'dom_m' => NULL,
			'dom_d' => NULL,
			'created'  => '2009-04-01 12:23:53',
			'modified'  => '2008-04-01 12:31:34'
		),
		
		// second marriage for profile 9
		array(
			'id' => 5,
			't' => 't',
			'loc' => 'Slovenska bistrica',
			'dom_y' => NULL,
			'dom_m' => NULL,
			'dom_d' => NULL,
			'created'  => '2009-04-01 12:23:53',
			'modified'  => '2008-04-01 12:31:34'
		),
		
		// family for profile 4
		array(
			'id' => 6,
			't' => 't',
			'loc' => 'Trebnje',
			'dom_y' => '1958',
			'dom_m' => 5,
			'dom_d' => 1,
			'created'  => '2009-03-01 11:22:33',
			'modified'  => '2008-03-01 22:33:44'
		),
		
		// grandparents of profile 4
		array(
			'id' => 7,
			't' => 't',
			'loc' => 'Maribor',
			'dom_y' => '1930',
			'dom_m' => NULL,
			'dom_d' => NULL,
			'created'  => '2009-04-01 12:23:53',
			'modified'  => '2008-04-01 12:31:34'
		),
		
		// grandparents of profile 4
		array(
			'id' => 8,
			't' => 't',
			'loc' => 'Slovenska bistrica',
			'dom_y' => '1932',
			'dom_m' => NULL,
			'dom_d' => NULL,
			'created'  => '2009-04-01 12:23:53',
			'modified'  => '2008-04-01 12:31:34'
		),
		
		// second marriage of profile 4
		array(
			'id' => 9,
			't' => 't',
			'loc' => 'Novo mesto',
			'dom_y' => '1968',
			'dom_m' => NULL,
			'dom_d' => NULL,
			'created'  => '2009-04-01 12:23:53',
			'modified'  => '2008-04-01 12:31:34'
		),
	);
}
?>