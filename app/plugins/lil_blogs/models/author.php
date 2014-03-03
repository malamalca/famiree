<?php
/* SVN FILE: $Id: author.php 181 2009-10-18 19:18:48Z miha@nahtigal.com $ */
/**
 * Short description for author.php
 *
 * Long description for author.php
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
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 * @since         v 1.0
 * @version       $Revision: 181 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-18 21:18:48 +0200 (ned, 18 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Author class
 *
 * @uses          LilBlogsAppModel
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 */
class Author extends LilBlogsAppModel {
/**
 * name property
 *
 * @var string 'Author'
 * @access public
 */
	var $name = 'Author';
/**
 * displayField property
 *
 * @var string 'name'
 * @access public
 */
	var $displayField = 'name';
/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	var $hasMany = array(
		'Post' => array(
			'className'  => 'LilBlogs.Post',
			'foreignKey' => 'author_id'
		)
	);
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name'  => array('rule' => array('minLength', '1'), 'required' => true),
		'email' => array('rule' => 'email', 'required' => false)
	);
}
?>