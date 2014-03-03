<?php
/* SVN FILE: $Id: imgnote.php 123 2009-11-29 18:38:55Z miha.nahtigal $ */
/**
 * Short description for imgnote.php
 *
 * Long description for imgnote.php
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
 * @version       $Revision: 123 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-11-29 19:38:55 +0100 (ned, 29 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 /**
 * Unit class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class Imgnote extends AppModel {
/**
 * name property
 *
 * @var string 'Imgnote'
 * @access public
 */
	var $name = 'Imgnote';
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'attachment_id' => array(
			'uuid' => array(
				'rule' => array('custom', '/[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}/'),  
			),
			'exists' => array(
				'rule' => 'checkAttachmentExists',
			)
		),
	);
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'LilLog.LilLog' => array(
			'userModel'       => 'Profile', 
			'userKey'         => 'user_id', 
			'change'          => 'serialize',
			'description_ids' => false,
			'classField'      => 'class',
			'foreignKey'      => 'foreign_id'
		));
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Attachment', 'Profile'
	);
/**
 * checkAttachmentExists method
 *
 * @param mixed $data
 * @access public
 * @return void
 */
	function checkAttachmentExists($data) {
		$value = reset(array_values($data));
		return $this->Attachment->hasAny(array('Attachment.id' => $value));
	}
}
?>