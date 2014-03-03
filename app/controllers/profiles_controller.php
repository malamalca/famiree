<?php
/* SVN FILE: $Id: profiles_controller.php 159 2010-01-23 17:48:06Z miha.nahtigal $ */
/**
 * Short description for profiles_controller.php
 *
 * Long description for profiles_controller.php
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
 * @version       $Revision: 159 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-23 18:48:06 +0100 (sob, 23 jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * ProfilesController class
 *
 * @uses          AppController
 * @package       base
 * @subpackage    base.controllers
 */
class ProfilesController extends AppController {
/**
 * name property
 *
 * @var string 'Profiles'
 * @access public
 */
 	var $name = 'Profiles';
/**
 * helpers property
 *
 * @var array
 * @access public
 */
 	var $helpers = array('LilBlogs.LilBlogs');
/**
 * paginate property
 *
 * @var array
 * @access public
 */
 	var $paginate = array(
 		'limit' => 21,
 		'contain' => array(),
	 );
/**
 * cacheAction property
 *
 * @var mixed
 * @access public
 */
	var $cacheAction = true;
/**
 * tree function
 *
 * @param mixed $id 
 * @access public
 * @return void
 */
	function tree($id=null) {
		$this->layout = false;
		if (!is_numeric($id) && isset($this->params['id'])) {
			$id = $this->params['id'];
		} else if (!is_numeric($id)) {
			// make user with id=1 default when no authorized user available
			if (!$id = $this->Auth->user('id')) {
				$id = 1;
			}
		}
		$this->set('current_profile', $id);
		$this->set('tree', $tree = $this->Profile->tree($id));
	}
/**
 * search function
 *
 * @access public
 * @return void
 */
	function search() {
		$data = array();
		
		$criterio = '';
		if (!empty($this->data['Profile']['criterio'])) {
			$criterio = $this->data['Profile']['criterio'];
		} else if (!empty($this->params['named']['criterio'])) {
			$criterio = $this->params['named']['criterio'];
		}
		
		$conditions = array();
		if (!empty($criterio)) {
			$conditions = array(
				'OR' => array(
					'fn LIKE' => '%' . $criterio . '%',
					'mdn LIKE' => '%' . $criterio . '%',
					'ln LIKE' => '%' . $criterio . '%',
					'loc LIKE' => '%' . $criterio . '%',
				)
			);
		}
		$profiles = $this->paginate('Profile', $conditions);
		$this->set(compact('profiles'));
		$this->set(compact('criterio'));
	}
/**
 * view function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function view($id = null) {
		if (is_null($id) && $this->Auth->user('id')) {
			$id = $this->Auth->user('id');
		} else if (is_null($id)) {
			$this->redirect(array('action'=>'tree'));
		}
		
		$this->Profile->contain(array('Attachment', 'Post' => 'Author', 'Creator'));
		if (is_numeric($id) && $profile = $this->Profile->read(null, $id)) {
			// display only dead people
			if (!$this->Auth->user('id') && $profile['Profile']['l']==1) {
				$this->Session->setFlash(__('You have to be logged in to view profiles of living people.', true), 'default', array(), 'auth');
				$this->redirect($this->Auth->loginAction);
			}
			
			if (!empty($this->data['Attachment'])) {
				if ($this->Profile->Attachment->saveAll($this->data)) {
					$this->setFlash(__('Attachment has been successfully saved.', true));
					$this->redirect(array($id));
				} else {
					$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
				}
			}
			
			if (!empty($this->data['Post'])) {
				if ($this->Profile->Post->saveAll($this->data)) {
					$this->Session->setFlash(__('Memory has been successfully saved.', true));
					$this->redirect(array($id));
				} else {
					$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
				}
			}
			
			$this->set('profile', $profile);
			$this->set('family', $this->Profile->family($id));
			
			$this->set('sidebar', 'profiles'.DS.'view');
		} else {
			$this->error404();
		}
	}
/**
 * edit function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function edit($id=null) {
		if (empty($id) && !empty($this->data['Profile']['id'])) $id = $this->data['Profile']['id'];
		
		if (!empty($this->data)) {
			$this->data['Profile']['modifier_id'] = $this->Auth->user('id');
			$this->data['Profile']['d_n'] = trim(trim($this->data['Profile']['fn'].' '.
				$this->data['Profile']['mn']).' '.$this->data['Profile']['ln']);
			
			// unbind model so Unions don't get deleted
			$unions = array();
			if (isset($this->data['Union'])) {						
				$unions = $this->data['Union']; 
				unset($this->data['Union']);
			}
			if ($this->Profile->saveAll($this->data)) {
				// save all unions
				foreach ($unions as $union) {
					$this->Profile->Union->save(array('Union'=>$union));
				}
				
				$this->setFlash(__('Profile has been successfully saved.', true));
				
				$referer = trim(base64_decode(@$this->data['Profile']['referer']));
				if (!empty($referer) && $referer!='?') {
					$this->redirect(array_merge(
						$this->parseUrl($referer), array('highlight'=>$this->Profile->id)
					));
				} else {
					$this->redirect(array('action'=>'view', $this->Profile->id));
				}
			} else {
				$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			if (is_numeric($id)) {
				$this->Profile->contain('Union');
				if (!$this->data = $this->Profile->read(null, $id)) {
					return $this->error404();
				}
			} else if (!is_null($id)) {
				return $this->error404();
			}
			$this->data['Profile']['referer'] = base64_encode($this->referer('?'));
		}
		
		$this->set('spouses', $this->Profile->family($id, 'spouses'));
		$this->set('d_n', $this->Profile->field('d_n', array('Profile.id'=>$id)));
		$this->set('sidebar', 'profiles'.DS.'edit');
	}
/**
 * delete function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function delete($id=null) {
		if (is_numeric($id) && $this->Profile->canDelete($id)) {
			$this->Profile->delete($id);
			$this->Session->setFlash(__('Profile has been successfuly deleted.', true));
			$this->redirect('/');
		} else {
			$this->error404();
		}
	}
/**
 * edit_avatar function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function edit_avatar($id=null, $attachment_id=null) {
		if (!is_numeric($id)) {
			return $this->error404();
		}
		
		$this->Profile->id = $id;
		if (!$this->Profile->exists()) {
			return $this->error404();
		}
		
		if (!empty($attachment_id)) {
			if ($attachment_id=='remove') {
				$this->Profile->saveField('ta', null);
				$this->setFlash(__('Profile\'s avatar has been successfully cleared.', true));
				$this->redirect(array('controller'=>'profiles', 'action'=>'view', $id));
			} else {
				$this->Profile->Attachment->id = $attachment_id;
				if ($this->Profile->Attachment->exists()) {
					$this->Profile->saveField('ta', $attachment_id);
					$this->setFlash(__('Profile\'s avatar has been successfully saved.', true));
					$this->redirect(array('controller'=>'profiles', 'action'=>'view', $id));
				}
			}
			return $this->error404();
		}
		
		$this->set('profile', $this->Profile->read(null, $id));
		$this->set('attachments', $this->Profile->Attachment->AttachmentsLink->find('all', array(
			'contain' => array('Attachment'),
			'conditions' => array(
				'AttachmentsLink.class' => 'Profile', 
				'AttachmentsLink.foreign_id' => $id)
		)));
		
		$this->set('sidebar', 'profiles'.DS.'edit_avatar');
	}

/**
 * add function
 *
 * @param mixed $mode
 * @param mixed $pass_data
 * @param mixed $data
 * @access public
 * @return void
 */
	function add($mode, $pass_data, $data) {
		if (!empty($this->data)) {
			
			if (empty($this->data['Profile']['id'])) {
				$this->data['Profile']['creator_id'] = $this->Auth->user('id');
			}
			$this->data['Profile']['modifier_id'] = $this->Auth->user('id');
			
			$this->data['Profile']['d_n'] = trim(trim($this->data['Profile']['fn'].' '.
				$this->data['Profile']['mn']).' '.$this->data['Profile']['ln']);
			
			// this is weird... when passing whole array to save, profile
			// wants to add custom unit.. because of HABTM Union
			$this->Profile->create(array('Profile' => $this->data['Profile']));
			if ($this->Profile->save()) {
				if (empty($this->data['Union']['id'])) {
					$this->Profile->Union->create();
					$this->Profile->Union->save($this->data);
					$this->data['Unit']['union_id'] = $this->Profile->Union->id;
					
					// create new unit if neccesary
					$new_unit = array('Unit' => array(
						'union_id' => $this->Profile->Union->id,
						'profile_id' => $this->data['Profile']['parent_id'],
					));
					if ($mode=='add_child' || $mode=='add_partner') {
						$new_unit['Unit']['kind'] = 'p';
					} else if ($mode=='add_parent' || $mode=='add_sibling') {
						$new_unit['Unit']['kind'] = 'c';
					}
					$this->Profile->Unit->create($new_unit);
					$this->Profile->Unit->save();
					
				} else {
					$this->data['Unit']['union_id'] = $this->data['Union']['id'];
				}
				
				$this->data['Unit']['profile_id'] = $this->Profile->id;
				$this->Profile->Unit->create($this->data);
				$this->Profile->Unit->save();

				$referer = trim(base64_decode(@$this->data['Profile']['referer']));
				if ($referer!='') {
					$this->redirect(array_merge(
						$this->parseUrl($referer), array('highlight'=>$this->Profile->id)
					));
				} else {
					$this->redirect(array('action'=>'view', $this->Profile->id));
				}
			} else {
				$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			$this->data = $pass_data;
			$this->data['Profile']['l'] = 1;
			$this->data['Profile']['g'] = 'm';
			$this->data['Profile']['ln'] = $data['Profile']['ln'];
			$this->data['Profile']['referer'] = base64_encode($this->referer(''));
		}
		$this->set('sidebar', '');
	}
/**
 * add_child function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function add_child($id=null) {
		if (empty($id) && !empty($this->data['Profile']['parent_id'])) {
			$id = $this->data['Profile']['parent_id'];
		}
		
		if (!$data = $this->Profile->read(array('id', 'd_n', 'ln'), $id)) {
			$this->error404();
			return;
		}
		
		// fetch all unions
		$unions = $this->Profile->Unit->find('all', array(
			'conditions' => array(
				'profile_id' => $id,
				'kind'       => 'p'
			),
			'fields' => array('union_id'),
			'recursive' => -1
		));
		$unions = Set::extract($unions, '{n}.Unit.union_id');
		
		// set data to pass to add() function
		$pass_data = array();
		if (empty($this->data)) {
			// select first union		
			$pass_data['Union']['id'] = reset($unions);
			
			$pass_data['Unit']['kind'] = 'c';
			$pass_data['Profile']['parent_id'] = $data['Profile']['id'];
		}
		
		// show available unions for child
		//if (sizeof($unions) > 1) {
			$this->Profile->Unit->contain(array('Profile'=>array('d_n', 'mdn', 'ln')));
			$marriages = $this->Profile->Unit->find('all', array(
				'conditions' => array(
					'Unit.kind'     => 'p',
					'Unit.union_id' => $unions,
					'NOT'           => array('Unit.profile_id' => $id)
				)
			));
			
			$mr = array();

			foreach ($marriages as $marriage) {
				if
				(
					!empty($marriage['Profile']['mdn']) && 
					$marriage['Profile']['mdn'] != $marriage['Profile']['ln']
				)
				{
					$marriage['Profile']['d_n'] .= ' ('.$marriage['Profile']['mdn'].')';
				}
				$mr[$marriage['Unit']['union_id']] = $marriage['Profile']['d_n'];
			}
			$this->set('marriages', $mr);
		//}
		
		$this->set('mode', 'add_child');
		$this->set('profile', $data);
		$this->setAction('add', 'add_child', $pass_data, $data);
	}
/**
 * add_sibling function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function add_sibling($id=null) {
		if (empty($id) && !empty($this->data['Profile']['parent_id'])) {
			$id = $this->data['Profile']['parent_id'];
		}
		
		if (!$data = $this->Profile->read(array('id', 'd_n', 'ln'), $id)) {
			$this->error404();
			return;
		}
		
		$pass_data = array();
		if (empty($this->data)) {
			if ($union_id = $this->Profile->Unit->field('union_id', array(
				'profile_id' => $id,
				'kind' => 'c')))
      		{
				$pass_data['Union']['id'] = $union_id;
			}
			$pass_data['Unit']['kind'] = 'c';
			$pass_data['Profile']['parent_id'] = $data['Profile']['id'];
		}
		
		$this->set('mode', 'add_sibling');
		$this->set('profile', $data);
		$this->setAction('add', 'add_sibling', $pass_data, $data);
	}
/**
 * add_partner function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function add_partner($id=null) {
		if (empty($id) && !empty($this->data['Profile']['parent_id'])) {
			$id = $this->data['Profile']['parent_id'];
		}
		
		if (!$data = $this->Profile->read(array('id', 'd_n', 'ln'), $id)) {
			$this->error404();
			return;
		}
		
		$marriage = $this->Profile->family($id, 'marriages', true);
		$unions = array();
		foreach ($marriage as $union_id => $family) {
			if (!$family['spouse']) {
				$unions[$union_id] = 
					__('Parent of', true) . ' ' . 
					implode(',', Set::extract($family['children'], '{n}.Profile.d_n'));
			}
		}
		
		
		$pass_data = array();
		if (empty($this->data)) {
			$pass_data['Union']['id'] = reset(array_keys($unions));
			$pass_data['Unit']['kind'] = 'p';
			$pass_data['Profile']['parent_id'] = $data['Profile']['id'];
		}
		
		$this->set('marriages', $unions);
		$this->set('mode', 'add_partner');
		$this->set('profile', $data);
		$this->setAction('add', 'add_partner', $pass_data, $data);
	}
/**
 * add_parent function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function add_parent($id=null) {
		if (empty($id) && !empty($this->data['Profile']['parent_id'])) {
			$id = $this->data['Profile']['parent_id'];
		}
		
		// fetch as child
		if (!$data = $this->Profile->read(array('id', 'd_n', 'ln'), $id)) {
			$this->error404();
			return;
		}
		
		$pass_data = array();
		if (empty($this->data)) {
		
			if ($union_id = $this->Profile->Unit->field('union_id', array(
				'profile_id' => $id,
				'kind' => 'c')))
      		{
				$pass_data['Union']['id'] = $union_id;
			}
			
			$pass_data['Unit']['kind'] = 'p';
			$pass_data['Profile']['parent_id'] = $id;
		}
		
		$this->set('mode', 'add_parent');
		$this->set('profile', $data);
		$this->setAction('add', 'add_parent', $pass_data, $data);
	}
/**
 * reorder_children function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function reorder_children($id=null) {
		if (is_numeric($id)) {
			if (!$profile = $this->Profile->family($id)) {
				$this->error404();
			}
		} else {
			$this->error404();
		}
		
		if (!empty($this->data)) {
			foreach ($this->data as $unit) {
				$this->Profile->Unit->save($unit);
			}
			
			// redirect
			$referer = trim(base64_decode(@$this->data['Unit']['referer']));
			if ($referer!='') {
				$this->redirect($this->parseUrl($referer));
			} else {
				$this->redirect(array('action'=>'view', $id));
			}
		} else {
			$this->data['Unit']['referer'] = base64_encode($this->referer(''));
		}
		
		foreach ($profile['marriages'] as $marriage) {
			foreach ($marriage['children'] as $child) {	
				$this->data['Unit'][] = $child['Unit'];
			}
		}
		
		$this->set('sidebar', 'profiles'.DS.'reorder_children');
		$this->set('parent_id', $id);
		$this->set('profile', $profile);
	}
/**
 * autocomplete function
 *
 * @access public
 * @return void
 */
	function autocomplete() {
		$this->layout = 'ajax';
		Configure::write('debug', 0);
		
		$l = 50;
		if (!empty($this->params['url']['limit']) && is_numeric($this->params['url']['limit'])) {
			$l = $this->params['url']['limit'];
		}
		
		$c = array();
		if (!empty($this->params['url']['q'])) {
			$c['Profile.d_n LIKE'] = '%'.$this->params['url']['q'].'%';
		}
		$this->set('profiles', $this->Profile->find('all', array('limit'=>$l, 'conditions'=>$c)));
	}
}
?>