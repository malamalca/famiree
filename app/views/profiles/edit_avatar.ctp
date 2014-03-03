<div class="form" id="EditProfileAvatar">
	<h1><?php 
		echo $this->Sanitize->html($profile['Profile']['d_n']).': '.__('Select Avatar', true);
	?>
	</h1>
	<?php
		foreach ($attachments as $attachment) {
			echo '<div class="index_attachment">';
			// display image with link to view attachment
			echo $this->Html->link(
				$this->Html->image(
					'thumbs/'.$attachment['Attachment']['id'].'.png'
				),
				array(
					'controller' => 'profiles',
					'action' => 'edit_avatar',
					$profile['Profile']['id'],
					$attachment['Attachment']['id']
				),
				array('escape' => false)
			);
			echo '<div class="_title">';
			echo $this->Sanitize->html($attachment['Attachment']['title']);
			echo '</div>';
			echo '</div>';
		}
	?>
</div>