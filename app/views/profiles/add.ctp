<?php
	// this is a CakePHP 1.3 
?>
<div class="form" id="FormAddChild">
<?php
	// form title
	echo '<h1>';
	switch ($mode) {
		case 'add_sibling':
			printf(__('Add %s\'s sister or brother', true), $this->Sanitize->html($profile['Profile']['d_n'])); 
			break;
		case 'add_child':	
			printf(__('Add %s\'s Child', true), $this->Sanitize->html($profile['Profile']['d_n'])); 
			break;
		case 'add_partner':	
			printf(__('Add %s\'s Partner', true), $this->Sanitize->html($profile['Profile']['d_n'])); 
			break;
		case 'add_parent':	
			printf(__('Add %s\'s Parent', true), $this->Sanitize->html($profile['Profile']['d_n'])); 
			break;
	}
	echo '</h1>';
	
	echo $this->Form->create('Profile',
		array(
			'url' => array('action' => $mode),
			'id'  => 'ProfileAddForm'
		)
	);
	echo $this->Form->input('Profile.parent_id', array('type' => 'hidden'));
	echo $this->Form->input('Profile.referer', array('type' => 'hidden'));
	echo $this->Form->input('Unit.kind', array('type' => 'hidden'));
	
	if (empty($marriages)) {
		echo $this->Form->input('Union.id', array('type' => 'hidden'));
	} else {
		echo '<fieldset>';
		echo $this->Form->input(
			'Union.id',
			array(
				'type'    => 'select',
				'options' => $marriages,
				'label'   => __('Select other parent', true) . ':',
				'empty'   => '-- ' . __('create new family', true) . ' --'
			)
		);
		echo '</fieldset>';

	}
?>
	<fieldset>
	<div class="input radio_row">
		<label><?php __('Status'); ?>:</label>
		<div class="row">
		<?php
			echo $this->Form->input('Profile.l', array(
				'type'    =>'radio', 
				'options' => array(
					'1' => __('Living', true),
					'0' => __('Deceased', true)
				),
				'legend'  => false,
				'div'     => false));
		?>
		</div>
	</div>
	</fieldset>
	<fieldset>
	<div class="input text">
		<label><?php __('Name'); ?>:</label>
		<?php
			echo $this->Form->input('Profile.fn', array('type'=>'text', 'label'=>false, 'div'=>false));
			echo $this->Form->input('Profile.mn', array('type'=>'text', 'label'=>false, 'div'=>false));
			echo $this->Form->input('Profile.ln', array('type'=>'text', 'label'=>false, 'div'=>false));
		?>
	</div>
	<?php
		echo $this->Form->input('Profile.mdn', array('type'=>'text', 'label'=>__('Maiden Name', true).':'));
	?>
	<div class="input radio_row">
		<label><?php __('Gender'); ?>:</label>
		<div class="row">
		<?php
			echo $this->Form->input(
				'Profile.g',
				array(
					'type'    => 'radio',
					'options' => array(
						'm' => __('Male', true),
						'f' => __('Female', true)
					),
					'legend'  => false,
					'div'=>false
				)
			);
		?>
		</div>
	</div>
	</fieldset>
	<fieldset>
		<div class="input text">
			<label><?php __('Date of Birth'); ?>:</label>
		<?php
			echo $this->Date->day('Profile.dob_d');
			echo $this->Date->month('Profile.dob_m');
			echo $this->Form->input('Profile.dob_y', array('type'=>'text', 'label'=>false, 'div'=>false));
		?>
		</div>
		<?php
			echo $this->Form->input('Profile.plob', array('type'=>'text', 'label'=>__('Place of Birth', true).':'));
		?>
	</fieldset>
	<fieldset>
	<?php
		echo $this->Form->input('Profile.loc', array('type'=>'text', 'label'=>__('Place of Living', true).':'));
	?>
	</fieldset>
	<fieldset id="AddProfileDeathInfo">
		<div class="input text">
			<label><?php __('Date of Death'); ?>:</label>
		<?php
			echo $this->Date->day('Profile.dod_d');
			echo $this->Date->month('Profile.dod_m');
			echo $this->Form->input('Profile.dod_y', array('type'=>'text', 'label'=>false, 'div'=>false));
		?>
		</div>
	<?php
		echo $this->Form->input('Profile.plod', array('type'=>'text', 'label'=>__('Place of Death', true).':'));
		echo $this->Form->input('Profile.cod', array('type'=>'text', 'label'=>__('Cause of Death', true).':'));
		echo $this->Form->input('Profile.plobu', array('type'=>'text', 'label'=>__('Place of Burial', true).':'));
	?>
	</fieldset>
	<?php
		echo '<div class="input submit">';
		echo $this->Form->submit(__('Save', true), array('div' => false, 'id' => 'ProfileSubmitButton'));
		
		if ($referer = trim(base64_decode($this->Html->value('Profile.referer')))) {
			echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), $referer);
		}
		echo '</div>';
		
		echo $form->end();
	?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		<?php if ($this->Html->value('Profile.l')) { ?>
			// hide death info form living people
			$("#AddProfileDeathInfo").hide();
		<?php } ?>
		
		$("#ProfileL0").click(function(){
			$("#AddProfileDeathInfo").show();
		});
		$("#ProfileL1").click(function(){
			$("#AddProfileDeathInfo").hide();
		});
		$('#ProfileAddForm').submit(function(){
			$('#ProfileSubmitButton').attr('disabled', true);
		});
	});
</script>