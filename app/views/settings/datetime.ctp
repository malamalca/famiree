<div class="form" id="ProfileSettings">
	<div class="tab" id="ProfileSettingsTabDate">
		<h1><?php __('Date and Time Format'); ?></h1>
		<?php
			echo $this->Form->create('Setting', array('url' => Router::url(null, true)));
			echo $this->Form->input('id', array('type' => 'hidden'));
			echo $this->Form->input('profile_id', array('type' => 'hidden'));
			
			echo $this->Form->input('date_order', array(
				'type'    => 'select',
				'label'   => __('Date Input Order', true) . ':',
				'options' => array(
					'YMD' => __('YMD - year, month, day', true),
					'MDY' => __('MDY - month, day, year', true),
					'DMY' => __('DMY - day, month, year', true)
				)
			));
			
			echo $this->Form->input('date_separator', array(
				'type'  => 'text',
				'label' => __('Date Separator', true) . ':',
				'size'  => 1,
				'maxlength' => 1
			));
			
			echo $this->Form->input('date_24hr', array(
				'type'  => 'checkbox',
				'label' => __('24 Hour Time Format', true),
			));
			
			echo $this->Form->input('datef_common', array(
				'type'  => 'text',
				'label' => __('Date Format', true) . ':',
			));
			
			echo $this->Form->input('datef_noyear', array(
				'type'  => 'text',
				'label' => __('No Year Format', true) . ':',
			));
			
			echo $this->Form->input('datef_short', array(
				'type'  => 'text',
				'label' => __('Short Date Format', true) . ':',
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