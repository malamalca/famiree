<?php

define('LILAUTH_ADMIN', '800');
define('LILAUTH_EDITOR', '700');
define('LILAUTH_MODERATOR', '600');
define('LILAUTH_COMMENTER', '300');
define('LILAUTH_READ', '200');
define('LILAUTH_NONE', '100');
define('LILAUTH_INVALID', '0');

class LilAuthComponent extends Object {

	var $components = array('Auth');

	function initialize() {
		$this->Auth->loginAction = array(
			'plugin'     => 'lil_users',
			'admin'      => null,
			'controller' => 'users',
			'action'     => 'login'
		);
		
		$this->Auth->loginRedirect	= '/';
		$this->Auth->logoutRedirect = '/';
		
		$this->Auth->userModel		= 'User';
		$this->Auth->sessionKey		= 'Auth.' . $this->Auth->userModel;
		$this->Auth->fields 		= array('username'=> 'username', 'password'=>'passwd');
		
		$this->Auth->authorize 		= 'object';
		$this->Auth->object 		= $this;
		$this->Auth->authenticate 	= $this;
		
		$this->Auth->loginError		= __d('lil_users', 'Wrong username or password. Please try again.', true);
		$this->Auth->authError		= __d('lil_users', 'Please login to continue.', true);
	}

	/*function startup(&$controller) {
		/*if($auth = $this->Auth->user()) {
			if (!empty($auth[$this->Auth->userModel]) && empty($auth[$this->Auth->userModel]['level'])) {
				$model = $this->Auth->getModel();
				$model->recursive = 0;
				$user = $model->read(
					array(
						$this->Auth->userModel.'.id', 
						$this->Auth->userModel.'.username',
						$this->Auth->userModel.'.level'
					), 
					$auth[$this->Auth->userModel]['id']);
				$this->Auth->Session->write('Auth.'.$this->Auth->userModel, 
					$user[$this->Auth->userModel]);
			}
		}
	}

	function hashPasswords($data) {
		if(!empty($data[$this->Auth->userModel][$this->Auth->fields['password']])) {
			$data[$this->Auth->userModel][$this->Auth->fields['password']] = 
				Security::hash($data[$this->Auth->userModel][$this->Auth->fields['password']]);
		}
		return $data;
	}*/ 

	function isAuthorized($user, $controller, $action) {
		if($this->Auth->user('level') == LILAUTH_ADMIN) {
			return true;
		}
		
		if (substr($action, 0, 6)=='admin_' && $this->Auth->user('level') < LILAUTH_EDITOR) {
			return false;
		}

		return true;
	}

	function beforeRender(&$controller) {
		$user = $this->Auth->user();
		$controller->set('Auth', $user);
		$controller->set('LilAuthUserModel', $this->Auth->userModel);
	}
}
?>