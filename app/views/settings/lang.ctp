<div class="form" id="ProfileSettings">
	<div class="tab" id="ProfileSettingsTabDate">
		<h1><?php __('Language'); ?></h1>
		<?php
			echo $this->Form->create('Setting', array('url' => Router::url(null, true)));
			echo $this->Form->input('id', array('type' => 'hidden'));
			echo $this->Form->input('profile_id', array('type' => 'hidden'));
			
			echo $this->Form->input('locale', array(
				'type'    => 'select',
				'label'   => __('Language', true) . ':',
				'options' => $languages,
				'empty'   => '-- ' . __('auto detect', true) . ' --'
			));
			
			// submit button
			echo '<div class="input submit">';
			echo $this->Form->submit(__('Save', true), array('div' => false));
			
			if ($referer = trim(base64_decode($this->Html->value('Settings.referer')))) {
			echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), $referer);
			}
			
			echo '</div>';
			echo $this->Form->end();
		?>
	</div>
</div>