<?php
/* SVN FILE: $Id: post.php 127 2009-07-03 18:06:10Z miha@nahtigal.com $ */
/**
 * Short description for user_controller.php
 *
 * Long description for user_controller.php
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
 * @package       lil_users
 * @subpackage    lil_users.controllers
 * @since         v 1.0
 * @version       $Revision: 127 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-07-03 20:06:10 +0200 (pet, 03 jul 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * UsersController class
 *
 * @uses          LilUsersAppController
 * @package       lil_users
 * @subpackage    lil_users.controllers
 */
class UsersController extends LilUsersAppController {
/**
 * name property
 *
 * @var string 'User'
 * @access public
 */
	var $name = 'Users';
/**
 * view property
 *
 * @var string 'Theme'
 * @access public
 */
	var $view = 'Theme';
/**
 * components property
 *
 * @var array
 * @access public
 */
	var $components = array('Auth', 'Email', 'Cookie', 'Security');
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Form');
/**
 * beforeFilter function
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		if ($theme = Configure::read('LilUsers.theme')) {
			$this->theme = $theme;
		}
		
		parent::beforeFilter();
		
		$this->Auth->allowedActions = array('logout', 'reset', 'verify');
		$this->Auth->autoRedirect   = false;
	}
/**
 * login function
 *
 * @access public
 * @return void
 */
	function login() {
		// function will execute only when autoRedirect was set to false (i.e. in a beforeFilter).
		if (empty($this->data)) {
			$cookie = $this->Cookie->read($this->Auth->sessionKey);
			if (!is_null($cookie) && $this->Auth->login($cookie)) {
				// update last login date and time
				$this->User->id = $this->Auth->user('id');
				$this->User->saveField('last_login', strftime('%Y-%m-%d %H:%M:%S'));
				
				//  Clear auth message, just in case we use it.
				$this->Session->delete('Message.auth');
				$this->redirect($this->Auth->redirect());
			}
		}
				
		if ($this->User->id = $this->Auth->user('id')) {
			$this->User->saveField('last_login', strftime('%Y-%m-%d %H:%M:%S'));
			
			// TODO: implement afterLogin() callback
			//$settings = $this->Profile->Setting->applyUserSettings($this->Auth->user('id'));
			//$this->Session->write('lang', Configure::read('Config.language'));
			
			if (!empty($this->data[$this->Auth->userModel]['remember_me'])) {
				$cookie = array();
				$cookie[$this->Auth->fields['username']] = 
					$this->data[$this->Auth->userModel][$this->Auth->fields['username']];
				$cookie[$this->Auth->fields['password']] = 
					$this->data[$this->Auth->userModel][$this->Auth->fields['password']];
				$this->Cookie->write($this->Auth->sessionKey, $cookie, true, '+2 weeks');
				unset($this->data[$this->Auth->userModel]['remember_me']);
			}
			$this->redirect($this->Auth->redirect());
		}

		$this->set('loginFields', $this->Auth->fields);
	}
/**
 * logout function
 *
 * @access public
 * @return void
 */
	function logout() {
		if ($this->Cookie->read($this->Auth->sessionKey)) $this->Cookie->delete($this->Auth->sessionKey);
		$this->Auth->logout();
		$this->Session->destroy();
		$this->Session->setFlash(__d('lil_users', 'User has been logged out.', true), 'default', array(), 'auth');
		$this->redirect($this->Auth->logoutRedirect);
	}
/**
 * changePassword function
 *
 * @access public
 * @return void
 */
	function change_password() {
		if (!empty($this->data)) {
			if ($this->User->changePassword($this->data)) {
				$this->Session->setFlash(__('Password have been successfully changed.', true));
				$this->redirect(Router::url(null, true));
			} else {
				$this->Session->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			$this->data['User']['id'] = $this->Auth->user('id');
		}
	}
/**
 * reset function
 *
 * @access public
 * @return void
 */
	/*function reset() {
		if(!empty($this->data['User']['email'])) {

			$email = $this->data['User']['email'];
			if(empty($email)) {
				$this->Session->setFlash('Please enter an email.');
				$this->set('error',array('email_missing' => true));
				$this->render(); exit;
			}

			$this->User->recursive = -1;
			if ($this->User->findCount(array('User.email' => $email), -1)) {
				$this->User->saveTempPassword($email);
				$user = $this->User->find(array('User.email' => $email));
				$this->Email->to = $user['User']['email'];
				$this->Email->from = Configure::read('Site.email');
				$this->Email->subject = Configure::read('Site.name') . ' new password request' ;
				$this->Email->template = null;

				$content[] = 'A request to reset you password has been submitted.';
				$content[] = 'Please visit the following url to have your temporary password sent';
				$content[] = Router::url('/users/verify/reset/'.$user['User']['email_token'], true);

				if($this->Email->send($content)) {
					$this->Session->setFlash('You should receive an email with further instruction shortly');
					$this->set($user);
					$this->redirect('/', null, true);
				}
			} else {
				$this->User->invalidate('email', 'The email you entered was not found');
			}
		}
	}
/**
 * verify function
 *
 * @param string $type
 * @access public
 * @return void
 */
	/*function verify($type = 'email') {
		if(isset($this->passedArgs['1'])){
			$token = $this->passedArgs['1'];
		} else {
			$this->Session->setFlash('Invalid verification token.');
			$this->render(); exit;
		}
		if($type === 'email') {
			$data = $this->User->validateToken($token);
		} elseif($type === 'reset') {
			$data = $this->User->validateToken($token, true);
		} else {
			$this->Session->setFlash('There url you accessed is no longer valid');
			$this->redirect(array('action' => 'login'));
		}

		$password = $data['User']['psword'];
		$data = $this->Auth->hashPasswords($data);

		if($data !== false){
			$email = $data['User']['email'];
			unset($data['User']['email']);
			if($this->User->save($data, false)) {
				if($type === 'reset'){
			        $this->Email->to = $email;
					$this->Email->from = Configure::read('Site.email');
					$this->Email->subject = Configure::read('Site.name') . ' password reset' ;
					$this->Email->template = null;
					$content[] = 'Your password has been reset';
					$content[] = 'Please login using';
					$content[] = 'Username: ' . $data['User']['username'];
					$content[] = 'Password: ' . $password;
					$this->Email->send($content);
					$this->Session->setFlash('Your password was sent to your registered email account');
				} else {
					$this->Session->setFlash('Your Email was validated, Please Login');
					$this->redirect(array('action' => 'login'));
				}
			} else {
				$this->Session->setFlash('There was an error trying to validate, check your email and the url entered');
			}
		} else {
			$this->Session->setFlash('There url you accessed is no longer valid');
			$this->redirect(array('action' => 'login'));
		}
	}*/

}
?>