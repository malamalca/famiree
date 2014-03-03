<?php
/* SVN FILE: $Id: imgnotes_controller.php 97 2009-06-30 18:42:59Z miha.nahtigal $ */
/**
 * Short description for settings_controller.php
 *
 * Long description for settings_controller.php
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
 * @version       $Revision: 97 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-06-30 20:42:59 +0200 (tor, 30 jun 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * SettingsController class
 *
 * @uses          AppController
 * @package       base
 * @subpackage    base.controllers
 */
class SettingsController extends AppController {
/**
 * name property
 *
 * @var string 'Settings'
 * @access public
 */
	var $name = 'Settings';
/**
 * datetime function
 *
 * @access public
 * @return void
 */
	function lang() {
		if (!empty($this->data)) {
			if ($data = $this->Setting->save($this->data)) {
				Configure::write('Config.language', $data['Setting']['locale']);
				$this->Session->write('lang', $data['Setting']['locale']);

				$this->Session->setFlash(__('Default language has been successfully set.', true));
				$this->redirect(Router::url(null, true));
			} else {
				$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			if (!$this->data = $this->Setting->findForUser($this->Auth->user('id'))) {
				// fill data with default values
				$this->data['Setting']['profile_id'] = $this->Auth->user('id');
				$this->data['Setting']['locale'] = Configure::read('Config.language');
			}
		}
		
		$this->set('languages', $this->Setting->getAvailableLanguages());
		
		$this->set('sidebar', 'settings'.DS.'index');
	}
/**
 * datetime function
 *
 * @access public
 * @return void
 */
	function datetime() {
		if (!empty($this->data)) {
			if ($data = $this->Setting->save($this->data)) {
				$this->Setting->applyUserSettings($this->Auth->user('id'));
				
				$this->Session->setFlash(__('Settings have been successfully updated.', true));
				$this->redirect(Router::url(null, true));
			} else {
				$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			if (!$this->data = $this->Setting->findForUser($this->Auth->user('id'))) {
				// fill data with default values
				$this->data['Setting']['profile_id'] = $this->Auth->user('id');
				
				$this->data['Setting']['date_order'] = Configure::read('dateFormat');
				$this->data['Setting']['date_separator'] = Configure::read('dateSeparator');
				$this->data['Setting']['date_24hr'] = (Configure::read('timeFormat') == '24');
				
				$this->data['Setting']['datef_common'] = Configure::read('outputDateFormat');
				$this->data['Setting']['datef_noyear'] = Configure::read('noYearDateFormat');
				$this->data['Setting']['datef_short'] = Configure::read('shortDateFormat');
			}
		}
		$this->set('sidebar', 'settings' . DS . 'index');
	}
/**
 * maintenance function
 *
 * @access public
 * @return void
 */
	function maintenance() {
		if (!empty($this->params['named']['operation'])) {
			switch ($this->params['named']['operation']) {
				case 'rebuild_index':
					$Profile =& ClassRegistry::init('Profile');
					$Profile->lilSearchOptimize();
					break;
			}
		}
		$this->set('sidebar', 'settings'.DS.'index');
	}
}
?>