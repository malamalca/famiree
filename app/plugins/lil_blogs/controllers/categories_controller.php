<?php
/* SVN FILE: $Id: categories_controller.php 188 2009-11-27 12:35:55Z miha@nahtigal.com $ */
/**
 * Short description for categories_controller.php
 *
 * Long description for categories_controller.php
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
 * @version       $Revision: 188 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-11-27 13:35:55 +0100 (pet, 27 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * CategoriesController class
 *
 * @uses          LilBlogsAppController
 * @package       lil_blogs
 * @subpackage    lil_blogs.controllers
 */
class CategoriesController extends LilBlogsAppController {
/**
 * name property
 *
 * @var string 'Categories'
 * @access public
 */
	var $name = 'Categories';
/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = array('LilBlogs.Category', 'LilBlogs.AuthorsBlog', 'Security');
/**
 * isAuthorized method
 *
 * @access public
 * @return bool
 */
	function isAuthorized() {
		if (!parent::isAuthorized()) return false;
		if (isset($this->Auth) && !Configure::read('LilBlogs.allowAuthorsAnything')) {
			// blog has to be selected
			if (!$blog_id = $this->getAdminBlog()) return false;
			
			// check if category belongs to current blog
			if (in_array($this->params['action'], array('admin_edit', 'admin_delete'))) {
				if (empty($this->params['pass'][0])) return false;
							
				if ($blog_id != $this->Category->field('blog_id',
					array('Category.id' => $this->params['pass'][0]))) return false;
				
			}
			
			// test if user has rights to current blog
			return 
				Configure::read('LilBlogs.noBlogs') || 
				$this->Auth->user(Configure::read('LilBlogs.authorAdminField')) ||
				$this->AuthorsBlog->hasAny(array(
					'author_id' => $this->Auth->user('id'), 
					'blog_id'   => $blog_id
				));
		}
		return true;
	}
/**
 * admin_index method
 *
 * @param int $blogid
 * @access public
 * @return void
 */
	function admin_index() {
		$blogid = $this->params['admin.blog_id'];
		$data = $this->Category->find('all', array(
			'conditions' => array(
				'Category.blog_id' => $blogid
			),
			'contain' => array()
		));
		$this->set(compact('data'));
	}
/**
 * admin_add method
 *
 * @param int $blogid
 * @access public
 * @return void
 */
	function admin_add() {
		$blogid = $this->params['admin.blog_id'];
		
		if (!empty($this->data)) {
			$this->Category->create();
			if ($this->Category->save($this->data)) {
				$this->Session->setFlash(__d('lil_blogs', 'A new category has been created.', true));
				$this->redirect(array('action'=>'admin_index'));
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Please verify that the information is correct.', true), 'error');
			}
		} else {
			$this->data['Category']['blog_id'] = $blogid;
		}
	}
/**
 * admin_edit method
 *
 * @param int $id
 * @access public
 * @return void
 */
	function admin_edit($id=null) {
		$this->Category->recursive = -1;
		
		if (!empty($this->data)) {
			if ($this->Category->save($this->data)) {
				$this->Session->setFlash(__d('lil_blogs', 'Category has been saved.', true));
				$this->redirect(array('action' => 'admin_index'));
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Please verify that the information is correct.', true), 'error');
			}
		} else if (!is_numeric($id) || !($this->data = $this->Category->read(null, $id))) {
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
	function admin_delete($id=null) {
		$this->Category->recursive = -1;
		if (is_numeric($id) && $data = $this->Category->findById($id)) {
			$this->Category->delete($id);
			$this->Session->setFlash(__d('lil_blogs', 'Category has been deleted.', true));
			$this->redirect($this->referer());
		} else {
			$this->error404();
		}
	}
}
?>