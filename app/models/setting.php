<?php
/* SVN FILE: $Id: event_date.php 105 2009-08-04 19:02:09Z miha.nahtigal $ */
/**
 * Short description for setting.php
 *
 * Long description for setting.php
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
 * @version       $Revision: 105 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-08-04 21:02:09 +0200 (tor, 04 avg 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 /**
 * Setting class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class Setting extends AppModel {
/**
 * name property
 *
 * @var string 'Setting'
 * @access public
 */
	public $name = 'Setting';
/**
 * findForUser function
 *
 * @param mixed $id profile_id
 * @access public
 * @return mixed Results
 */
	public function findForUser($id = null) {
		return $this->find('first', array(
			'conditions' => array(
				'Setting.profile_id' => $id
			),
			'contain' => array()
		));
	}
/**
 * applySettings function
 *
 * @param mixed $id profile_id
 * @access public
 * @return void
 */
	public function applySettings($id = null, $forceReload = false) {
		App::import('core', 'CakeSession');
		$Session = new CakeSession();
		
		// init settings
		if (!$forceReload && ($settings = $Session->read('User.Settings'))) {
			$this->updateConfig($settings);
		} else if (!empty($id)) {
			$this->applyUserSettings($id);
		}
	}
/**
 * applyUserSettings function
 *
 * @param mixed $id profile_id
 * @access public
 * @return void
 */
	public function applyUserSettings($id = null) {
		if ($data = $this->findForUser($id)) {
			$this->updateConfig($data['Setting']);

			// write to session
			$session = new CakeSession(); 
			$session->write('User.Settings', $data['Setting']);
		}
	}
/**
 * applyUserSettings function
 *
 * @param mixed $settings
 * @access public
 * @return void
 */
	private function updateConfig($data = null) {
		Configure::write('dateFormat', $data['date_order']);
		Configure::write('dateSeparator', $data['date_separator']);
		Configure::write('timeFormat', $data['date_24hr'] ? '24' : '12');
		
		Configure::write('outputDateFormat', $data['datef_common']);
		Configure::write('noYearDateFormat', $data['datef_noyear']);
		Configure::write('shortDateFormat', $data['datef_short']);
		
		Configure::write('Config.language', $data['locale']);
	}
/**
 * getAvailableLanguages function
 *
 * @access public
 * @return mixed Results
 */
	public function getAvailableLanguages() {
		// find available languages
		$languages = array('eng');
		foreach (new DirectoryIterator(APP.'locale') as $fileInfo) {
		    if ($fileInfo->isDir() && !$fileInfo->isDot() && !($fileInfo->getFilename()=='.svn')) {;
		    	$languages[] = $fileInfo->getFilename();
		    }
		}
		
		App::import('Core', 'l10n');
		$l10n = new L10n();
		
		$result = array();
		foreach ($languages as $k => $lang) {
			if ($mapped = $l10n->map($lang)) {
				if ($l = $l10n->catalog($mapped)) {
					$result[$lang] = $l['language'];
				}
			}
		}
		
		return $result;
	}
}
?>