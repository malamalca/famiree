<?php
	$pagination_url = Router::getParam('pass');
	
	$post_index = array(
		'title' =>
			'<div class="head">'.
				'<h1>' . 
				sprintf(
					__d('lil_blogs', 'Posts for "%s"', true),
					$sanitize->html($blog['Blog']['name'])
				).
				'</h1>'.
			'</div>',
		'actions' => array(
			'pre' => '<div>',
			'post' => '</div>',
			'lines' => array(
			)
		),
		'table' => array(
			'pre' => '<div class="index">' . PHP_EOL,
			'post' => '</div>',
			'element' => array(
				'parameters' => array(
					'cellspacing' => 0,
					'cellpadding' => 0,
					'id'          => 'AdminPostsIndex'
				),
			)
		)
	);
	
	$post_index['table']['element']['head'] = array(
		'parameters' => array(),
		'rows' => array(
			0 => array(
				'parameters' => array(),
				'columns' => array(
					'id' => array(
						'parameters' => array(),
						'html' => $paginator->sort(__d('lil_blogs', 'ID', true), 'id', array('url' => $pagination_url)),
					),
					'title' => array(
						'parameters' => array('class' => 'left'),
						'html' => $paginator->sort(__d('lil_blogs', 'Title', true), 'title', array('url' => $pagination_url)),
					),
					'author' => array(
						'parameters' => array('class' => 'left'),
						'html' => $paginator->sort(__d('lil_blogs', 'Author', true), 'author_id', array('url' => $pagination_url)),
					),
					'status' => array(
						'parameters' => array(),
						'html' => $paginator->sort(__d('lil_blogs', 'Status', true), 'status', array('url' => $pagination_url)),
					),
					'date' => array(
						'parameters' => array(),
						'html' => $paginator->sort(__d('lil_blogs', 'Date', true), 'created', array('url' => $pagination_url)),
					),
					'edit' => array(
						'parameters' => array(),
						'html' => '&nbsp;',
					),
					'delete' => array(
						'parameters' => array(),
						'html' => '&nbsp;',
					),
					'comments' => array(
						'parameters' => array(),
						'html' => '&nbsp;',
					),
				)
			)
		)
	);
	
	// table body values
	$post_index['table']['element']['body']['rows'] = array();
	if (empty($data)) {
		$post_index['table']['element']['body']['rows'][0] = array(
			'parameters' => array(),
			'columns' => array(
				0 => array(
					'parameters' => array('colspan' => 8, 'class' => 'light'),
					'html' => __d('lil_blogs', 'No posts that match your criteria.', true)
				)
			),
		);
	} else {
		$i = 0; 
		foreach($data as $item) {
			$post_index['table']['element']['body']['rows'][$i] = array(
				'post' => $item,
				'parameters' => ($i++%2==0) ? array() : array('class' => 'altrow'),
				'columns' => array(
					'id' => array(
						'parameters' => array('class' => 'center'),
						'html' => $item['Post']['id']
					),
					'title' => array(
						'parameters' => ($item['Post']['status'] == 0) ? array('class' => 'light') : array(),
						'html' => (($item['Post']['status'] == 0) ?
							('<div class="big">' . $sanitize->html($item['Post']['title']) . '</div>') :
							('<div>' . $html->link($item['Post']['title'], array(
								'admin'=>false, 
								'controller'=>'posts', 
								'action'=>'view', 
								'blogname'=>$blog['Blog']['short_name'], 
								'post'=>$item['Post']['slug']
							), array('class' => 'big')) . '</div>')).
							$sanitize->html($item['Post']['slug'])
					),
					'author' => array(
						'parameters' => array('class' => 'small'),
						'html' => $sanitize->html($item['Author'][Configure::read('LilBlogs.authorDisplayField')])
					),
					'status' => array(
						'parameters' => array('class' => 'center small'),
						'html' => ($item['Post']['status'] == 0) ? __d('lil_blogs', 'Draft', true) : __d('lil_blogs', 'Live', true)
					),
					'date' => array(
						'parameters' => array('class' => 'nowrap center small' . (($item['Post']['status']==0) ? ' light' : '')),
						'html' => $time->niceShort($item['Post']['created'])
					),
					
					'edit' => array(
						'parameters' => array('class' => 'center'),
						'html' => $html->link(
							$html->image(
								'/lil_blogs/img/edit.gif', 
								array('alt' => __d('lil_blogs', 'Edit', true))
							),
							array(
								'action' => 'admin_edit',
								$item['Post']['id']),
								array('title' => __d('lil_blogs', 'Edit', true)
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
							array(
								'action' => 'admin_delete',
								$item['Post']['id']
							),
							array('title' => __d('lil_blogs', 'Delete', true)),
							null,
							false
						)
					),
					'comments' => array(
						'parameters' => array('class' => 'center'),
						'html' => $html->link($item['Post']['no_comments'],
							array(
								'controller' => 'comments', 
								'action'     => 'index',
								'admin'      => 'true',
								'post_id'    => $item['Post']['id']
							), 
							array(
								'title' => __d('lil_blogs', 'Comments', true), 
								'class' => 'PostsIndexCommentCount'
							),
							null, false)
					),
				)
			);
			

		}
	}
	
	$post_index['table']['element']['foot'] = array(
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
	$post_index = $this->callPluginHandlers('admin_index_posts', $post_index);
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	// form display begins
	echo $post_index['title'] . PHP_EOL;
	
	echo $post_index['actions']['pre'];
	foreach ($post_index['actions']['lines'] as $name => $line) {
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
	echo $post_index['actions']['post'] . PHP_EOL;
	
	echo $post_index['table']['pre'];
	echo '<table';
	foreach ($post_index['table']['element']['parameters'] as $key => $param) {
		echo ' ' . $key . '="' . $param . '"';
	}
	echo '>' . PHP_EOL;
	
	// display thead
	echo '<thead';
	foreach ($post_index['table']['element']['head']['parameters'] as $key => $param) {
		echo ' ' . $key . '="' . $param . '"';
	}
	echo '>' . PHP_EOL;
	
	foreach ($post_index['table']['element']['head']['rows'] as $row) {
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
	foreach ($post_index['table']['element']['body']['rows'] as $row) {
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
	foreach ($post_index['table']['element']['foot']['parameters'] as $key => $param) {
		echo ' ' . $key . '="' . $param . '"';
	}
	echo '>' . PHP_EOL;
	
	foreach ($post_index['table']['element']['foot']['rows'] as $row) {
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
	echo $post_index['table']['post'];
?>