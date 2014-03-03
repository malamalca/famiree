<?php
/* SVN FILE: $Id: profile.php 158 2010-01-19 18:16:42Z miha.nahtigal $ */
/**
 * Short description for profile.php
 *
 * Long description for profile.php
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
 * @subpackage    famiree.app.models
 * @since         v 1.0
 * @version       $Revision: 158 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-19 19:16:42 +0100 (tor, 19 jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 /**
 * Profile class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class Profile extends AppModel {
/**
 * name property
 *
 * @var string
 * @access public
 */
	var $name = 'Profile';
/**
 * displayField property
 *
 * @var string
 * @access public
 */
	var $displayField = 'd_n';
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'LilLog.LilLog' => array(
			'userModel' => 'Profile', 
			'userKey' => 'user_id', 
			'change' => 'serialize',
			'description_ids' => false,
			'classField' => 'class',
			'foreignKey' => 'foreign_id',
			'skip' => array('login'),
		),
		/*'LilSearch.LilSearch' => array(
			'contain' => array()
		),*/
		'FamilyTree'
	);
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Creator' => array(
			'className'  => 'Profile',
			'foreignKey' => 'creator_id'
		),
		'Modifier' => array(
			'className'  => 'Profile',
			'foreignKey' => 'modifier_id'
		),
	);
/**
 * hasOne property
 *
 * @var array
 * @access public
 */
	var $hasOne = array(
		'Setting' => array(
			'dependant' => true
		),
	);	
/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	var $hasMany = array(
		'Unit' => array(
			'order' => 'Unit.kind DESC',
		),
	);
/**
 * hasAndBelongsToMany property
 *
 * @var array
 * @access public
 */
	var $hasAndBelongsToMany = array(
		'Union' => array(
			'joinTable'  => 'units',
			'with'       => 'Unit',
		),
		'Attachment' => array(
			'joinTable'  => 'attachments_links',
			'foreignKey' => 'foreign_id',
			'conditions' => array('AttachmentsLink.class' => 'Profile'),
			'order'      => 'Attachment.created',
			'with'       => 'AttachmentsLink'
		),
		'Post' => array(
			'className'  => 'LilBlogs.Post',
			'joinTable'  => 'posts_links',
			'foreignKey' => 'foreign_id',
			'associationForeignKey' => 'post_id',
			'conditions' => array('PostsLink.class' => 'Profile'),
			'order'      => 'Post.id DESC',
			'with'       => 'PostsLink'
		),
	);
/**
 * canDelete method
 *
 * Check if Profile can be deleted. Person can be deleted only if it doesn't have children or
 * spouses. Otherwise there might be orphaned nodes.
 *
 * @param mixed $id profile_id
 * @access public
 * @return bool
 */
	function canDelete($id = null) {
		if (is_numeric($id) && ($family = $this->family($id, array('children', 'marriages')))) {
			return (empty($family['children']) && empty($family['marriages']));
		}
		return false;
	}
/**
 * family method
 *
 * Fetch nearest family for specified profile
 *
 * @param mixed $id profile_id
 * @param mixed $type Which part of family to fetch: parents, children, siblings, marriages, spouses
 * @param boolean $assoc Fetch associativly (array keys equal profile id)
 * @access public
 * @return array Family for given Profile id
 */
	function family($id, $type = null, $assoc = false) {
		$ret = array();
		
		$data = $this->find('first', array('conditions' => array('Profile.id' => $id), 'contain' => 'Union'));
		$family = reset(Set::extract('/Unit[kind=c]/union_id', $data['Union']));
		
		$parents = array();	$siblings = array();
		if (!empty($family)) {
			if (empty($type) || $type=='parents' || (is_array($type) && in_array('parents', $type))) {
				$parents = $this->Unit->find('all', array(
					'conditions' => array(
						'Unit.union_id'	=> $family,
						'Unit.kind' => 'p'
					),
					'contain' => 'Profile',
					'order' => 'Profile.g DESC',
				));
				
				
				if ($assoc) {
					$parents2 = array();
					foreach ($parents as $parent) {
						$parents2[$parent['Profile']['id']] = $parent;
					}
					$parents = $parents2;
				}
				
				if ($type=='parents') return $parents;
				$ret['parents'] = $parents;
			}
			
			if (empty($type) || $type=='siblings' || (is_array($type) && in_array('siblings', $type))) {
				$siblings = $this->Unit->find('all', array(
					'conditions' => array(
						'Unit.union_id'	=> $family,
						'Unit.kind' => 'c',
						'NOT' => array('Unit.profile_id'=>$id),
					),
					'contain' => 'Profile',
					'order' => 'Unit.sort_order, Profile.dob_y'
				));
				
				if ($assoc) {
					$siblings2 = array();
					foreach ($siblings as $sibling) {
						$siblings2[$sibling['Profile']['id']] = $sibling;
					}
					$siblings = $siblings2;
				}
				
				if ($type=='siblings') return $siblings;
				$ret['siblings'] = $siblings;
			}
		}
		
		$marr = Set::extract('/Unit[kind=p]/union_id', $data['Union']);
		
		if (empty($type) || $type=='marriages' || $type=='spouses' || $type=='children' || 
			(is_array($type) && (in_array('marriages', $type) || in_array('spouses', $type) || in_array('children', $type)))) {
		
			$marriages = array(); $spouses = array(); $children = array();
			foreach ($marr as $m_k=>$union_id) {
				if ($assoc) {
					$marriage_key = $union_id;
				} else {
					$marriage_key = $m_k;
				}
				
				$marriages[$marriage_key]['spouse'] = $this->Unit->find('first', array(
					'conditions' => array(
						'Unit.union_id'	=> $union_id,
						'Unit.kind' => 'p',
						'NOT' => array('Unit.profile_id' => $id),
					),
					'contain' => 'Profile'
				));
				if ($assoc) {
					$spouses[$marriages[$marriage_key]['spouse']['Profile']['id']] = $marriages[$marriage_key]['spouse'];
				} else {
					$spouses[] = $marriages[$marriage_key]['spouse'];
				}
				
				if (empty($type) || $type=='marriages' || $type=='children' || 
					(is_array($type) && (in_array('marriages', $type) || (in_array('marriages', $type) || in_array('children', $type))))) {
					$marriages[$marriage_key]['children'] = $this->Unit->find('all', array(
						'conditions' => array(
							'Unit.union_id'	=> $union_id,
							'Unit.kind' => 'c'
						),
						'contain' => 'Profile',
						'order' => 'Unit.sort_order, Profile.dob_y'
					));
					
					if ($assoc) {
						foreach ($marriages[$marriage_key]['children'] as $child) {
							$children[$child['Profile']['id']] = $child;
						}
					} else {
						$children = array_merge($children, $marriages[$marriage_key]['children']);
					}
				}
			}
			if (empty($type) || is_array($type)) {
				if (empty($type) || in_array('marriages', $type)) {
					$ret['marriages'] = $marriages;
				}
				if (empty($type) || in_array('spouses', $type)) {
					$ret['spouses'] = $marriages;
				}
				if (empty($type) || in_array('children', $type)) {
					$ret['children'] = $children;
				}
			} else {
				if ($type=='marriages') {
					return $marriages;
				} else if ($type=='spouses') {
					return $spouses;
				} else {
					return $children;
				}
			}
		}
		
		return $ret;
	}
/**
 * afterSave method
 *
 * Update event dates
 *
 * @param boolean $created
 * @access private
 * @return void
 */
	function afterSave($created) {
		if (!empty($this->data['Profile']['dob_y']) &&
			!empty($this->data['Profile']['dob_m']) &&
			!empty($this->data['Profile']['dob_d']) &&
			checkdate(
				$this->data['Profile']['dob_m'], 
				$this->data['Profile']['dob_d'],
				$this->data['Profile']['dob_y']
			)
		) {
			$EventDate =& ClassRegistry::init('EventDate');
			$data['EventDate']['class'] = 'Profile';
			$data['EventDate']['foreign_id'] = $this->id;
			$data['EventDate']['kind'] = 'dob';
			$data['EventDate']['date_start'] = $this->data['Profile']['dob_y'].'-'.
				$this->data['Profile']['dob_m'].'-'.$this->data['Profile']['dob_d'];
			if ($id = $EventDate->field('id', array('EventDate.class'=>'Profile', 'EventDate.foreign_id'=>$this->id))) {
				$data['EventDate']['id'] = $id;
			}
			
			if (!empty($this->data['Profile']['dod_y']) &&
				!empty($this->data['Profile']['dod_m']) &&
				!empty($this->data['Profile']['dod_d']) &&
				checkdate(
					$this->data['Profile']['dod_m'], 
					$this->data['Profile']['dod_d'],
					$this->data['Profile']['dod_y']
				)
			) {
				$data['EventDate']['date_end'] = $this->data['Profile']['dod_y'].'-'.
					$this->data['Profile']['dod_m'].'-'.$this->data['Profile']['dod_d'];
			}
			
			$EventDate->save($data);
		}
		return true;
	}
}
?>