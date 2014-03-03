<?php
/* SVN FILE: $Id: comments_controller.php 193 2009-11-29 16:33:35Z miha@nahtigal.com $ */
/**
 * Short description for comments_controller.php
 *
 * Long description for comments_controller.php
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
 * @subpackage    lil_blogs.controllers
 * @since         v 1.0
 * @version       $Revision: 193 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-11-29 17:33:35 +0100 (ned, 29 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * CommentsController class
 *
 * @uses          LilBlogsAppController
 * @package       lil_blogs
 * @subpackage    lil_blogs.controllers
 */
class CommentsController extends LilBlogsAppController {
/**
 * name property
 *
 * @var string 'Comments'
 * @access public
 */
	var $name = 'Comments';
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Paginator', 'Time');
/**
 * components property
 *
 * @var array
 * @access public
 */
	var $components = array('RequestHandler', 'Security');
/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = array('LilBlogs.Comment', 'LilBlogs.Post', 'LilBlogs.AuthorsBlog');
/**
 * paginate property
 *
 * @var array
 * @access public
 */
	var $paginate = array(
		'limit' => 10,
		'order' => array(
			'Comment.id' => 'desc'
		)
	);
/**
 * beforeFilter method
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		$this->Auth->allow('index');
		if ($this->params['action'] == 'admin_quick') {
			$this->Security->validatePost = false;
		}
		parent::beforeFilter();
	}
/**
 * isAuthorized method
 *
 * @access public
 * @return bool
 */
	function isAuthorized() {
		if (!parent::isAuthorized()) return false;
		if (in_array($this->params['action'], array('index'))) return true;
		if (isset($this->Auth) && !Configure::read('LilBlogs.allowAuthorsAnything')) {
			// blog has to be selected
			if (!$blog_id = $this->getAdminBlog()) return false;
			
			// check target post's blog id
			if (in_array($this->params['action'], array('admin_edit', 'admin_delete', 'admin_categorize')))
			{
				if (empty($this->params['pass'][0])) return false;
				
				if (!$post_id = $this->Comment->field('Comment.post_id', array('Comment.id' => $this->params['pass'][0]))) return false;
				if (!$blog_id = $this->Comment->Post->field('Post.blog_id', array('Post.id' => $post_id))) return false;
			}
			
			// when beeing on admin index, there can be filter on post_id
			// so we must check if author has rights on this post's blog
			if (($this->params['action'] == 'admin_index') && 
			!empty($this->params['named']['post_id']))
			{
				if (!$blog_id = $this->Post->field('Post.blog_id',
					array('Post.id' => $this->params['named']['post_id']))) return false;
			}
			
			if (($this->params['action']=='admin_quick')) {
				// this is exception; all comments in this action must belong to current blog
				$this->Comment->recursive = 0;
				if (!empty($this->data['Comment']['comments'])) {
					foreach ((array)$this->data['Comment']['comments'] as $comment_id) {
						if (!empty($comment_id)) {
							if (!$post_id = $this->Comment->field('Comment.post_id', array('Comment.id' => $comment_id))) return false;
							if (!$blog_id = $this->Comment->Post->field('Post.blog_id', array('Post.id' => $post_id))) return false;
							if (!$blog_id == $this->getAdminBlog()) return false;
						}
					}
				} else return true;
			} else {
				// test if user has rights to current blog
				return 
					Configure::read('LilBlogs.noBlogs') || 
					$this->Auth->user(Configure::read('LilBlogs.authorAdminField')) ||
					$this->AuthorsBlog->hasAny(array(
						'author_id' => $this->Auth->user('id'), 
						'blog_id'   => $blog_id
					));
			}
		}
		return true;
	}
/**
 * index method
 *
 * @access public
 * @return void
 */
	function index() {
		$blog_name = '';
		if (!empty($this->params['blogname'])) {
			$blog_name = $this->params['blogname'];
		} else if (!empty($this->params['named']['blogname'])) {
			$blog_name = $this->params['named']['blogname'];
		} else if (!empty($this->params['pass'][0])) {
			$blog_name = $this->params['pass'][0];
		}
		
		$blog = $this->_findBlogByShortName($blog_name);
		
		$post_id = null;
		if (!empty($blog['Blog']['id'])) {
			if (!empty($this->params['postid']) && is_numeric($this->params['postid'])) {
				$post_id = $this->params['postid'];
			} else if (!empty($this->params['named']['postid']) && is_numeric($this->params['named']['postid'])) {
				$post_id = $this->params['named']['postid'];
			} else if (!empty($this->params['post'])) {
				$post_id = $this->Comment->Post->field('id', array(
					'Post.blog_id' => $blog['Blog']['id'],
					'Post.slug'    => $this->params['post'], 
				));
			} else if (!empty($this->params['named']['post'])) {
				$post_id = $this->Comment->Post->field('id', array(
					'Post.blog_id' => $blog['Blog']['id'],
					'Post.slug'    => $this->params['named']['post'], 
				));
		 	} else if (!empty($this->params['pass'][1]) && is_numeric($this->params['pass'][1])) {
		 		$post_id = $this->params['pass'][1];
		 	} else if (!empty($this->params['pass'][1]) && !empty($this->params['pass'][1])) {
				$post_id = $this->Comment->Post->field('id', array(
					'Post.blog_id' => $blog['Blog']['id'],
					'Post.slug'    => $this->params['pass'][1], 
				));
			}
		}
		
		// set additional parameters
		$params = array(
			'conditions'=> array(
				'Comment.status' => LILCOMMENT_APPROVED,
				'Post.status'    => 2
			),
			'limit'   => 50,
		);
		
		if (Configure::read('LilBlogs.noBlogs')) {
			$params['contain'] = array('Post');
		} else {
			$params['contain'] = array('Post' => 'Blog');
		}
		
		if (!empty($blog['Blog']['id'])) {
			$params['conditions']['Post.blog_id'] = $blog['Blog']['id'];
			
			if ($post_id && 
				($post_title = $this->Comment->Post->field('title', array('Post.id' => $post_id))))
			{
				$rss_title = sprintf(__d('lil_blogs', 'Comments on %1$s for %2$s', true),
					$blog['Blog']['name'],
					$post_title
				);
			} else {
				$rss_title = __d('lil_blogs', 'Comments for', true) . ' ' . $blog['Blog']['name'];
			}
			$rss_descript = $blog['Blog']['description'];
		} else {
			$blog_name    = '';
			$rss_title    = __d('lil_blogs', 'LilBlogs comments', true);
			$rss_descript = __d('lil_blogs', 'Coments from every single blog on', true) . ' ' . 
				Router::url('/', true);
		}
		
		if ($this->RequestHandler->prefers('rss') == 'rss') {
			Configure::write('debug', 0);
			$this->set('channel', array('title' => 	$rss_title, 'description' => $rss_descript));
			$params['limit'] = Configure::read('LilBlogs.rssItems');
		}
		
		$recentcomments = $this->Comment->find('all', $params);
		if ($blogs = Configure::read('LilBlogs.noBlogs')) {
			foreach ($recentcomments as $k => $c) {
				$recentcomments[$k]['Blog'] = $blogs[$c['Post']['blog_id']]['Blog'];
			}
		}
		
		$this->set(compact('blog', 'recentcomments'));
	}
/**
 * admin_edit method
 *
 * @param int $id
 * @access public
 * @return void
 */
	function admin_edit($id=null) {
		if (!empty($this->data)) {
			if ($this->Comment->saveAll($this->data)) {
				$this->Session->setFlash(__d('lil_blogs', 'Comment has been saved.', true));
				$this->redirect(array('action' => 'admin_index', $this->data['Comment']['post_id']));
			}
		} else if (!is_numeric($id) || !($this->data = $this->Comment->read(null, $id))) {
			$this->error404();
		}
	}
/**
 * admin_delete method
 *
 * @param int $id
 * @access public
 * @return void
 */
	function admin_delete($id) {
		if ($post_id = $this->Comment->field('post_id', array('Comment.id' => $id))) {
			$this->Comment->delete($id);
			$this->Session->setFlash(__d('lil_blogs', 'Comment has been deleted', true));
			$this->redirect(array('action' => 'admin_index', $post_id));
		} else {
			$this->error404();
		}
	}
/**
 * admin_index method
 *
 * @param int $postid
 * @access public
 * @return void
 */
	function admin_index() {
		$this->Comment->recursive = 0;
		$conditions = array();
		
		if(!empty($this->params['named']['post_id']) && is_numeric($this->params['named']['post_id'])) {
			$conditions = array('Comment.post_id' => $this->params['named']['post_id']);
			$post = $this->Comment->Post->read(null, $this->params['named']['post_id']);
			$this->set(compact('post'));
		} else if ($blog_id = $this->getAdminBlog()) {
			$conditions = array('Post.blog_id' => $blog_id);
		} else {
			$this->error404();
			return;
		}
		
		if (isset($this->params['named']['status'])) {
			switch ($this->params['named']['status']) {
				case 'approved':
					$conditions['Comment.status'] = LILCOMMENT_APPROVED;
					break;
				case 'pending':
					$conditions['Comment.status'] = LILCOMMENT_PENDING;
					break;
			}
		}
		
		// allow plugins to modify pagination parameters
		$params = array(
			'conditions' => $conditions
		);
		$params = $this->callPluginHandlers('admin_comments_index_conditions', $params);
		
		$this->paginate = $params;
		$comments = $this->paginate('Comment');

		array_walk($comments, array($this, '_clean'));
		$this->set(compact('comments'));
	}
/**
 * admin_quick method
 *
 * @access public
 * @return void
 */
	function admin_quick() {
		if (!empty($this->data['Comment']['comments']) &&
		!empty($this->data['Comment']['action']) &&
		in_array($this->data['Comment']['action'], array('delete', 'approve', 'unapprove')))
		{
			foreach ((array)$this->data['Comment']['comments'] as $comment_id) {
				$this->Comment->id = $comment_id;
				if (!empty($comment_id) && $this->Comment->exists(true)) {
					if ($this->data['Comment']['action']=='delete') {
						if (!$this->Comment->delete($comment_id)) {
							$this->Session->setFlash(__d('lil_blogs', 'Ups, an error occured. Returning back.', true));
							$this->redirect($this->referer());
							return;
						}
					} else {
						if ($this->data['Comment']['action'] == 'approve') {
							$comment_status = LILCOMMENT_APPROVED;
						} else if ($this->data['Comment']['action'] == 'unapprove') {
							$comment_status = LILCOMMENT_PENDING;
						}
						if (empty($comment_status) || !$this->Comment->saveField('status', $comment_status)) {
							$this->Session->setFlash(__d('lil_blogs', 'Ups, an error occured. Returning back.', true), 'error');
							$this->redirect($this->referer());
							return;
						}
					}
				}
			}
			$this->Session->setFlash(__d('lil_blogs', 'Comments have been updated.', true));
		} else {
			$this->Session->setFlash(__d('lil_blogs', 'No comments or action selected', true), 'error');
		}
		$this->redirect($this->referer());
	}
/**
 * admin_categorize method
 *
 * @access public
 * @return void
 */
	function admin_categorize($comment_id, $comment_status) {
		if ($current_status = $this->Comment->field('status', array('Comment.id' => $comment_id)))
		{
			if (( $current_status != $comment_status) &&
				$this->Comment->setStatus($comment_id, $comment_status))
			{
				$this->Session->setFlash(__d('lil_blogs', 'Comment has been successfuly categorized.', true));
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Ups, an error occured. Returning back.', true));
			}
			
			$this->redirect(array(
				'admin' => true,
				'action' => 'index',
				$this->Comment->field('post_id', array('Comment.id' => $comment_id))
			));
		} else {
			$this->error404();
		}
	}
/**
 * _clean method
 *
 * @param mixed $data 
 * @access private
 * @return void
 */
	function _clean(&$data)	{
		$data['Comment']['body'] = $this->kses5Clean($data['Comment']['body']);
	}
/**
 * _findBlogByShortName method
 *
 * Find blog record by short name
 *
 * @param string $name
 * @access private
 * @return void
 */
	function _findBlogByShortName($name) {
		if ($blogs = Configure::read('LilBlogs.noBlogs')) {
			foreach ($blogs as $blog) {
				if ($blog['Blog']['short_name'] == $name) return $blog;
			}
			return false;
		} else {
			$this->Post->Blog->recursive = -1;
			return $this->Post->Blog->findByShortName($name);
		}
	}
}
?>