<?php
/* SVN FILE: $Id: pages_controller.php 159 2010-01-23 17:48:06Z miha.nahtigal $ */
/**
 * Short description for pages_controller.php
 *
 * Long description for pages_controller.php
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
 * PagesController class
 *
 * @uses          AppController
 * @package       base
 * @subpackage    base.controllers
 */
class PagesController extends AppController {
/**
 * name property
 *
 * @var string 'Pages'
 * @access public
 */
 	var $name = 'Pages';
/**
 * cacheAction property
 *
 * @var mixed
 * @access public
 */
	var $cacheAction = true;
/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = array('Profile', 'LilLogs.Log', 'LilBlogs.Post');
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
		'Post' => array(
			'limit' => 5,
			'order' => array(
				'Post.id' => 'desc'
			),
		)
	);
/**
 * dashboard function
 *
 * @access public
 * @return void
 */
 	function dashboard() {
		$this->set('posts', $this->Post->find('all', array(
			'conditions' => array('Post.status' => 2),
			'order'      => array('Post.id' => 'desc'),
			'limit'      => 8,
			'contain'    => array('Author', 'Category')
		)));
 		
 		$this->set('count_total', $this->Profile->find('count'));
 		$this->set('count_m', $this->Profile->find('count', array('conditions'=>array('g'=>'m'), 'contain'=>array())));
 		$this->set('count_f', $this->Profile->find('count', array('conditions'=>array('g'=>'f'), 'contain'=>array())));
 		
 		$this->set('sidebar', 'pages' . DS . 'dashboard');
 		
 		$dates = array();
 		$dates = array();
 		
 		// this year
 		$birthdays = $this->Profile->find('all', array(
		 	'fields' => array('id', 'd_n', 'dob_y', 'dob_m', 'dob_d', 'g'), 
		 	'conditions' => array(
			 	'Profile.l' => true, 
				'Profile.dob_y <>' => '',
				'OR' => array(
					0 => array(
						'Profile.dob_m >' => strftime('%m'),
						'Profile.dob_d >' => 0,
					),
					1 => array(
						'Profile.dob_m =' => strftime('%m'),
						'Profile.dob_d >=' => strftime('%d'),
					),
				)
			),
			'order' => array(
				'Profile.dob_m',
				'Profile.dob_d'
			),
			'limit' => 20
		));
		foreach ($birthdays as $bd) {
			$dates[$bd['Profile']['dob_m']][$bd['Profile']['dob_d']]['Birthdays'][$bd['Profile']['id']] = array('id' => $bd['Profile']['id'], 'd_n' => $bd['Profile']['d_n'], 'dob_y' => $bd['Profile']['dob_y'], 'g' => $bd['Profile']['g']);
		}
		
		// show for next year
		if (sizeof($birthdays) < 20) {
	 		$birthdays = $this->Profile->find('all', array(
			 	'fields' => array('id', 'd_n', 'dob_y', 'dob_m', 'dob_d', 'g'), 
			 	'conditions' => array(
				 	'Profile.l' => true, 
					'Profile.dob_y <>' => '',
					'OR' => array(
						0 => array(
							'Profile.dob_m <' => strftime('%m'),
							'Profile.dob_d >' => 0,
						),
						1 => array(
							'Profile.dob_m =' => strftime('%m'),
							'Profile.dob_d >' => 0,
							'Profile.dob_d <' => strftime('%d'),
						),
					)
				),
				'order' => array(
					'Profile.dob_m',
					'Profile.dob_d'
				),
				'limit' => 20-sizeof($birthdays)
			));
			foreach ($birthdays as $bd) {
				$dates[$bd['Profile']['dob_m']][$bd['Profile']['dob_d']]['Birthdays'][$bd['Profile']['id']] = array('id' => $bd['Profile']['id'], 'd_n' => $bd['Profile']['d_n'], 'dob_y' => $bd['Profile']['dob_y'], 'g' => $bd['Profile']['g']);
			}
		}
		
		$this->set(compact('dates'));
 		
 		// do not put line below inside find() because containable doesnt see it
 		// and uses default -1 instead
 		$this->Log->bindModel(
 			array(
 				'belongsTo' => array(
	 				'User' => array(
						'className'  => 'Profile',
						'foreignKey' => 'user_id'
					),
					'Imgnote' => array(
						'foreignKey' => 'foreign_id',
						'conditions' => array('Log.class' => 'Imgnote')
					),
					'Memory' => array(
						'className'  => 'LilBlogs.post',
						'foreignKey' => 'foreign_id',
						'conditions' => array('Log.class' => 'Memory')
					),
					'Profile' => array(
						'foreignKey' => 'foreign_id',
						'conditions' => array('Log.class' => 'Profile')
					),
					'Attachment' => array(
						'foreignKey' => 'foreign_id',
						'conditions' => array('Log.class' => 'Attachment')
					)
 				),
 			)
 		);
 		$this->set('recent_changes', $this->Log->find('all', array(
		 	'limit'   => 5,
		 	'contain' => array('User', 'Imgnote', 'Memory', 'Profile', 'Attachment'),
		 	'order'   => 'Log.id DESC'
		 )));
 	}
}
?>