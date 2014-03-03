<h1><?php __d('lil_blogs', 'LilBlogs List'); ?></h1>
<?php foreach($blogs as $blog) { ?>
	<h2><?php echo $sanitize->html($blog['Blog']['name']); ?></h2>
	<p><?php echo $sanitize->html($blog['Blog']['description']); ?></p>
	<p><?php echo $html->link(__d('lil_blogs', 'Read more', true), array('controller'=>'posts', 'action'=>'index', 'blogname'=>$blog['Blog']['short_name'])); ?></p>
<?php } ?>
