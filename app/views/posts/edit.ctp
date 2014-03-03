<h1><?php __('Edit Post'); ?></h1>
<div class="form" id="FormEditPost">
<div id="FormEditPostMain" class="panel">
	<div class="legend"><?php __('Basic Info'); ?></div>
	<div class="dropdown">
<?php
	echo $form->create('Post');
	echo $form->input('Post.id', array('type' => 'hidden'));
	echo $form->input('Post.profile_id', array('type' => 'hidden'));
	echo $form->input('Post.referer', array('type' => 'hidden'));
	
	echo $form->input('Post.title', array(
		'label' => __('Title', true).':',
		'class' => 'big',
	));
	echo $form->input('Post.body', array('label'=>__('Body', true).':', 'rows'=>4));
	
	echo $form->input('Post.allow_comments', array('label' => __('Allow Comments', true)));
	
	echo $javascript->link('jquery.textarearesizer.min');
?>
	</div>
</div>
<div class="panel">
	<div class="legend"><?php __('Additional Properties'); ?></div>
	<div class="dropdown">
	<?php
		echo $form->input('Post.slug', array('label' => __('Slug', true).':'));
		echo $form->input('Post.created', array('label' => __('Created', true).':'));
		echo $form->input('Post.creator_id', array('type' => 'hidden'));
		echo $form->input('Post.author', array('label' => __('Author', true).':'));
	?>
	</div>
</div>
<?php
	// repeat submit
	echo '<div class="input submit">';
	echo $form->submit(__('Save', true), array('div'=>false));
	
	if ($referer = trim(base64_decode($html->value('Post.referer')))) {
		echo ' '.__('or', true).' '.$html->link(__('Cancel', true), $referer);
	}
	echo '</div>';
	
	echo $form->end();
	echo $javascript->link('ui.core');
	echo $javascript->link('ui.autocomplete');
	echo $html->css('ui.core');
	echo $html->css('ui.theme');
	echo $html->css('ui.autocomplete');
?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		// add resizer to textarea
		$('#PostBody:not(.processed)').TextAreaResizer();
		
		$('#PostAuthor').autocomplete({
			url:'<?php echo $html->url(array('controller'=>'profiles', 'action'=>'autocomplete')); ?>', 
			dataType:"text",
			width:"500px",
			formatResult: function(row) {
				return row[1];
			},
			formatItem: function(data, i, total) {
				return data[1];
			},
			result: function(data, row) {
				$('#PostCreatorId').val(row[0]);
			}
		});
	});
</script>