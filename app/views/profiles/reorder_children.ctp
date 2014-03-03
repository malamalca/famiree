<?php
	// converted to CakePhp 1.3
?>
<h1><?php __('Reorder Children'); ?></h1>
<?php
	echo $this->Html->script('jquery-ui-personalized-1.6rc6.min');
?>
<style type="text/css">
	.sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; margin-bottom: 20px; }
	.sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
	</style>
	<script type="text/javascript">
	$(function() {
		$("#sortable").sortable({ cursor: 'crosshair' });
		
	});
	</script>

<div class="demo">
	<?php
		echo $this->Form->create('Unit', array('url'=>array('controller'=>'profiles', 'action'=>'reorder_children', $parent_id)));
		echo $this->Form->input('Unit.referer', array('type'=>'hidden'));
		
		$i = 0;
		foreach ($profile['marriages'] as $marriage) {
			
	?>
	<ul class="sortable" id="sortable<?php echo $i; ?>">
		<?php
			$j = 0;
			foreach ($marriage['children'] as $child) {	
		?>
		<li id="li<?php echo $child['Unit']['id']; ?>"><?php 
			echo $this->Sanitize->html($child['Profile']['d_n']);
			echo $this->Form->input($j.'.Unit.sort_order', array('type'=>'hidden', 'value'=>$j, 'id'=>'so_li'.$child['Unit']['id']));
			echo $this->Form->input($j.'.Unit.id', array('type'=>'hidden', 'value'=>$child['Unit']['id']));
		?></li>
		<?php
				$j++;
			}
		?>
	</ul>
	<script type="text/javascript">
	$(function() {
		$("#sortable<?php echo $i; ?>").sortable({ cursor: 'crosshair', update: function(event, ui) {
			var els = $(this).sortable('toArray');
			var i = 0;
			$.each(els, function(){
				$("#so_"+this).val(i);
				i++;
			});
		}});
		
	});
	</script>
	<?php
			$i++;
		}
		
		echo '<div class="input submit">';
		echo $this->Form->submit(__('Save', true), array('div'=>false));
		
		if ($referer = trim(base64_decode($this->Html->value('Unit.referer')))) {
			echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), $referer);
		}
		echo '</div>';
		
		echo $this->Form->end();
	?>
</div>