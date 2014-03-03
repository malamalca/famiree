<?php
/* SVN FILE: $Id: posts_link.php 153 2010-01-12 20:14:49Z miha.nahtigal $ */
/**
 * Short description for posts_link.php
 *
 * Long description for posts_link.php
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
 * @version       $Revision: 153 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-12 21:14:49 +0100 (tor, 12 jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 /**
 * Unit class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class PostsLink extends AppModel {
/**
 * name property
 *
 * @var string 'PostsLink'
 * @access public
 */
	var $name = 'PostsLink';
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Post' => array(
			'className' => 'LilBlogs.Post',
			'foreignKey' => 'post_id',
		),
		'Profile' => array(
			'foreignKey' => 'foreign_id',
			'conditions' => array('PostsLink.class' => 'Profile'),
			'type'       => 'INNER',
			'counterCache' => 'cn_mem'
		)
	);
}
?>