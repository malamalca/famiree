<?php
	// this is default blog short_name
	Configure::write('LilBlogs.defaultBlog', null);
	
	Configure::write('LilBlogs.useAdminLayout', true);

	Configure::write('LilBlogs.rssItems', 20);
	Configure::write('LilBlogs.mainPageItems', 5);
	
	Configure::write('LilBlogs.excerptLength', null);
	Configure::write('LilBlogs.excerptDelimiter', '<!-- MORE -->');
	
	Configure::write('LilBlogs.tablePrefix', '');
	Configure::write('LilBlogs.userTable', array(
		'className'  => 'LilBlogs.Author',
		'foreignKey' => 'author_id'
	));
	
	Configure::write('LilBlogs.categoryTable', array(
		'className'             => 'Category',
		'joinTable'             => 'categories_posts',
		'foreignKey'            => 'post_id',
		'associationForeignKey' => 'category_id',
		'conditions'            => array(),
		'with'                  => 'LilBlogs.CategoriesPost',
		'withClassName'         => 'CategoriesPost',
	));
	
	Configure::write('LilBlogs.authorAdminField', 'admin');
	Configure::write('LilBlogs.authorDisplayField', 'name');
	Configure::write('LilBlogs.authorsBlogTable', 'authors_blogs');
	
	Configure::write('LilBlogs.allowAuthorsAnything', false);
	
	Configure::write('LilBlogs.slug', 'auto'); // auto, manual
	
	// ability to disable blogs and categories tables
	Configure::write('LilBlogs.noCategories', false);
	Configure::write('LilBlogs.noBlogs', false);
	
	Configure::write('LilBlogs.plugins', array());
?>