<?php
	$comment_edit = array(
		'title' => '<div class="head">'.
			'<h1>' . __d('lil_blogs', 'Edit Comment', true) . '</h1>'.
			'</div>',
		'form' => array(
			'pre' => '<div class="form">',
			'post' => '</div>',
			'lines' => array(
				'form_start' => array(
					'class' => $form,
					'method' => 'create',
					'parameters' => array('Comment')
				),
				'id' => array(
					'class' => $form,
					'method' => 'hidden',
					'parameters' => array('id')
				),
				'post_id' => array(
					'class' => $form,
					'method' => 'hidden',
					'parameters' => array('post_id')
				),
				'referer' => array(
					'class' => $form,
					'method' => 'hidden',
					'parameters' => array('referer')
				),
				
				'author' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'author',
						 array('label'=>__d('lil_blogs', 'Author', true) . ':')
					)
				),
				'url' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'url',
						 array('label'=>__d('lil_blogs', 'Url', true) . ':')
					)
				),
				'email' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'email',
						 array('label'=>__d('lil_blogs', 'Email', true) . ':')
					)
				),
				'body' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'body',
						 array('label'=>__d('lil_blogs', 'Body', true) . ':')
					)
				),
				'status' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'field' => 'status',
						'options' => array(
							'label'=>__d('lil_blogs', 'Status', true) . ':',
							'options' => array(
								LILCOMMENT_PENDING => __d('lil_blogs', 'Awaiting Moderation', true),
								LILCOMMENT_APPROVED => __d('lil_blogs', 'Approved', true))
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
	$comment_edit = $this->callPluginHandlers('form_edit_comment', $comment_edit);
	
	// form display begins
	echo $comment_edit['title']; 
	
	echo $comment_edit['form']['pre']; 
	foreach ($comment_edit['form']['lines'] as $name => $line) {
		if (is_array($line) && !empty($line['class'])) {
			$parameters = array();
			if (!empty($line['parameters'])) {
				$parameters = (array)$line['parameters'];
			}
			echo call_user_func_array(array($line['class'], $line['method']), $parameters);
		} else {
			echo $line;
		}
	}
	echo $comment_edit['form']['post']; 
?>