<?php
/* SVN FILE: $Id: comment.php 184 2009-10-21 18:52:07Z miha@nahtigal.com $ */
/**
 * Short description for comment.php
 *
 * Long description for comment.php
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
 * @version       $Revision: 184 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-21 20:52:07 +0200 (sre, 21 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
define('LILCOMMENT_APPROVED', 1);
define('LILCOMMENT_PENDING', -1);
/**
 * Comment class
 *
 * @uses          LilBlogsAppModel
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 */
class Comment extends LilBlogsAppModel {
/**
 * name property
 *
 * @var string 'Comment'
 * @access public
 */
	var $name = 'Comment';
/**
 * order property
 *
 * @var string
 * @access public
 */
	var $order = 'created DESC';
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
	var $belongsTo = array(
		'Post' => array(
			'className' => 'LilBlogs.Post',
			'foreign_key' => 'post_id',
			'counterCache' => 'no_comments',
			'counterScope' => array('Comment.status' => LILCOMMENT_APPROVED),
			'type' => 'INNER'
		)
	);
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'author' => array(
			'rule' => array('minLength', '1'),
			'required' => true
		),
		'email' => array(
			'rule' => 'email',
			'required' => true
		),
		'body' => array(
			'rule' => array('minLength', '1'),
			'required' => true
		),
		'post_id' => array(
			'rule'=>'checkAllowed',
			'required'=>true
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
		$this->order = $this->name.'.created DESC';
		parent::__construct($id, $table, $ds);
	}
/**
 * checkAllowed method
 *
 * @param array $data
 * @access public
 * @return boolean
 */
	function checkAllowed($data) {
		return (boolean)$this->Post->field('allow_comments', array('Post.id' => $data['post_id']));
	}
/**
 * add method
 *
 * @param array $data
 * @access public
 * @return mixed
 */
	function add($data) {
		$data['Comment']['ip'] = env('REMOTE_ADDR');
		$data['Comment']['status'] = LILCOMMENT_APPROVED;
		
		$data = $this->callPluginHandlers('before_save_comment', $data);
		return $this->saveAll($data);
	}
/**
 * setStatus method
 *
 * @param array $data
 * @access public
 * @return mixed
 */
	function setStatus($comment_id, $comment_status) {
		$data = $this->callPluginHandlers('set_comment_status', array(
			'id'      => $comment_id,
			'status'  => $comment_status,
			'allowed' => array(LILCOMMENT_APPROVED, LILCOMMENT_PENDING)
		));
		
		if (is_numeric($data['id']) && in_array($data['status'], (array)$data['allowed']) && 
			$this->hasAny(array('Comment.id' => $comment_id)))
		{
			$this->id = $comment_id;
			return $this->saveField('status', $comment_status);
		} else {
			return false;
		}
	}
}
?>