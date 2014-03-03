<?php
	if (!$author_id_field = Configure::read('LilBlogs.userTable.foreignKey')) {
		$author_id_field = 'author_id';
	}
	$post_edit = array(
		'title' => 	'<div class="head">'.
			'<h1>' . __d('lil_blogs', 'Edit Post', true) . '</h1>'.
			'</div>',
		'form' => array(
			'pre' => '<div class="form">',
			'post' => '</div>',
			'lines' => array(
				'form_start' => array(
					'class' => $form,
					'method' => 'create',
					'parameters' => array('Post')
				),
				'id' => array(
					'class' => $form,
					'method' => 'hidden',
					'parameters' => array('id')
				),
				'blog_id' => array(
					'class' => $form,
					'method' => 'hidden',
					'parameters' => array('blog_id')
				),
				'referer' => array(
					'class' => $form,
					'method' => 'hidden',
					'parameters' => array('referer')
				),
				'title' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'title',
						 array(
							'label' => __d('lil_blogs', 'Title', true).':',
							'error' => __d('lil_blogs', 'Post title is required.', true),
							'class' => 'big'
						)
					)
				),
				'slug' => (Configure::read('LilBlogs.slug')=='manual') ? null : array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'slug',
						array(
							'label' => __d('lil_blogs', 'Slug', true).':',
							'error' => __d('lil_blogs', 'Post slug is required and must only use letters, numbers, underscores or hyphens.', true)
						)
					)
				),
				'body' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'body',
						 array(
							'label' => __d('lil_blogs', 'Body', true).':',
							'rows'  => 12,
							'error' => __d('lil_blogs', 'Body of the post is required.', true)
						)
					)
				),
				'allow_comments' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'allow_comments',
						 array(
							'label' => __d('lil_blogs', 'Allow Comments', true)
						)
					)
				),
				'categories' => (Configure::read('LilBlogs.noCategories')) ? null : array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'Category.Category',
						 array(
							'label' => __d('lil_blogs', 'Category', true).':',
							'options'=>$categories,
							'empty'=>'-- '.__d('lil_blogs', 'none', true).' --'
						)
					)
				),
				'author_id' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						$author_id_field,
						 array(
							'label'   => __d('lil_blogs', 'Author', true).':',
							'options' => $authors
						)
					)
				),
				'status' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'status',
						 array(
							'label'=>__d('lil_blogs', 'Status', true).':',
							'options'=>array(
								'0'=>__d('lil_blogs', 'Draft', true),
								'2'=>__d('lil_blogs', 'Live', true)
							)
						)
					)
				),
				'submit' => array(
					'class' => $form,
					'method' => 'submit',
					'parameters' => array(__d('lil_blogs', 'Save', true))
				),
				'form_end' => array(
					'class' => $form,
					'method' => 'end',
					'parameters' => array()
				),
			)
		)
	);
	
	// call plugin handlers
	$post_edit = $this->callPluginHandlers('form_edit_post', $post_edit);
	
	// form display begins
	echo $post_edit['title']; 
	
	echo $post_edit['form']['pre']; 
	foreach ($post_edit['form']['lines'] as $name => $line) {
		if (!empty($line['class'])) {
			$parameters = array();
			if (!empty($line['parameters'])) {
				$parameters = (array)$line['parameters'];
			}
			echo call_user_func_array(array($line['class'], $line['method']), $parameters);
		}
	}
	echo $post_edit['form']['post'];
?>