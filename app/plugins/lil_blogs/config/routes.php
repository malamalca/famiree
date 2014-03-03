<?php
	Router::parseExtensions('rss');
	
	// base route
	Router::connect('/lil_blogs', array('plugin'=>'lil_blogs', 'controller' => 'blogs', 'action'=>'index'));
	
	// login logout
	Router::connect('/lil_blogs/login', array('plugin'=>'lil_blogs', 'controller' => 'authors', 'action'=>'login'));
	Router::connect('/lil_blogs/logout', array('plugin'=>'lil_blogs', 'controller' => 'authors', 'action'=>'logout'));
	
	// make sure blog admin get routed properly
	Router::connect('/admin/lil_blogs/', array('admin'=>true, 'plugin'=>'lil_blogs', 'controller' => 'blogs', 'action'=>'list'));

	// make sure blog comments still get routed properly
	Router::connect('/lil_blogs/comments/add', array('plugin'=>'lil_blogs', 'controller' => 'comments', 'action'=>'add'));
	
	// route blog post id
	Router::connect('/lil_blogs/posts/:postid', array('plugin'=>'lil_blogs', 'controller' => 'posts', 'action'=>'view'), array('blogid'=>'[0-9]+'));

	// route blog short names
	Router::connect('/lil_blogs/:blogname', array('plugin'=>'lil_blogs', 'controller' => 'posts', 'action' => 'index'), array('blogname'=>'[A-Za-z0-9_-]+'));
	
	// route blog post short names
	Router::connect('/lil_blogs/:blogname/:post', array('plugin'=>'lil_blogs', 'controller' => 'posts', 'action' => 'view'), array('blogname'=>'[A-Za-z0-9_-]+', 'post'=>'[A-Za-z0-9_-]+'));
	Router::connect('/lil_blogs/:blogname/:post/#:comment', array('plugin'=>'lil_blogs', 'controller' => 'posts', 'action' => 'view'), array('blogname'=>'[A-Za-z0-9_-]+', 'post'=>'[A-Za-z0-9_-]+', 'comment'=>'[A-Za-z0-9_-]+'));
?>
