<?php
/* SVN FILE: $Id: log.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
/**
 * Short description for log.php
 *
 * Long description for log.php
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
 * @version       $Revision: 113 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-08-16 12:09:41 +0200 (ned, 16 avg 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Log class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class Log extends AppModel {
/**
 * name property
 *
 * @var string 'Log'
 * @access public
 */
	var $name = 'Log';
/**
 * displayField property
 *
 * @var string 'description'
 * @access public
 */
	var $displayField = 'title';
/**
 * name property
 *
 * @var string 'Attachment'
 * @access public
 */
	var $order = 'Log.created DESC';
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'Polymorphic' => array(
			'Imgnote' => array(
				'contain'  => array('Attachment'),
				'recursive' => 0,
			),
			'Memory' => array(
				'contain'  => array('Profile'),
				'recursive' => 0,
			),
			/*'Profile' => array(
				'contain' => array('Modifier'),
				'recursive' => 0,
			)*/
		),
	);
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'User' => array(
			'className'  => 'Profile',
			'foreignKey' => 'user_id'
		)
	);
}
?>