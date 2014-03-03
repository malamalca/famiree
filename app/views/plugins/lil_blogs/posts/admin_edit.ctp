<?php
	$this->set('sidebar', '');
?>
<h1><?php __('Edit Post'); ?></h1>
<div class="form" id="FormEditPost">
<div id="FormEditPostMain" class="panel">
	<div class="legend"><?php __('Basic Info'); ?></div>
	<div class="dropdown">
<?php
	echo $this->Form->create('Post');
	echo $this->Form->input('Post.id', array('type' => 'hidden'));
	echo $this->Form->input('Post.blog_id', array('type' => 'hidden'));
	echo $this->Form->input('Post.referer', array('type' => 'hidden'));
	
	echo $this->Form->input('Post.title', array(
		'label' => __('Title', true).':',
		'class' => 'big',
	));
	echo $this->Form->input('Post.body', array('label'=>__('Body', true).':', 'rows'=>4));
	
	echo $this->Form->input('Post.allow_comments', array('label' => __('Allow Comments', true)));
	
	echo $this->Html->script('jquery.textarearesizer.min');
?>
	</div>
</div>
<div class="panel">
	<div class="legend"><?php __('Additional Properties'); ?></div>
	<div class="dropdown">
	<?php
		echo $this->Form->input('Post.slug', array('label' => __('Slug', true).':'));
		echo $this->Form->input('Post.created', array(
			'label' => __('Created', true).':',
			'dateFormat' => Configure::read('dateFormat'),
			'timeFormat' => Configure::read('timeFormat'),
			'separator' => Configure::read('dateSeparator'),
		));
		
		echo $this->Form->input('Post.creator_id', array(
			'type' => 'select',
			'options' => $authors,
			'label' => __('Creator', true).':'
		));
	?>
	</div>
</div>
<?php
	// repeat submit
	echo '<div class="input submit">';
	echo $this->Form->submit(__('Save', true), array('div'=>false));
	
	if ($referer = trim(base64_decode($this->Html->value('Post.referer')))) {
		echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), $referer);
	}
	echo '</div>';
	
	echo $this->Form->end();
	echo $this->Html->script('ui.core');
	echo $this->Html->script('ui.autocomplete');
	echo $this->Html->css('ui.core');
	echo $this->Html->css('ui.theme');
	echo $this->Html->css('ui.autocomplete');
?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		// add resizer to textarea
		$('#PostBody:not(.processed)').TextAreaResizer();
		
		/*$('#PostAuthorTitle').autocomplete({
			url      : '<?php echo $this->Html->url(array('controller'=>'profiles', 'action'=>'autocomplete')); ?>', 
			dataType : "text",
			width    : "500px",
			formatResult: function(row) {
				return row[1];
			},
			formatItem: function(data, i, total) {
				return data[1];
			},
			result: function(data, row) {
				$('#PostCreatorId').val(row[0]);
			}
		});*/
	});
</script>