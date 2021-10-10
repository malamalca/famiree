<?php
	if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
?>
<h1><?= __('Actions') ?></h1>
<ul>
	<li>
		<?php
			echo $this->Html->image('ico_photos.gif', array('class' => 'icon'));
			echo $this->Famiree->link(__('[Upload] attachment'),
				array(
					'controller' => 'Attachments',
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
<h1><?= __('Filters') ?></h1>
<ul>
	<li class="<?php echo ($this->getRequest()->getQuery('filter') == 'all')?'active ':''; ?>at_ix_flt"><?php
		echo $this->Html->link(__('All Attachments'), array(
			'action' => 'index',
			'filter' => 'all'
		));
	?>
	</li>
	<li class="<?php echo ($this->getRequest()->getQuery('filter') != 'all')?'active ':''; ?>at_ix_flt"><?php
		echo $this->Html->link(__('Recent Uploads'), array(
			'action' => 'index'
		));
	?>
	</li>
</ul>
