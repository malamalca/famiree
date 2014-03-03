<h1><?php __('Change Password'); ?></h1>
<div class="form">
<?php
	echo $form->create($LilAuthUserModel, array('url' => Router::url(null, true)));
	echo $form->input('redirect', array('type' => 'hidden'));
	echo $form->input('id', array('type' => 'hidden'));
	echo $form->input('old_pass', array('type' => 'text', 'label' => __('Current Password', true) . ':'));
	
	echo $form->input('p', array('type' => 'text', 'label' => __('New Password', true) . ':'));
	echo $form->input('new_pass', array(
		'type'  => 'text',
		'label' => __('Repeat Password', true) . ':',
		'error' => __('Passwords do not match.', true)
	));

	echo $form->end(__('OK', true));
?>
</div>