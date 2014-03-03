<?php
	$this->set('title_for_layout', __('Login', true));
	$this->set('sidebar', null);
?>
<div class="form" id="ProfileLogin">
	<span id="YourFather"><?php __('Your Father'); ?></span>
	<span id="YourMother"><?php __('Your Mother'); ?></span>
<?php
	echo $this->Form->create('Profile', array('url' => Router::url(null, true)));
	echo $this->Form->hidden('redirect', array('value' => $session->read('Auth.redirect')));
	echo $this->Form->input('u', array('label' => __('Your name', true)));
	echo $this->Form->input('p', array('label' => __('Password', true), 'type' => 'password'));
	echo $this->Form->input('remember_me', array(
		'label' => __('Remember me', true),
		'type' => 'checkbox'
	));
	
	echo $this->Form->submit(__('Login', true));
	echo $this->Form->end();
?>
</div>