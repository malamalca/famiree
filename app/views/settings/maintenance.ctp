<div class="form" id="ProfileSettings">
	<div class="tab" id="ProfileSettingsMaintenance">
		<h1><?php __('Maintentance operations'); ?></h1>
		<ul>
			<li>
				<h1><?php echo $this->Html->link(__('Rebuild index', true), array('operation' => 'rebuild_index')); ?></h1>
				<?php __('Rebuilds index for Zend Search Lucene.'); ?>
			</li>
		</ul>
	</div>
</div>