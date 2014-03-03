<?php
/* SVN FILE: $Id: post.php 154 2009-10-10 17:56:54Z miha@nahtigal.com $ */
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
 * @link          http://www.nahtigal.com/
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 * @since         v 1.0
 * @version       $Revision: 154 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-10 19:56:54 +0200 (sob, 10 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Post class
 *
 * @uses          LilBlogsAppModel
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 */
class Post extends LilBlogsAppModel {
/**
 * name property
 *
 * @var string 'Post'
 * @access public
 */
	var $name = 'Post';
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array('Containable', 'LilBlogs.LilSearch');
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Blog' => array(
			'className'  => 'LilBlogs.Blog',
			'foreignKey' => 'blog_id',
			'type'       => 'INNER'
		),
	);
/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	var $hasMany = array(
		'Comment' => array(
			'className'  => 'LilBlogs.Comment',
			'foreignKey' => 'post_id',
			'order'      => 'Comment.created'
		)
	);
/**
 * hasAndBelongsToMany property
 *
 * @var array
 * @access public
 */
	var $hasAndBelongsToMany = array(
		'Category' => array(
			'className' => 'LilBlogs.Category',
			'width' => 'LilBlogs.CategoriesPost',
			// this has to be additional for custom configuration abilities
			'withClassName' => 'CategoriesPost',
			'foreignKey' => 'post_id',
			'associationForeignKey' => 'category_id',
		)
	);
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'title'	=> array(
			'rule'     => array('minLength', '1'),
			'required' => true
		),
		'slug' => array(
			'rule'       => 'checkSlug',
			'required'   => false,
			'allowEmpty' => true
		),
		'body' => array(
			'rule'     => array('minLength', '1'),
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
		$this->order = $this->name . '.created DESC';
		$this->belongsTo['Author'] = Configure::read('LilBlogs.userTable');
		
		// create this on fly because table prefix cannot be set in HABTM
		$this->hasAndBelongsToMany['Category']['className'] = 'LilBlogs.Category';
		if ($table_prefix = Configure::read('LilBlogs.tablePrefix')) {
			$this->hasAndBelongsToMany['Category']['joinTable'] = $table_prefix.'categories_posts';
		}
		
		if (Configure::read('LilBlogs.noCategories')) {
			unset($this->hasAndBelongsToMany['Category']);
		} else if ($category_assoc = Configure::read('LilBlogs.categoryTable')) {
			$this->hasAndBelongsToMany['Category'] = $category_assoc;
		}
		
		if (Configure::read('LilBlogs.noBlogs')) {
			unset($this->belongsTo['Blog']);
		}
		
		parent::__construct($id, $table, $ds);
	}
/**
 * checkSlug method
 *
 * @param array $data
 * @access public
 * @return boolean
 */
	function checkSlug($data) {
		if (Configure::read('LilBlogs.slug') == 'auto') {
			return true;
		} else {
			return preg_match('/^[a-zA-Z0-9_-]+$/', $data['slug']);
		}
	}
/**
 * beforeSave callback
 *
 * @access public
 * @return boolean
 */
	function beforeSave() {
		if (empty($this->data['Post']['slug']) && !empty($this->data['Post']['title']) && !empty($this->data['Post']['blog_id'])) {
			$this->data['Post']['slug'] = strtolower(Inflector::slug($this->data['Post']['title'], '-'));
			if ($this->hasAny(array('Post.slug'=>$this->data['Post']['slug'], 'Post.blog_id'=>$this->data['Post']['blog_id'], 'NOT'=>array('Post.id'=>@$this->data['Post']['id'])))) {
				$i = 2;
				while ($this->hasAny(array('Post.slug'=>$this->data['Post']['slug'].'-'.$i, 'Post.blog_id'=>$this->data['Post']['blog_id'], 'NOT'=>array('Post.id'=>@$this->data['Post']['id'])))) {
					$i++;
				}
				$this->data['Post']['slug'] .= '-'.$i;
			}
		}
		return true;
	}
}
?>