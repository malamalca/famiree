<?php
/* SVN FILE: $Id: app_controller.php 159 2010-01-23 17:48:06Z miha.nahtigal $ */
/**
 * Short description for app_controller.php
 *
 * Long description for app_controller.php
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
 * @subpackage    famiree.app
 * @since         v 1.0
 * @version       $Revision: 159 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-23 18:48:06 +0100 (sob, 23 jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application controller for Cake.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       famiree
 * @subpackage    famiree.app
 */
class AppController extends Controller {
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Session', 'Html', 'Form', 'Js',
		'Lil.Date', 'Lil.Sanitize', 'Cache', 'Text', 
		'Number', 'Quicks'
	);
/**
 * components property
 *
 * @var array
 * @access public
 */
	var $components = array('Session', 'Auth', 'RequestHandler', 'LilUsers.LilAuth');
/**
 * persistModel property
 *
 * @var bool
 * @access public
 */	
	var $persistModel = false;
/**
 * beforeFilter function
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		// these are Auth settings all other functionality is handled by LilUsers.LilAuth component
		$this->Auth->userModel	= 'Profile';
		$this->Auth->fields		= array('username' => 'u', 'password' => 'p');
		$this->Auth->authorize	= 'controller';
		$this->Auth->sessionKey = 'Auth.User';
		
		// try to read user settings or apply default
		$settings = ClassRegistry::init('Setting');
		$settings->applySettings($this->Auth->user('id'));
		
		// required by lil_log plugin
		if (isset($this->{$this->modelClass}) && $this->{$this->modelClass}->Behaviors->attached('LilLog')) {
			$activeUser = array('Profile' => array(
				'id' => $this->Auth->user('id')?$this->Auth->user('id'):-1, 
				'd_n' => $this->Auth->user('d_n')?$this->Auth->user('d_n'):'')); 
			$this->{$this->modelClass}->setUserData($activeUser);
		}
	}
/**
 * isAuthorized function
 *
 * @access public
 * @return boolean
 */
	function isAuthorized() {
		return true;
	}
/**
 * setFlash function
 *
 * @param string $message
 * @param string $layout
 * @param boolean $flashOnAjax
 * @access public
 * @return void
 */
	function setFlash($message, $layout='default', $flashOnAjax=false) {
		if (!$this->RequestHandler->isAjax() || $flashOnAjax) {
			$this->Session->setFlash($message, $layout);
		}
	}
/**
 * parseUrl function
 *
 * @param string $url
 * @access public
 * @return string
 */
	function parseUrl($url) {
		$url = Router::parse($url);
		$url = am($url, $url['named'], $url['pass']);
		unset($url['named']); unset($url['pass']); unset($url['url']);
		return $url;
	}
/**
 * error404 function
 *
 * @access public
 * @return void
 */
	function error404() {
		$this->cakeError('error404', array());
	}
}
?>