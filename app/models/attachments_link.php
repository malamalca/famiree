<?php
/* SVN FILE: $Id: attachments_link.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
/**
 * Short description for attachments_link.php
 *
 * Long description for attachments_link.php
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
 * Unit class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class AttachmentsLink extends AppModel {
/**
 * name property
 *
 * @var string 'AttachmentsLink'
 * @access public
 */
	var $name = 'AttachmentsLink';
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Attachment',
		'Profile' => array(
			'foreignKey'   => 'foreign_id',
			'conditions'   => 'AttachmentsLink.class = \'Profile\'',
			'type'         => 'INNER',
			'counterCache' => 'cn_med'
		)
	);
}
?>