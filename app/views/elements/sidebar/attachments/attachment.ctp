<?php
	if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
?>
<h1><?php __('Operations'); ?></h1>
<ul>
	<li>
		<?php
			echo $this->Html->image('ico_avatar.gif', array('class'=>'icon'));
			echo $this->Html->link(__('Point a Person', true), 
				array(
					'controller'=>'attachments', 
					'action'=>'addnote', 
					$attachment['Attachment']['id']
				), array(
					'id' => 'AddNoteLink'
				)
			);
		?>
	</li>
	<li><?php 
		echo $this->Html->image('ico_profile_edit.gif', array('class'=>'icon'));
		echo $this->Html->link(__('Edit', true), array('controller'=>'attachments', 'action'=>'edit', $attachment['Attachment']['id']));
		echo ' '.__('properties', true);
	?></li>
</ul>
<div>&nbsp;</div>
<?php
	} // level check
?>
<h1><?php __('Image Properties'); ?></h1>
<ul class="label_value">
	<li>
		<span class="label"><?php __('Dimensions'); ?>:</span>
		<span class="value"><?php echo $attachment['Attachment']['width'].' <span class="light">x</span> '.$attachment['Attachment']['height'].' <span class="light">'.__('px', true).'</span>'; ?></span>
	</li>
	<li>
		<span class="label"><?php __('Size'); ?>:</span>
		<span class="value"><?php echo $this->Number->toReadableSize($attachment['Attachment']['filesize']); ?></span>
	</li>
</ul>