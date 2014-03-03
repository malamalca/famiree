<?php
/* SVN FILE: $Id: blog.php 181 2009-10-18 19:18:48Z miha@nahtigal.com $ */
/**
 * Short description for blog.php
 *
 * Long description for blog.php
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
 * Blog class
 *
 * @uses          LilBlogsAppModel
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 */
class Blog extends LilBlogsAppModel {
/**
 * name property
 *
 * @var string 'Blog'
 * @access public
 */
	var $name = 'Blog';
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array('Containable');
/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	var $hasMany = array(
		'Post'     => array(
			'className' => 'LilBlogs.Post',
			'foreignKey' => 'blog_id'
		), 
		'Category' => array(
			'className' => 'LilBlogs.Category',
			'foreignKey' => 'blog_id'
		)
	);
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			'rule' => array('minLength', '1'),
			'required' => true
		),
		'short_name' => array(
			'rule' => 'checkShortName',
			'required' => false,
			'allowEmpty' => true
		),
		'description' => array(
			'rule' => array('minLength', '1'), 
			'required' => true
		)
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
		$this->hasAndBelongsToMany['Author'] = Configure::read('LilBlogs.userTable');
		$this->hasAndBelongsToMany['Author']['associationForeignKey'] = $this->hasAndBelongsToMany['Author']['foreignKey'];
		$this->hasAndBelongsToMany['Author']['foreignKey'] = 'blog_id';
		
		parent::__construct($id, $table, $ds);
	}
/**
 * checkShortName method
 *
 * @param array $data
 * @access public
 * @return boolean
 */
	function checkShortName($data) {
		if (Configure::read('LilBlogs.slug') == 'auto') {
			return true;
		} else {
			return preg_match('/^[a-zA-Z0-9_-]+$/', $data['short_name']);
		}
	}
/**
 * beforeSave callback
 *
 * @access public
 * @return boolean
 */
	function beforeSave() {
		if (empty($this->data['Blog']['short_name']) && !empty($this->data['Blog']['name'])) {
			$this->data['Blog']['short_name'] = strtolower(Inflector::slug($this->data['Blog']['name'], '-'));
			if ($this->hasAny(array('Blog.short_name'=>$this->data['Blog']['short_name'], 'NOT'=>array('Blog.id'=>@$this->data['Blog']['id'])))) {
				$i = 2;
				while ($this->hasAny(array('Blog.short_name'=>$this->data['Blog']['short_name'].'-'.$i, 'NOT'=>array('Blog.id'=>@$this->data['Blog']['id'])))) {
					$i++;
				}
				$this->data['Blog']['short_name'] .= '-'.$i;
			}
		}
		return true;
	}
/**
 * findByShortName method
 *
 * @param string $short_name Blog's short name
 * @access public
 * @return boolean
 */
	function findByShortName($short_name) {
		if ($blog = Configure::read('LilBlogs.noBlogs')) {
			return $blog;
		} else {
			return $this->find('first', array(
				'conditions' => array(
					'Blog.short_name' => $short_name
				),
				'recursive' => -1
			));
		}
	}
}
?>