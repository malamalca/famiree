<?php
	$this->set('sidebar', 'settings'.DS.'index');
?>
<div class="form" id="ProfileSettings">
	<div class="tab" id="ProfileSettingsTabChangePassword">
		<h1><?php __('Change Password'); ?></h1>
		<?php
			echo $this->Form->create('User', array('url' => Router::url(null, true)));
			echo $this->Form->input('id', array('type' => 'hidden'));
			echo $this->Form->input('old_pass', array(
				'type' => 'password',
				'label' => __('Current Password', true) . ':',
				'error' => __('Password does not match to your current password.', true)
			));
			
			echo $this->Form->input('p', array(
				'type' => 'password',
				'label' => __('New Password', true) . ':',
				'error' => __('Please enter your new password.', true)
			));
			echo $this->Form->input('new_pass', array(
				'type'  => 'password',
				'label' => __('Repeat Password', true) . ':',
				'error' => __('Repeated password does not match to password above.', true)
			));
			
			// submit button
			echo '<div class="input submit">';
			echo $this->Form->submit(__('Save', true), array('div' => false));
			
			if ($referer = trim(base64_decode($this->Html->value('User.referer')))) {
				echo ' ' . __('or', true) . ' ' . $this->Html->link(__('Cancel', true), $referer);
			}
			
			echo '</div>';
			echo $this->Form->end();
		?>
	</div>
</div>