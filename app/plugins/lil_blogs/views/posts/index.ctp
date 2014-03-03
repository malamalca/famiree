<?php
	if (!empty($search)) {
		echo '<div class="content_header">' . __d('lil_blogs', 'Search Results', true) . '</div>';
	} else {
		$this->pageTitle = $sanitize->html($blog['Blog']['name']);
	}
?>
<div id="post-index">
<?php foreach($recentposts as $post) { ?>
<div class="post" id="post-<?php echo $post['Post']['id']; ?>">
	<h1>
	<?php
		echo $html->link($post['Post']['title'], array(
			'plugin'     => 'lil_blogs',
			'controller' => 'posts',
			'action'     => 'view',
			'blogname'   => $blog['Blog']['short_name'],
			'post'       => $post['Post']['slug']));
	?>
	</h1>
	<div class="meta">
		<?php

			if (!empty($post['Author'][Configure::read('LilBlogs.authorDisplayField')])) {
				printf(__d('lil_blogs', 'Posted %1$s by %2$s', true),
					$time->timeAgoInWords($post['Post']['created']),
					$post['Author'][Configure::read('LilBlogs.authorDisplayField')]
				);
			} else {
				printf(__d('lil_blogs', 'Posted %s', true),
					$time->timeAgoInWords($post['Post']['created'])
				);
			}
			
			// display list of categories
			if (!empty($post['Category'])) {
				echo ' '.__d('lil_blogs', 'in', true).' ';
				
				echo $text->toList(
					Set::format(
						$post, 
						$html->link('%1$s', array(
							'controller' => 'posts',
							'action'     => 'index',
							'blogname'   => $blog['Blog']['short_name'],
							'category'   => '%2$s'
						)), 
						array('Category.{n}.name', 'Category.{n}.id')
					),
					__d('lil_blogs', 'and', true)
				);
			}
			
			// show no of comments
			echo ' (';
			// this is neccessary because of i18n extraction
			$comment_count = $post['Post']['no_comments'];
			echo $html->link(sprintf(
				__dn('lil_blogs', '1 comment', '%d comments', $comment_count, true), $comment_count),
				array(
					'plugin'     => 'lil_blogs',
					'controller' => 'posts',
					'action'     => 'view',
					'blogname'   => $blog['Blog']['short_name'],
					'post'       => $post['Post']['slug'],
					'comment'    => 'comments'
				));
			echo ')';
			
			// show "edit this post" link
			if ($auth->user('id')) {
				echo ' | '.$html->link(__d('lil_blogs', 'Edit this post', true), array(
					'admin'=>true,
					'action'=>'edit',
					$post['Post']['id']
				));
			}
		?>
	</div>
	<div class="body"><?php
		$body = $post['Post']['body'];
		if ($excerpt_length = Configure::read('LilBlogs.excerptLength')) {
			$body = $text->truncate($body, $excerpt_length, '...', false, true);
		} else if (stripos($body, Configure::read('LilBlogs.excerptDelimiter'))!==false) {
			$body = substr($body, 0, stripos($body, Configure::read('LilBlogs.excerptDelimiter')));
		}
		echo $sanitize->wpautop($body);
	?></div>
	<?php
		if (strlen($body) != strlen($post['Post']['body'])) {
	?>
	<div class="more">
		<?php
			echo $html->link(__d('lil_blogs', 'Read more', true), array(
				'plugin'     => 'lil_blogs',
				'controller' => 'posts',
				'action'     => 'view',
				'blogname'   => $blog['Blog']['short_name'],
				'post'       => $post['Post']['slug'])); ?>
	</div>
	<?php
		}
	?>
</div>
<?php } ?>
<div class="paging">
<?php
	echo $paginator->prev(__d('lil_blogs', 'Newer', true), array('url' => array('blogname'=>$blog['Blog']['short_name'])), '');
	echo $paginator->next(__d('lil_blogs', 'Older', true), array('url' => array('blogname'=>$blog['Blog']['short_name'])), '');
?>
</div>
</div>
