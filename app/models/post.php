<?php
/**
 * Short description for post.php
 *
 * Long description for post.php
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
 * @link          www.nahtigal.com
 * @package       famiree
 * @subpackage    famiree.app.models
 * @since         v 1.0
 * @version       $Revision: 90 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-06-10 19:48:08 +0200 (sre, 10 jun 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Post class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class Post extends AppModel {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Post';
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'Logable' => array(
			'userModel' => 'Profile', 
			'userKey' => 'user_id', 
			'change' => 'serialize',
			'description_ids' => false,
			'classField' => 'class',
			'foreignKey' => 'foreign_id'
		),
		'Polymorphic', 'Containable'
	);
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Creator' => array(
			'className'  => 'Profile',
			'foreignKey' => 'creator_id',
		),
	);
/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	var $hasMany = array(
		'PostsLink' => array(
			'dependent'=> true
		)
	);
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'body' => array('rule' => array('minLength', 1))
	);
}
?>