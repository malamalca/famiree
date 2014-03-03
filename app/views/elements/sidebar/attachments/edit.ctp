<?php
	if (isset($attachment)) {
?>
<div id="SidebarAttachmentPreview">
<?php
	echo $this->Html->image('thumbs/'.$attachment['Attachment']['id'].'.png', array(
		'id' => 'SidebarAttachmentPreviewImage'
	));
?>
</div>
<?php
	}
?>