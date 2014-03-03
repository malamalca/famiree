<?php
	// converted to CakePHP 1.3
	
	$this->set('sidebar', '');
?>
<div id="DashBoardPosts">
<?php
	foreach ($recentposts as $post) {
		echo '<div class="dashboard_post">';
		echo '<div class="_header">';
		
		// admin actions - DELETE, EDIT
		if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
			echo '<div class="_actions">';
			echo $html->link(__('edit', true), array(
				'admin'      => true,
				'plugin'     => 'lil_blogs',
				'controller' => 'posts', 
				'action'     => 'edit', 
				$post['Post']['id']
			));
			echo ' '.__('or', true).' ';
			echo $this->Html->link(__('delete', true),
				array(
					'admin'      => true,
					'plugin'     => 'lil_blogs',
					'controller' => 'posts',
					'action'     => 'delete',
					$post['Post']['id']
				), array(
					'class'=>'ajax_del_memory', 
					'id'=>'ProfileMemory'.$post['Post']['id']
				), __('Are your sure you want to delete this post?', true));
			echo '</div>';
		}
		echo '<h1>'.$this->Html->link($post['Post']['title'], array(
			'plugin'     => 'lil_blogs',
			'controller' => 'posts',
			'action'     => 'view',
			'blogname'   => 'memories',
			'post'       => $post['Post']['slug']
		)).'</h1>';
		
		// show date of publish and publisher
		printf(__('Published %1$s by %2$s.', true), $this->Date->timeAgoInWords($post['Post']['created'], 
			array('format' => Configure::read('outputDateFormat').' %H:%M')),
			$this->Html->link($post['Author']['d_n'], array(
				'admin'      => false,
				'plugin'     => null,
				'controller' => 'profiles',
				'action'     => 'view',
				$post['Author']['id']
			))
		);
		
		// show profiles to which this post is linked
		$linked_to = array();
		if (isset($post['Category'])) foreach ($post['Category'] as $post_link) {
			if (empty($post_link['d_n'])) {
				$linked_to[] = __('Unknown', true);
			} else {
				$linked_to[] = $this->Html->link($post_link['d_n'], array(
					'admin'      => false,
					'plugin'     => null,
					'controller' => 'profiles',
					'action'     => 'view',
					$post_link['id']
				));
			}
		}
		if (!empty($linked_to)) {
			echo ' '.__('Linked to', true).' '.$this->Text->toList($linked_to, __('and', true)).'.';
		}
		
		// show no of comments
		echo ' (';
		// this is neccessary because of i18n extraction
		$comment_count = $post['Post']['no_comments'];
		echo $this->Html->link(
			sprintf(
				__n('1 comment', '%d comments', $comment_count, true),
				$comment_count
			),
			array(
				'plugin'     => 'lil_blogs',
				'controller' => 'posts',
				'action'     => 'view',
				'blogname'   => $blog['Blog']['short_name'],
				'post'       => $post['Post']['slug'],
				'comment'    => 'comments'
			));
		echo ')';
		
		echo '</div>';
			echo '<div class="_body">';
			echo $this->Sanitize->wpautop($body = $this->Quicks->excerpt($post['Post']['body']));
			echo '</div>';
			
			if ($body != $post['Post']['body']) {
				echo '<div class="_readmore">';
				echo $this->Html->link(__('Read more...', true), array(
					'admin'      => false,
					'plugin'     => 'lil_blogs',
					'controller' => 'posts',
					'action'     => 'view',
					'blogname'   => 'memories',
					'post'       => $post['Post']['slug']
				));
				echo '</div>';
			}
			
		echo '</div>';
	}
?>
<div class="paging">
<?php
	echo $this->Paginator->prev(__('Newer', true), array('url' => array('blogname'=>'memories')), '');
	echo $this->Paginator->next(__('Older', true), array('url' => array('blogname'=>'memories')), '');
?>
</div>
</div>