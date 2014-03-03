<h1><?php __('User Login'); ?></h1>
<div class="form">
<?php
	echo $form->create($LilAuthUserModel, array('url' => Router::url(null, true)));
	echo $form->hidden('redirect', array('value' => $session->read('Auth.redirect')));
?>
	<fieldset>
		<legend><?php __('Login') ?></legend>
<?php
	foreach($loginFields as $label => $field) {
	
		$input_type = 'text';
		$after = null;
		
		if ($label == 'password') {
			$input_type = 'password';
			$after = '<p>' . $html->link(__('Forgot your password?', true), array('admin'=> false, 'action' => 'reset')) .'</p>';
		}
		
		echo $form->input($field, array(
			'type'  => $input_type,
			'label' => Inflector::humanize($label) . ':',
			'after' => $after));
	}
?>
	</fieldset>
<?php
	echo $form->end(__('Login', true));
?>
</div>