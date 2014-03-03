<div class="head">
	<h1><?php __d('lil_blogs', 'Add an Author'); ?></h1>
</div>
<div class="form">
<?php
	echo $form->create();
	echo $form->input('name',  array('label'=>__d('lil_blogs', 'Name', true).':', 'error' => __d('lil_blogs', 'Name is required.', true)));
	echo $form->input('email', array('label'=>__d('lil_blogs', 'Email', true).':', 'error' => __d('lil_blogs', 'Email is required, format must be valid.', true)));
	echo $form->submit(__d('lil_blogs', 'Create', true));
	echo $form->end();
?>
</div>
