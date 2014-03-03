<div class="head">
	<h1><?php __d('lil_blogs', 'LilBlogs Login'); ?></h1>
</div>
<div class="form" id="FormLogin">
	<?php
		echo $form->create('Author', array('url'=>array('plugin'=>'lil_blogs', 'controller'=>'authors', 'action' => 'login')));
		echo $form->input('username', array('label' => __d('lil_blogs', 'Username', true).':'));
		echo $form->input('passwd', array('label' => __d('lil_blogs', 'Password', true).':'));
		echo $form->input('remember_me', array('type'=>'checkbox', 'label'=>__d('lil_blogs', 'Remember me on this computer', true)));
		echo $form->submit(__d('lil_blogs', 'OK', true));
		echo $form->end();
	?>
</div>
