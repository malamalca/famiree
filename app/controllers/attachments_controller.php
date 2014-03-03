<?php
/* SVN FILE: $Id: attachments_controller.php 119 2009-11-26 20:01:50Z miha.nahtigal $ */
/**
 * Short description for attachments_controller.php
 *
 * Long description for attachments_controller.php
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
 * @version       $Revision: 119 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-11-26 21:01:50 +0100 (čet, 26 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * AttachmentsController class
 *
 * @uses          AppController
 * @package       base
 * @subpackage    base.controllers
 */
class AttachmentsController extends AppController {
/**
 * name property
 *
 * @var string 'Attachments'
 * @access public
 */
	var $name = 'Attachments';
/**
 * paginate property
 *
 * @var array
 * @access public
 */
	var $paginate = array('limit' => 10, 'order' => 'Attachment.id DESC');
/**
 * publicAccess property
 *
 * If set to true, you don't need to login to see uploaded, mediaView served, content.
 * Otherwise, you do.
 *
 * @var bool true
 * @access public
 */
	var $publicAccess = true;
/**
 * beforeFilter method
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		parent::beforeFilter();
		if ($this->publicAccess && isset($this->Auth)) {
			$this->Auth->allow('display');
		}
	}
/**
 * index function
 *
 * @access public
 * @return void
 */
	function index() {
		$this->set('attachments', $this->Attachment->filter($this->params['named']));
		$this->set('sidebar', 'attachments'.DS.'index');
	}
/**
 * display method
 *
 * Serve up files directly from the uploads folder.
 *
 * @param mixed $id
 * @param mixed $name
 * @param mixed $size
 * @return void
 * @access public
 */
	function display($id, $size = null, $name = null) {
		Configure::write('debug', 0);
		$this->Attachment->recursive = -1;
		$correctSlug = false;
		
		if (!$size) $size = 'original';
		
		if (!empty($id)) {
			$correctSlug = true;
			$row = $this->Attachment->read(null, $id);
		} else {
			$params = func_get_args();
			$file = array_pop($params);
			$folder = implode($params, '/');
			$extension = array_pop(explode('.', $file));
			if (strpos($extension, '_') !== false) {
				list($extension_, $size) = explode('_', $extension);
				$file = str_replace($extension, $extension_, $file);
				$extension = $extension_;
			}
			$conditions['dir'] = $folder;
			$conditions['filename'] = $file;
			$conditions['ext'] = $extension;
			$row = $this->Attachment->find('first', compact('conditions'));
		}
		if (!$row) {
			debug('No file found for ' . implode(func_get_args(), '/'));
			die;
		}
		
		extract ($row['Attachment']);
		if ($correctSlug) {
			$a_description = Inflector::slug(${$this->Attachment->displayField});
			if (!$a_description) {
				$a_description = $filename;
				if (substr($a_description, -3) != $ext) {
					$a_description .= '.' . $ext;
				}
			} else {
				$a_description = str_replace('-' . $ext, '.' . $ext, $a_description);
				if (strpos('.' . $ext, $a_description) === false) {
					$a_description .= '.' . $ext;
				}
			}
			if ($name != $a_description) {
				$this->redirect(array($id, $size, $a_description), 301);
			}
		}
		
		$data = compact('modified');
		$file_path = APP . 'uploads' . DS . $id . DS . $size;
		
		if (!file_exists($file_path)) {
			$filename =  'missing.png';
			$ext = 'png';
			$data['path'] = 'uploads' . DS;
		} else {
			$data['path'] = 'uploads' . DS . $id . DS;
		}
		
		$data['id'] = $size;
		$data['extension'] = strtolower($ext);
		$data['download'] = isset($this->params['named']['download'])?$this->params['named']['download']:false;
		$data['name'] = $a_description;
		if ($this->publicAccess) {
			$data['cache'] = '+ 99days';
		}
		$this->set($data);
		
		$this->view = 'Media';
		$this->render();
	}
/**
 * view function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function view($id = null) {
		$this->Attachment->contain(array('Profile', 'AttachmentsLink'=>array('Profile'), 'Imgnote'));
		if (!empty($id) && ($data = $this->data = $this->Attachment->read(null, $id))) {
			$this->set('attachment', $data);
			$this->set('large_sizes', $this->Attachment->getImageSize($id, 'large'));
			$this->set('sidebar', 'attachments'.DS.'attachment');
		} else {
			$this->error404();
		}
	}
/**
 * edit method
 *
 * Edit specified attachment
 *
 * @param mixed $id
 * @return void
 * @access public
 */
	function edit($id=null) {
		if (!empty($this->data)) {
			$this->Attachment->create();
			if ($this->Attachment->save($this->data)) {
				$this->setFlash(__('Attachment has been successfully saved.', true));
				
				$referer = trim(base64_decode(@$this->data['Attachment']['referer']));
				if (!empty($referer) && $referer!='') {
					$this->redirect(array_merge(
						$this->parseUrl($referer), array('highlight' => $this->Attachment->id)
					));
				} else {
					$this->redirect(array(
						'controller' => Inflector::pluralize($this->data['class']),
						'action'     => 'view',
						$this->data['Attachment']['foreign_id']
					));
				}
			} else {
				$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			if (!empty($id)) {
				if (!$this->data = $this->Attachment->read(null, $id)) {
					$this->error404();
				}
			} else {
				$this->data['Attachment']['id'] = String::uuid();
			}
			$this->data['Attachment']['referer'] = base64_encode($this->referer(''));
		}
		
		if (!empty($id) && ($attachment = $this->Attachment->read(null, $id))) {
			$this->set(compact('attachment'));
		}
		
		$this->set('sidebar', 'attachments'.DS.'edit');
	}
/**
 * add method
 *
 * Add a new attachment
 *
 * @return void
 * @access public
 */
	function add() {
		$this->setAction('edit');
	}
/**
 * delete function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function delete($id=null) {
		if (!empty($id)) {
			if (!$this->Attachment->Profile->hasAny(array('Profile.ta' => $id))) {
				if ($this->Attachment->del($id)) {
					$this->setFlash(__('Attachment has been successfully deleted.', true));
				} else {
					$this->setFlash(__('Deleting Attachment has failed.', true), 'error');
				}
			} else {
				$this->setFlash(__('You cannot delete Profile\'s avatar.', true), 'error');
			}
			$this->redirect($this->referer());
		} else {
			$this->error404();
		}
	}
/**
 * rotate_cw method
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function rotate_cw($id=null) {
		if (!empty($id)) {
			if ($this->Attachment->read(null, $id) && $this->Attachment->rotate()) {
				$this->setFlash(__('Attachment has been successfully modified.', true));
			} else {
				$this->setFlash(__('Modify Attachment has failed.', true), 'error');
			}
			$this->redirect($this->referer());
		} else {
			$this->error404();
		}
	}
}
?>