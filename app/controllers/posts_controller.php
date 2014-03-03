<?php
/* SVN FILE: $Id: posts_controller.php 90 2009-06-10 17:48:08Z miha.nahtigal $ */
/**
 * Short description for posts_controller.php
 *
 * Long description for posts_controller.php
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
 * @subpackage    famiree.app.controllers
 * @since         v 1.0
 * @version       $Revision: 90 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-06-10 19:48:08 +0200 (sre, 10 jun 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * PostsController class
 *
 * @uses          AppController
 * @package       base
 * @subpackage    base.controllers
 */
class PostsController extends AppController {
/**
 * name property
 *
 * @var string 'Posts'
 * @access public
 */
 	var $name = 'Posts';
/**
 * edit function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function edit($id=null) {
		if (!empty($this->data)) {
			$this->Post->create();
			
			if ($this->Post->saveAll($this->data)) {
				$this->setFlash(__('Post has been successfully saved.', true));
				
				$referer = trim(base64_decode(@$this->data['Post']['referer']));
				if (!empty($referer)) {
					$this->redirect(array_merge(
						$this->parseUrl($referer), array('highlight_post'=>$this->Post->id)
					));
				} else {
					$this->redirect(array('action'=>'view', $this->Post->id));
				}
			} else {
				$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			if (is_numeric($id)) {
				if (!$this->data = $this->Post->read(null, $id)) {
					return $this->error404();
				}
				App::import('helper', 'Quicks');
				$this->Quicks =& new QuicksHelper();
				$creator = $this->Post->Creator->read(null, $this->data['Post']['creator_id']);
				$this->data['Post']['author'] = $this->Quicks->profileCaption($creator['Creator']);
			} else if (!is_null($id)) {
				return $this->error404();
			}
			$this->data['Post']['referer'] = base64_encode($this->referer(''));
		}
		$this->set('sidebar', '');
	}

/**
 * view function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function view($id=null) {
		$this->Post->contain(array('Creator', 'PostsLink'=>'Profile'));
		if (is_numeric($id) && $post=$this->Post->read(null, $id)) {
			$this->set('post', $post);
			$this->set('sidebar', '');
		} else {
			$this->error404();
		}
	}

/**
 * delete function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function delete($id=null) {
		if (is_numeric($id)) {
			if ($this->Post->del($id)) {
				$this->setFlash(__('Post has been successfully deleted.', true));
			} else {
				$this->setFlash(__('Deleting Post has failed.', true), 'error');
			}
			$this->redirect($this->referer());
		} else {
			$this->error404();
		}
	}
}

?>