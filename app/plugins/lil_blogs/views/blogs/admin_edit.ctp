<div class="head">
	<h1><?php __d('lil_blogs', 'Edit a Blog'); ?></h1>
	<p><?php __d('lil_blogs', 'A blog is a container for posts and is generally maintained by one or a few people.'); ?></p>
</div>
<div class="form">
<?php
	echo $form->create('Blog');
	echo $form->input('id');
	echo $form->input('name', array('label'=>__d('lil_blogs', 'Name', true).':', 'error' => __d('lil_blogs', 'Blog name is required.', true)));
	if (Configure::read('LilBlogs.slug')=='manual') {
		echo $form->input('short_name', array('label'=>__d('lil_blogs', 'Short name', true).':', 'error' => __d('lil_blogs', 'Short name must only use letters, numbers, underscores or hyphens.', true)));
	}
	echo $form->input('description', array('label'=>__d('lil_blogs', 'Description', true).':', 'error' => __d('lil_blogs', 'Blog description is required.', true)));
	
	if (!$author_id_field = Configure::read('LilBlogs.userTable.foreignKey')) {
		$author_id_field = 'author_id';
	}
	echo $form->input('Author', array(
		'label'   => __d('lil_blogs', 'Author', true).':',
		'options' => $authors
	));
	
	echo $form->submit(__d('lil_blogs', 'Save', true));
	echo $form->end();

?>
</div>