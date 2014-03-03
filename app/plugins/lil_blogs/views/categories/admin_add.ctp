<div class="head">
	<h1><?php __d('lil_blogs', 'Add a Category'); ?></h1>
</div>
<div class="form">
<?php
	echo $form->create();
	echo $form->hidden('blog_id');
	echo $form->input('name', array('label'=>__d('lil_blogs', 'Name', true).':', 'error'=> __d('lil_blogs', 'Category name is required.', true)));
	echo $form->submit(__d('lil_blogs', 'Create', true));
	echo $form->end();
?>
</div>
