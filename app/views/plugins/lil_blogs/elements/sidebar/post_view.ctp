<?php
	foreach ($post['Category'] as $profile) {
		//attachment id
		if (!empty($profile['ta'])) {
			echo '<div id="ProfileHeadshot">';
			echo $this->Html->image($this->Html->url(
				array(
					'plugin'     => null,
					'controller' => 'attachments',
					'action'     => 'display',
					$profile['ta'],
					'medium'
				),
				true), 
				array(
					'id' => 'SidebarAttachmentPreviewImage'
				)
			);
			echo '</div>';
		}
	}
?>