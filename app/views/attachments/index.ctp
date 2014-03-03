<h1><?php __('Attachments'); ?></h1>
<div class="index" id="IndexAttachment">
<?php
	foreach ($attachments as $attachment) {
		echo '<div class="index_attachment">';
		// display image with link to view attachment
		echo $this->Html->link($this->Html->image(
			'thumbs/'.$attachment['Attachment']['id'].'.png'), 
			array(
				'controller' => 'attachments',
				'action' => 'view',
				$attachment['Attachment']['id']
			),
			array(
			    'escape' => false
			));
		echo '<div class="_title">';
		echo $this->Sanitize->html($attachment['Attachment']['title']);
		echo '&nbsp;</div>';
		echo '</div>'.PHP_EOL;
	}
?>
</div>