<?php
/* SVN FILE: $Id: blogs_controller.php 193 2009-11-29 16:33:35Z miha@nahtigal.com $ */
/**
 * Short description for blogs_controller.php
 *
 * Long description for blogs_controller.php
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
 * BlogsController class
 *
 * @uses          LilBlogsAppController
 * @package       lil_blogs
 * @subpackage    lil_blogs.controllers
 */
class BlogsController extends LilBlogsAppController {
/**
 * name property
 *
 * @var string 'Blogs'
 * @access public
 */
	var $name = 'Blogs';
/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = array('LilBlogs.Blog', 'LilBlogs.Post', 'LilBlogs.AuthorsBlog');
/**
 * beforeFilter method
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		$this->Auth->allow('index', 'view');
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
		if (in_array($this->params['action'], array('index', 'view'))) return true;
		if (isset($this->Auth) && !Configure::read('LilBlogs.allowAuthorsAnything')) {
			if (in_array($this->params['action'], array('admin_edit', 'admin_delete', 'admin_select'))) {
				if (!empty($this->params['pass'][0])) {
					return 
						$this->Auth->user(Configure::read('LilBlogs.authorAdminField')) ||
						$this->AuthorsBlog->hasAny(array(
							'author_id' => $this->Auth->user('id'),
							'blog_id'   => $this->params['pass'][0]
						));
				} else return false;
			} else if ($this->params['action'] == 'admin_add') {
				return $this->Auth->user(Configure::read('LilBlogs.authorAdminField'));
			}
		}
		return true;
	}
/**
 * index method
 *
 * @access public
 * @return bool
 */
	function index() {
		$this->Blog->recursive = -1;
		$blogs = $this->Blog->find('all', array('recursive' => 0));
		if (sizeof($blogs) == 1) {
			$this->redirect(array(
				'plugin'     => 'lil_blogs',
				'controller' => 'posts',
				'action'     => 'index',
				'blogname'   => $blogs[0]['Blog']['short_name']
			));
		} else {
			$this->set('blogs', $blogs);
		}
	}
/**
 * view method
 *
 * @param string $short_name  
 * @access public
 * @return void
 */
	function view($short_name) {
		$this->redirect('/' . $this->params['plugin'] . '/' . $short_name);
	}
/**
 * admin_index method
 *
 * @access public
 * @return void
 */
	function admin_index() {
		$this->Blog->recursive = 0;
		$params = array();
		if (isset($this->Auth) && (
			!$this->Auth->user(Configure::read('LilBlogs.authorAdminField')) && 
			!Configure::read('LilBlogs.allowAuthorsAnything')))
		{
			$params = array(
				'id' => $this->AuthorsBlog->find('list', array(
					'conditions' => array('author_id' => $this->Auth->user('id')), 
					'fields'     => array('id', 'blog_id')
				))
			); 
		}
		$this->set('blogs', $this->paginate('Blog', $params));
	}
/**
 * admin_list method
 *
 * @access public
 * @return void
 */
	function admin_list() {
		$this->Blog->recursive = 0;
		$params = array();
		if (isset($this->Auth) && (
			!$this->Auth->user(Configure::read('LilBlogs.authorAdminField')) &&
			!Configure::read('LilBlogs.allowAuthorsAnything')))
		{
			$params = array(
				'id' => $this->AuthorsBlog->find('list', array(
					'conditions' => array('author_id' => $this->Auth->user('id')),
					'fields'     => array('id', 'blog_id')
				))
			);
		}
		$this->set('blogs', $this->paginate('Blog', $params));
	}
/**
 * admin_select method
 *
 * This method select blog on which all operations will be performed.
 *
 * @param int $id  
 * @access public
 * @return void
 */
	function admin_select($id = null) {
		if (is_numeric($id) && $this->Blog->hasAny(array('Blog.id' => $id))) {
			$this->setAdminBlog($id);
			
			$this->Session->setFlash(__d('lil_blogs', 'Blog has been selected.', true));
			$this->redirect(array('controller' => 'posts', 'action'=>'index', 'admin' => true));
		} else {
			$this->error404();
		}
	}
/**
 * admin_add method
 *
 * @access public
 * @return void
 */
	function admin_add() {
		if(!empty($this->data)) {
			if($this->Blog->saveAll($this->data)) {
				$this->Session->setFlash(__d('lil_blogs', 'A new blog has been created.', true));
				$this->redirect(array(
					'admin'      => true,
					'plugin'     => 'lil_blogs',
					'controller' => 'blogs',
					'action'     => 'admin_index'
				));
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Please verify that the information is correct.', true), 'error');
			}
		}
		
		$this->set('authors', $this->Blog->Post->Author->find('list'));
	}
/**
 * admin_edit method
 *
 * @param int $id  
 * @access public
 * @return void
 */
	function admin_edit($id = null) {
		if(!empty($this->data)) {
			if($this->Blog->saveAll($this->data)) {
				$this->Session->setFlash(__d('lil_blogs', 'Blog has been saved.', true));
				$this->redirect(array(
					'admin'      => true,
					'plugin'     => 'lil_blogs',
					'controller' => 'blogs',
					'action'     => 'admin_index'
				));
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Please verify that the information is correct.', true), 'error');
			}
		} else if (!is_numeric($id) || !($this->data = $this->Blog->read(null, $id))) {
			$this->error404();
		}
		
		$this->set('authors', $this->Blog->Post->Author->find('list'));
	}
/**
 * admin_delete method
 *
 * @param int $id  
 * @access public
 * @return void
 */
	function admin_delete($id=null) {
		$this->Blog->recursive = -1;
		if (is_numeric($id) && $data = $this->Blog->findById($id)) {
			$this->Blog->delete($id);
			$this->Session->setFlash(__d('lil_blogs', 'Blog has been deleted.', true));
			$this->redirect(array('action'=>'admin_index'));
		} else {
			$this->error404();
		}
	}

}
?>