<?php
	$this->set('sidebar', 'post_view');
?>
<div id="PostView">
	<div class="_header">
	<?php
		// admin actions - DELETE, EDIT
		if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
			echo '<div class="_actions">';
			echo $this->Html->link(__('edit', true), array(
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
		echo '<h1>';
		echo $pageTitle = $this->Sanitize->html($post['Post']['title']);
		$this->set('title_for_layout', $pageTitle);
		echo '</h1>';
		
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
			echo ' '.__('Linked to', true).' '.$this->Quicks->toList($linked_to, __('and', true)).'.';
		}
	?>
	</div>
	<div class="_body">
	<?php
		echo $this->Sanitize->wpautop($post['Post']['body']);
	?>
	</div>
</div>