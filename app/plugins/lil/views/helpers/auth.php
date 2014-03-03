<?php
class AuthHelper extends Helper {
	var $sessionKey = null;
	var $userModel = null;
	
	function __construct() {
		parent::__construct();
		$this->sessionKey = 'Auth.Contact';
		$this->userModel = 'Contact';
	}
	
	function user($key = null) {
		$this->Session = new CakeSession();
		if ($this->Session->started() && $this->Session->valid($this->sessionKey)) {
			if ($key == null) {
				return array($this->userModel => $this->Session->read($this->sessionKey));
			} else {
				$user = $this->Session->read($this->sessionKey);
				if (isset($user[$key])) return $user[$key];
			}
		}
		return null;
	}
}
?>