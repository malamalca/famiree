<?php
/* SVN FILE: $Id: post.php 127 2009-07-03 18:06:10Z miha@nahtigal.com $ */
/**
 * Short description for user.php
 *
 * Long description for user.php
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
 * @package       lil_users
 * @subpackage    lil_users.models
 * @since         v 1.0
 * @version       $Revision: 127 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-07-03 20:06:10 +0200 (pet, 03 jul 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Post class
 *
 * @uses          LilUsersAppModel
 * @package       lil_users
 * @subpackage    lil_users.models
 */
class User extends LilUsersAppModel {
/**
 * name property
 *
 * @var string 'User'
 * @access public
 */
	var $name = 'User';
/**
 * displayField property
 *
 * @var string 'username'
 * @access public
 */
	var $displayField = 'username';
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'old_pass' => array(
			'rule'       => 'testOldPassword',
			'required'   => false,
			'allowEmpty' => false,
		),
		'new_pass' =>  array(
			'rule'       => 'testNewPassword',
			'required'   => false,
			'allowEmpty' => false,
		),
	);
/**
 * __construct method
 *
 * @param mixed $id
 * @param mixed $table
 * @param mixed $ds
 * @access private
 * @return void
 */
	function __construct($id = false, $table = null, $ds = null)	{
		if ($table_name = Configure::read('LilUsers.table')) {
			$this->useTable = $table_name;
		}
		$this->validate[Configure::read('LilUsers.fields.password')] = array(
			'empty' => array(
				'rule'       => array('minLength', 1),
				'required'   => false,
				'allowEmpty' => false
			)
		);
		
		parent::__construct($id, $table, $ds);
	}
/**
 * testNewPassword method
 *
 * @param mixed $check
 * @access private
 * @return void
 */
	function testNewPassword($check) {
		$value = array_values($check);
		$value = $value[0];
		return isset($this->data['User'][Configure::read('LilUsers.fields.password')]) && $this->data['User'][Configure::read('LilUsers.fields.password')]==$value; 
	}
/**
 * array_values method
 *
 * @param mixed $check
 * @access private
 * @return void
 */
	function testOldPassword($check) {
		$value = array_values($check);
		$value = $value[0];
		if (isset($this->data['User'][$this->primaryKey])) {
			return $this->hasAny(
				array(
					$this->primaryKey => $this->data['User'][$this->primaryKey],
					Configure::read('LilUsers.fields.password') => $value
				)
			);
		}
		return false;
	}
/**
 * changePassword method
 *
 * @param mixed $check
 * @access private
 * @return void
 */
	function changePassword($data) {
		if (!isset($data['User'][Configure::read('LilUsers.fields.password')])) return false;
		if (!isset($data['User']['new_pass'])) return false;
		if (!isset($data['User']['old_pass'])) return false;
		
		$data['User'][Configure::read('LilUsers.fields.password')] = Security::hash($data['User'][Configure::read('LilUsers.fields.password')], null, true);
		$data['User']['new_pass'] = Security::hash($data['User']['new_pass'], null, true);
		$data['User']['old_pass'] = Security::hash($data['User']['old_pass'], null, true);
		return $this->save($data, array('fieldList' =>
			array(
				$this->primaryKey,
				Configure::read('LilUsers.fields.password'),
				'old_pass',
				'new_pass'
			)
		));
	}
}
?>