<div class="panel">
	<div class="inner">
	<div class="legend"><?php __('Statistics'); ?></div>
	<div class="center">
	<?php
		echo '<div id="SidebarStatsCntF">';
		echo '<span>' . $count_f . '</span>';
		echo __('No. of Females', true);
		echo '</div>';
		
		echo '<div id="SidebarStatsCntM">';
		echo '<span>' . $count_m . '</span>';
		echo __('No. of Males', true);
		echo '</div>';
		
		echo '<div id="SidebarStatsCntTotal">';
		printf('Total no. of people: %d', $count_total);
		echo '</div>';
	?>
	</div>
	</div>
</div>

<div class="panel">
	<div class="inner">
	<div class="legend"><?php __('Memories'); ?></div>
	<ul>
	<?php
		foreach ($posts as $post) {
			echo '<li>';
			
			$linked_to = array();
			if (isset($post['Category'])) foreach ($post['Category'] as $post_link) {
				$linked_to[] = $post_link['d_n'];
			}
			if (empty($linked_to)) $linked_to[] = __('my past', true);
			
			printf(
				__('About %1$s written by %2$s %3$s', true),
				$this->LilBlogs->permalink('memories', $post, array(
					'caption' => $this->Quicks->toList($linked_to, __('and', true))
				)),
				$this->Html->link($post['Author']['d_n'], array(
					'controller' => 'profiles',
					'action'     => 'view',
					$post['Author']['id']
				)),
				'<span class="light">(' . $this->Date->timeAgoInWords($post['Post']['created'], 
					array('format' => Configure::read('outputDateFormat'))
				).')</span>'
			);
			echo '</li>';
		}
	?>
		<li class="right"><?php
			echo $this->Html->link(__('All Posts', true), array(
					'plugin'     => 'lil_blogs',
					'controller' => 'posts',
					'action'     => 'index',
					'blogname'   => 'memories'
				));
		?></li>
	</ul>
	</div>
</div>

<div class="panel">
	<div class="inner">
	<div class="legend"><?php __('Log'); ?></div>
	<ul>
		<?php
			foreach ($recent_changes as $change) {
				echo '<li>';
				switch ($change['Log']['class']) {
					case 'Profile':
						if (in_array($change['Log']['action'], array('add', 'edit')))  {
							if ($change['Log']['foreign_id']==$Auth['Profile']['id'] && 
								$change['User']['id']==$Auth['Profile']['id']) 
							{
								__('I\'ve edited my own profile.');
						 	} else if ($change['Log']['foreign_id']==$Auth['Profile']['id']) {
						 		printf(__('%s has edited my profile.', true),
						 			$this->Html->link($change['User']['d_n'], array(
										'controller' => 'profiles',
										'action'     => 'view',
										$change['User']['id']
									))
								);
							} else if ($change['Log']['foreign_id']==$change['User']['id']) {
								printf(__('%s has edited his own profile.', true),
									$this->Html->link($change['User']['d_n'], array(
										'controller' => 'profiles',
										'action'     => 'view',
										$change['User']['id']
									))
								);
							} else {
								if ($change['Log']['action']=='add')  {
									$message = __('%1$s has been added by %2$s', true);
								} else {
									$message = __('%1$s has been edited by %2$s', true);
								}
								printf($message, 
									$this->Html->link($change['Profile']['d_n'],
										array(
											'controller' => 'profiles',
											'action'     => 'view',
											$change['Profile']['id']
										)
									), 
									$this->Html->link(
										// is it me or someone else?
										($change['User']['id'] == $Auth['Profile']['id']) ?
										__('Me', true) : $change['User']['d_n'],
										array(
											'controller' => 'profiles',
											'action'     => 'view',
											$change['User']['id']
										)
									)
								);
							}
						} else if ($change['Log']['action'] == 'delete') {
							printf(
								__('Profile "%1$s" has been deleted by %2$s', true),
								$change['Log']['title'],
								$this->Html->link(
									// is it me or someone else?
									($change['User']['id'] == $Auth['Profile']['id']) ?
										__('Me', true) : $change['User']['d_n'],
									array(
										'controller' => 'profiles',
										'action'     => 'view',
										$change['User']['id']
									)
								)
							);
						}
						break;
					case 'Post':
						if ($change['Log']['action']=='delete')  {
							printf(__('Post "%1$s" has been deleted by %2$s.', true), 
								$change['Log']['title'],
								$this->Html->link(($change['User']['id']==$Auth['Profile']['id'])?
									__('Me', true):$change['User']['d_n'], array(
									'controller' => 'profiles',
									'action'     => 'view',
									                $change['User']['id']
								))
							);
						} else {
							if ($change['Log']['action']=='add')  {
								$message = __('Post "%1$s" has been added by %2$s', true);
							} else {
								$message = __('Post "%1$s" has been edited by %2$s', true);
							}
							printf($message, 
								((!empty($change['Post']))?
								$this->Html->link($change['Post']['title'], array(
									'controller' => 'posts',
									'action'     => 'view',
								    $change['Post']['id']
								))
								:
								$change['Log']['title']
								),
								$this->Html->link(($change['User']['id']==$Auth['Profile']['id'])?
									__('Me', true):$change['User']['d_n'], array(
									'controller' => 'profiles',
									'action'     => 'view',
									$change['User']['id']
								))
							);
						}
						break;
					case 'Attachment':
						if ($change['Log']['action']=='add')  {
							$message = __('Attachment "%1$s" has been added by %2$s', true);
						} else {
							$message = __('Attachment "%1$s" has been edited by %2$s', true);
						}
						printf($message,
							(empty($change['Attachment']))?__('Unknown', true):
							$this->Html->link($change['Attachment']['title'], array(
								'controller' => 'attachments',
								'action'     => 'view',
								$change['Attachment']['id']
							)),
							(empty($change['User']['id'])) ?
							__('Unknown', true) :
							$this->Html->link(($change['User']['id'] == $Auth['Profile']['id'])?
								__('Me', true) : $change['User']['d_n'],
								array(
									'controller' => 'profiles',
									'action'     => 'view',
									$change['User']['id']
								)
							)
						);
						break;
					case 'Imgnote':
						if ($change['Log']['action'] == 'add' && !empty($change['Attachment'])) {
							printf(__('A note has been added to %1$s by %2$s.', true), 
								$this->Html->link(__('image', true), array(
									'controller' => 'attachments',
									'action'     => 'view',
									$change['Imgnote']['attachment_id']
								)),
								$this->Html->link(($change['User']['id'] == $Auth['Profile']['id']) ?
									__('Me', true) : $change['User']['d_n'],
									array(
										'controller' => 'profiles',
										'action'     => 'view',
										$change['User']['id']
									)
								)
							);
						}
						break;
				}
				echo ' <span class="light">';
				echo '('.$this->Date->timeAgoInWords($change['Log']['created'], array(
					'time'   => false,
					'format' => Configure::read('outputDateFormat')
				)).')';
				echo '</span>';
				echo '</li>';
			}
		?>
	</ul>
	</div>
</div>