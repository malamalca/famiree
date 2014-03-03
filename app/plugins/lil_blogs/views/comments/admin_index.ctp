<?php
	$pagination_url = Router::getParam('pass');
	if (!empty($this->params['named']['blog_id'])) {
		$pagination_url['blog_id'] = $this->params['named']['blog_id'];
	}
	if (isset($this->params['named']['status'])) {
		$pagination_url['status'] = $this->params['named']['status'];
	}
	
	$comment_index = array(
		'title' =>
			'<div class="head">'.
				'<h1>'.
				(isset($post) ?
					sprintf(__d('lil_blogs', 'Comments for "%s"', true),
					$html->link($post['Post']['title'], array(
						'controller' => 'posts',
						'action'     => 'admin_index',
						$post['Post']['blog_id']
					))) :
					sprintf(__d('lil_blogs', 'All comments on "%s"', true), $blog['Blog']['name'])
				).
				'</h1>'.
			'</div>',
		'actions' => array(
			'pre' => '<div>',
			'post' => '</div>',
			'lines' => array(
				'form_start' => array(
					'class' => $form,
					'method' => 'create',
					'parameters' => array(
						'model' => null,
						'params' => array('url' => array('action' => 'quick')))
				),
				'action' => array(
					'class' => $form,
					'method' => 'input',
					'parameters' => array(
						'name' => 'action',
						'params' => array(
							'type'    => 'select',
							'div'     => false,
							'label'   => false,
							'empty'   => __d('lil_blogs', 'With selected comments:', true),
							'options' => array(
								'approve'   => __d('lil_blogs', 'Approve them', true),
								'unapprove' => __d('lil_blogs', 'Unapprove them', true),
								'delete'    => __d('lil_blogs', 'Delete them', true)
							)
					))
				),
				'submit' => array(
					'class' => $form,
					'method' => 'submit',
					'parameters' => array(
						'caption' => __d('lil_blogs', 'Go', true),
						'params' => array('div' => false)
					)
				),
				// there is no form end tag!!!
				// because comments can be selected in table and have to be included in post data
			)
		),
		'table' => array(
			'pre' => '<div class="index">' . PHP_EOL .
				'<script type="text/javascript">' . PHP_EOL .
					'function DoSelectAll(el) {'.
						'var checkboxes = document.getElementsByName("data[Comment][comments][]");' . PHP_EOL .
						'for (var i=0; i < checkboxes.length; i++) {' . PHP_EOL .
							'checkboxes[i].checked = el.checked;' . PHP_EOL .
						'}' . PHP_EOL .
						'document.getElementById("SelectAll1").checked = el.checked;' . PHP_EOL .
						'document.getElementById("SelectAll2").checked = el.checked;' . PHP_EOL .
					'}' . PHP_EOL .
				'</script>' . PHP_EOL,
			'post' => '</div>' . $form->end(),
			'element' => array(
				'parameters' => array(
					'cellspacing' => 0,
					'cellpadding' => 0,
					'id' => 'AdminCommentsIndex'
				),
			)
		)
	);
	
	$comment_index['table']['element']['head'] = array(
		'parameters' => array(),
		'rows' => array(
			0 => array(
				'parameters' => array(),
				'columns' => array(
					'checkbox' => array(
						'parameters' => array(),
						'html' => '<input type="checkbox" id="SelectAll1" onclick="DoSelectAll(this);" />',
					),
					'id' => array(
						'parameters' => array(),
						'html' => $paginator->sort(__d('lil_blogs', 'ID', true), 'id', array('url' => $pagination_url)),
					),
					'author' => array(
						'parameters' => array('class' => 'left'),
						'html' => __d('lil_blogs', 'Author', true),
					),
					'comment' => array(
						'parameters' => array('class' => 'left'),
						'html' => $paginator->sort(__d('lil_blogs', 'Comment', true), 'created', array('url' => $pagination_url)),
					),
					'post' => array(
						'parameters' => array('class' => 'left'),
						'html' => __d('lil_blogs', 'In response to', true),
					),
					'status' => array(
						'parameters' => array(),
						'html' => $paginator->sort(__d('lil_blogs', 'Status', true), 'status', array('url' => $pagination_url)),
					),
					'edit' => array(
						'parameters' => array(),
						'html' => '&nbsp;',
					),
					'delete' => array(
						'parameters' => array(),
						'html' => '&nbsp;',
					),
				)
			)
		)
	);
	
	// table body values
	$comment_index['table']['element']['body']['rows'] = array();
	if (empty($comments)) {
		$comment_index['table']['element']['body']['rows'][0] = array(
			'parameters' => array(),
			'columns' => array(
				0 => array(
					'parameters' => array('colspan' => 8, 'class' => 'light'),
					'html' => __d('lil_blogs', 'No comments that match your criteria.', true)
				)
			),
		);
	} else {
		$i = 0; 
		foreach($comments as $item) {
			$comment_index['table']['element']['body']['rows'][$i] = array(
				'comment' => $item,
				'parameters' => ($i++%2==0) ? array() : array('class' => 'altrow'),
				'columns' => array(
					'checkbox' => array(
						'parameters' => array('class' => 'center'),
						'html' => $form->checkbox('Comment.bulk', array(
							'name'  => 'data[Comment][comments][]',
							'id'    => 'dataCommentComments' . $i,
							'value' => $item['Comment']['id']
						))
					),
					'id' => array(
						'parameters' => array('class' => 'center small'),
						'html' => $item['Comment']['id']
					),
					'author' => array(
						'parameters' => array('class' => 'td_admin_comment_author'),
						'html' => '<b>' . $sanitize->html($item['Comment']['author']) . '</b>' .
								  '<div class="small light">' . $item['Comment']['email'] . '</div>' .
								  '<div class="small light">' . $item['Comment']['url'] . '</div>' .
								  '<div class="small light">' . $item['Comment']['ip'] . '</div>'
					),
					'comment' => array(
						'parameters' => array('class' => 'td_admin_comment_body'),
						'html' =>	'<div class="small light">' .
										__d('lil_blogs', 'Submitted', true) . ' ' . 
										$time->niceShort($item['Comment']['created']) .
									'</div>' .
									'<div class="admin_comment_body">' . 
									$text->truncate(
										$sanitize->html($item['Comment']['body']), 200, '...', true
									) . 
									'</div>'
					),
					'post' => array(
						'parameters' => array('class' => 'td_admin_comment_post'),
						'html' => 	$html->link($item['Post']['title'], array(
										'controller' => 'posts',
										'action' => 'edit',
										$item['Post']['id'],
										'admin' => true
									)) . 
									'<div class="small light">' . 
										__d('lil_blogs', 'Comments', true) . ': ' . 
										$item['Post']['no_comments'] . 
									'</div>'
					),
					'status' => array(
						'parameters' => array('class' => 'center small'),
						'html' => 	(($item['Comment']['status'] == LILCOMMENT_PENDING) ? __d('lil_blogs', 'Pending', true) : '') . 
									(($item['Comment']['status'] == LILCOMMENT_APPROVED) ? __d('lil_blogs', 'Approved', true) : '')
					),
					
					'edit' => array(
						'parameters' => array('class' => 'center'),
						'html' => $html->link(
							$html->image(
								'/lil_blogs/img/edit.gif', 
								array('alt'=>__d('lil_blogs', 'Edit', true))
							),
							array(
								'action'=>'admin_edit',
								$item['Comment']['id']),
								array('title'=>__d('lil_blogs', 'Edit', true)
							),
							null,
							false
						)
					),
					'delete' => array(
						'parameters' => array('class' => 'center'),
						'html' => $html->link(
							$html->image('/lil_blogs/img/delete.gif',
								array('alt'=>__d('lil_blogs', 'Delete', true))
							),
							array('action'=>'admin_delete', $item['Comment']['id']),
							array('title'=>__d('lil_blogs', 'Delete', true)),
							null,
							false
						)
					),
				)
			);
			

		}
	}
	
	$comment_index['table']['element']['foot'] = array(
		'parameters' => array(),
		'rows' => array(
			0 => array(
				'parameters' => array(),
				'columns' => array(
					'checkbox' => array(
						'parameters' => array('class' => 'center'),
						'html' => '<input type="checkbox" id="SelectAll2" onclick="DoSelectAll(this);" />'
					),
					'paginator' => array(
						'parameters' => array('colspan' => '7', 'class' => 'paging'),
						'html' => $paginator->prev('<< '.__d('lil_blogs', 'previous', true), array('class'=>'prev', 'url' => $pagination_url), null, array('class'=>'prev light')) .
							$paginator->next(__d('lil_blogs', 'next', true).' >>', array('class'=>'next', 'url' => $pagination_url), null, array('class'=>'next light')) .
							'<div class="counter">' . $paginator->counter(array('format' => __d('lil_blogs', 'Page <b>%page%</b> of <b>%pages%</b>, total <b>%count%</b> records.', true))).'</div>'
					),
				)
			)
		)
	);
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	// call plugin handlers
	$comment_index = $this->callPluginHandlers('admin_index_comments', $comment_index);
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	// form display begins
	echo $comment_index['title'] . PHP_EOL;
	
	echo $comment_index['actions']['pre'];
	foreach ($comment_index['actions']['lines'] as $name => $line) {
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
	echo $comment_index['actions']['post'] . PHP_EOL;
	
	echo $comment_index['table']['pre'];
	echo '<table';
	foreach ($comment_index['table']['element']['parameters'] as $key => $param) {
		echo ' ' . $key . '="' . $param . '"';
	}
	echo '>' . PHP_EOL;
	
	// display thead
	echo '<thead';
	foreach ($comment_index['table']['element']['head']['parameters'] as $key => $param) {
		echo ' ' . $key . '="' . $param . '"';
	}
	echo '>' . PHP_EOL;
	
	foreach ($comment_index['table']['element']['head']['rows'] as $row) {
			echo '<tr';
			foreach ($row['parameters'] as $key => $param) {
				echo ' ' . $key . '="' . $param . '"';
			}
			echo '>' . PHP_EOL;
			
			foreach ($row['columns'] as $col) {
				echo '<th';
				foreach ($col['parameters'] as $key => $param) {
					echo ' ' . $key . '="' . $param . '"';
				}
				echo '>' . PHP_EOL;
				
				echo $col['html'];
				echo '</th>' . PHP_EOL;
			}
			
			echo '</tr>' . PHP_EOL;
	}
	echo '</thead>' . PHP_EOL;
	
	// display body
	foreach ($comment_index['table']['element']['body']['rows'] as $row) {
		echo '<tr';
		foreach ($row['parameters'] as $key => $param) {
			echo ' ' . $key . '="' . $param . '"';
		}
		echo '>' . PHP_EOL;
		
		foreach ($row['columns'] as $col) {
			echo '<td';
			foreach ($col['parameters'] as $key => $param) {
				echo ' ' . $key . '="' . $param . '"';
			}
			echo '>' . PHP_EOL;
			echo $col['html'];
			echo '</td>' . PHP_EOL;
		}
		
		echo '</tr>' . PHP_EOL;
	}
	
	// display tfoot
	echo '<tfoot';
	foreach ($comment_index['table']['element']['foot']['parameters'] as $key => $param) {
		echo ' ' . $key . '="' . $param . '"';
	}
	echo '>' . PHP_EOL;
	
	foreach ($comment_index['table']['element']['foot']['rows'] as $row) {
			echo '<tr';
			foreach ($row['parameters'] as $key => $param) {
				echo ' ' . $key . '="' . $param . '"';
			}
			echo '>' . PHP_EOL;
			
			foreach ($row['columns'] as $col) {
				echo '<td';
				foreach ($col['parameters'] as $key => $param) {
					echo ' ' . $key . '="' . $param . '"';
				}
				echo '>' . PHP_EOL;
				echo $col['html'];
				echo '</td>' . PHP_EOL;
			}
			
			echo '</tr>' . PHP_EOL;
	}
	echo '</tfoot>' . PHP_EOL;
	
	echo '</table>' . PHP_EOL;
	echo $comment_index['table']['post'];
?>