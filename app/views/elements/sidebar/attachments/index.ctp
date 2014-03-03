<?php
	if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
?>
<h1><?php __('Actions'); ?></h1>
<ul>
	<li>
		<?php
			echo $this->Html->image('ico_photos.gif', array('class' => 'icon'));
			echo $this->Quicks->link(__('[Upload] attachment', true), 
				array(
					'controller' => 'attachments', 
					'action' => 'add', 
				)
			);
		?>
	</li>
</ul>
<div>&nbsp;</div>
<?php
	} // level check
?>
<h1><?php __('Filters'); ?></h1>
<ul>
	<li class="<?php echo (@$this->params['named']['filter']=='all')?'active ':''; ?>at_ix_flt"><?php
		echo $this->Html->link(__('All Attachments', true), array(
			'action' => 'index',
			'filter' => 'all'
		));
	?>
	</li>
	<li class="<?php echo (empty($this->params['named']['filter']))?'active ':''; ?>at_ix_flt"><?php
		echo $this->Html->link(__('Recent Uploads', true), array(
			'action' => 'index'
		));
	?>
	</li>
</ul>