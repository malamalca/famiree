<?php
/* SVN FILE: $Id: union.php 124 2009-12-01 19:57:45Z miha.nahtigal $ */
/**
 * Short description for union.php
 *
 * Long description for union.php
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
 * @version       $Revision: 124 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-12-01 20:57:45 +0100 (tor, 01 dec 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 /**
 * Union class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class Union extends AppModel {
/**
 * name property
 *
 * @var string 'Union'
 * @access public
 */
	var $name = 'Union';
/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	var $hasMany = array(
		'Unit' => array(
			'order' => 'Unit.kind'
		),
	);
	var $xml_field_list = array(
		'id', 'd_n', 'mdn', 'ln', 'fn',
		'g', 'l', 'dob_y', 'dod_y', 'ta',
		'cn_med', 'cn_mem'
	);
/**
 * fetchAsParent2 method
 *
 * Fetch union containing passed profile_id as parent.
 * Return formatted array in form $union = array('p'=>array(..parents), 'c'=>array(..children))
 *
 * @param int $profile_id
 * @param bool $fetchProfiles Additionaly to unit, fetch all profiles into separate array.
 * @access public
 * @return array
 */
	function fetchAsChild2($profile_id, $fetchProfiles=false) {
		if ($union_id = $this->Unit->field('union_id', array('profile_id'=>$profile_id, 'kind'=>'c'))) {
			if ($data = $this->Unit->find('all', array(
				'conditions'=>array('Unit.union_id'=>$union_id),
				'contain' => (($fetchProfiles)?array('Profile'):array()),
				'order' => 'Unit.sort_order'.(($fetchProfiles)?', Profile.dob_y':'')
			))) {
				$ret = array($union_id=>array('p'=>array(), 'c'=>array()));
				if ($fetchProfiles) $ret[$union_id]['Profile'] = array();
				foreach ($data as $key=>$unit) {
					if ($unit['Unit']['kind']=='p') {
						$ret[$union_id]['p'][] = $unit['Unit']['profile_id'];
					} else {
						$ret[$union_id]['c'][] = $unit['Unit']['profile_id'];
					}
					
					if ($fetchProfiles) {
						$unit['Profile']['n'] = $unit['Profile']['id'];
						$ret[$union_id]['Profile'][$unit['Profile']['id']] = $unit['Profile'];
					}
				}
				return $ret;
			} else return false;
		} else return false;
	}
	
/**
 * fetchAsParent2 method
 *
 * Fetch union containing passed profile_id as child.
 * Return formatted array in form $union = array('p'=>array(..parents), 'c'=>array(..children))
 *
 * @param int $profile_id
 * @param bool $fetchProfiles Additionaly to unit, fetch all profiles into separate array.
 * @access public
 * @return array
 */
	function fetchAsParent2($profile_id, $fetchProfiles=false) {
		if ($unions = $this->Unit->find('all', array('conditions'=>array('profile_id'=>$profile_id, 'kind'=>'p'), 'contain'=>array()))) {
			$ret = array();

			foreach ($unions as $union) {
				$union_id = $union['Unit']['union_id'];
				if ($data = $this->Unit->find('all', array(
					'conditions'=>array('Unit.union_id'=>$union_id),
					'contain' => (($fetchProfiles)?array('Profile'=>$this->xml_field_list):array()),
					'order' => 'Unit.sort_order'.(($fetchProfiles)?', Profile.dob_y':'')
				))) {
					$ret[$union_id]['p']=array();
					$ret[$union_id]['c']=array();
					if ($fetchProfiles) $ret[$union_id]['Profile'] = array();
					foreach ($data as $key=>$unit) {
						if ($unit['Unit']['kind']=='p') {
							$ret[$union_id]['p'][] = $unit['Unit']['profile_id'];
						} else {
							$ret[$union_id]['c'][] = $unit['Unit']['profile_id'];
						}
						if ($fetchProfiles) {
							$unit['Profile']['n'] = $unit['Profile']['id'];
							$ret[$union_id]['Profile'][$unit['Profile']['id']] = $unit['Profile'];
						}
					}
					
				}
			}
			return $ret;
		} else return false;
	}
}
?>