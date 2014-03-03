<div id="PostView">
	<div class="_header">
	<?php
		// admin actions - DELETE, EDIT
		if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
			echo '<div class="_actions">';
			echo $html->link(__('edit', true), array(
				'controller'=>'posts', 
				'action'=>'edit', 
				$post['Post']['id']
			));
			echo ' '.__('or', true).' ';
			echo $html->link(__('delete', true),
				array(
					'controller'=>'posts', 
					'action'=>'delete', 
					$post['Post']['id']
				), array(
					'class'=>'ajax_del_memory', 
					'id'=>'ProfileMemory'.$post['Post']['id']
				), __('Are your sure you want to delete this post?', true));
			echo '</div>';
		}
		echo '<h1>';
		echo $this->pageTitle = $sanitize->html($post['Post']['title']);
		echo '</h1>';
		
		// show date of publish and publisher
		printf(__('Published %1$s by %2$s.', true), $date->timeAgoInWords($post['Post']['created'],
			array('format' => Configure::read('outputDateFormat').' %H:%M')),
			$html->link($post['Creator']['d_n'], array(
			'controller' => 'profiles',
			'action'     => 'view',
			$post['Creator']['id']))
		);
		
		// show profiles to which this post is linked
		$linked_to = array();
		foreach ($post['PostsLink'] as $post_link) {
			$linked_to[] = $html->link($post_link['Profile']['d_n'], array(
				'controller' => 'profiles',
				'action'     => 'view',
				$post_link['Profile']['id']
			));
		}
		if (!empty($linked_to)) {
			echo ' '.__('Linked to', true).' '.$text->toList($linked_to, __('and', true)).'.';
		}
	?>
	</div>
	<div class="_body">
	<?php
		echo nl2br($sanitize->html($post['Post']['body']));
	?>
	</div>
</div>