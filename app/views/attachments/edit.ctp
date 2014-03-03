<h1>
<?php 
	__('Attachment'); 
	echo ': ';
	if (isset($attachment)) {
		echo $this->Sanitize->html($attachment['Attachment']['title']);
	} else {
		__('Add');
	}
?>
</h1>
<div class="form" id="FormAttachment">
<div class="panel">
	<div class="legend"><?php __('Basic Info'); ?></div>
	<div class="dropdown">
	<?php
		echo $this->Form->create('Attachment', array('type' => 'file', 'id' => 'AttachmentForm'));
		echo $this->Form->input('Attachment.id', array('type'=>'hidden'));
		echo $this->Form->input('Attachment.foreign_id', array('type'=>'hidden'));
		echo $this->Form->input('Attachment.class', array('type'=>'hidden'));
		echo $this->Form->input('Attachment.referer', array('type'=>'hidden'));
		
		echo $this->Form->input('Attachment.title', array(
			'label'=>__('Title', true).':', 
			'class' => 'big'
		));
		echo $this->Form->input('Attachment.filename', array(
			'type' => 'file',
			'label' => __('Filename', true).':'
		));
		echo $this->Form->input('Attachment.description', array(
			'label'=>__('Description', true).':', 
			'rows'=>4
		));
	?>
	</div>
</div>
<div class="panel">
	<div class="legend"><?php __('Additional Properties'); ?></div>
	<div class="dropdown">
	<?php
		echo $this->Form->input('Attachment.created', array(
			'label' => __('Created', true) . ':',
			'dateFormat' => Configure::read('dateFormat'),
			'timeFormat' => Configure::read('timeFormat'),
			'separator' => Configure::read('dateSeparator'),
		));
		echo $this->Form->input('Attachment.creator_id', array('type' => 'hidden'));
	?>
	</div>
</div>
	<?php
		echo '<div class="input submit">';
		echo $this->Form->submit(__('Save', true), array(
			'div' => false,
			'id' => 'AttachmentSubmitButton'
		));
		
		if ($referer = trim(base64_decode($this->Html->value('Attachment.referer')))) {
		echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), $referer);
		}
		
		echo '</div>';
		echo $this->Form->end();
		
		echo $this->Html->script('jquery.textarearesizer.min');
	?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		// add resizer to textarea
		$('#AttachmentDescription:not(.processed)').TextAreaResizer();
		
		$('#AttachmentForm').submit(function(){
			$('#AttachmentSubmitButton').attr('disabled', true);
		});
		$('#AttachmentFilename').change(function(){
			if ($('#AttachmentTitle').val()=='') {
				var fileName = $('#AttachmentFilename').val();
				var extractStart = fileName.lastIndexOf('\\')+1;
				var extractStart2 = fileName.lastIndexOf('/')+1;
				if (extractStart2 > extractStart) extractStart = extractStart2;
						
				fileName = fileName.substring(extractStart, fileName.length);
				
				var extractEnd = fileName.lastIndexOf('.');
				if (extractEnd>=0) fileName = fileName.substring(0, extractEnd);
				
				$('#AttachmentTitle').val(fileName);
			}
		});
	});
</script>