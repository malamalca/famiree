<?php
	// this is a CakePHP 1.3 
?>
<div class="form" id="EditProfile">
<?php
		echo $this->Form->create('Profile', array('id' => 'ProfileEditForm'));
		echo $this->Form->input('Profile.id');
		echo $this->Form->input('Profile.referer', array('type'=>'hidden'));
?>
	<div class="tab" id="EditProfileTabBasics">
	<h1><?php 
		echo $this->Sanitize->html($d_n).': '.__('Basics', true);
	?>
	</h1>
	<fieldset>
	<div class="input radio_row">
		<label><?php __('Status'); ?>:</label>
		<div class="row">
		<?php
			echo $this->Form->input('Profile.l', array('type'=>'radio', 
				'options'=>array('1'=>__('Living', true), '0'=>__('Deceased', true)),
				'legend'=>false, 'div'=>false));
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
		echo $form->input('Profile.mdn', array('type'=>'text', 'label'=>__('Maiden Name', true).':'));
	?>
	<div class="input radio_row">
		<label><?php __('Gender'); ?>:</label>
		<div class="row">
		<?php
			echo $this->Form->input('Profile.g', array('type'=>'radio', 
				'options'=>array('m'=>__('Male', true), 'f'=>__('Female', true)), 
				'legend'=>false, 'div'=>false));
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
	<fieldset id="EditProfileDeathInfo">
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
	</div>
	<div class="tab" id="EditProfileTabPersonal">
	<h1><?php 
		echo $this->Sanitize->html($d_n).': '.__('Personal info', true);
	?>
	</h1>
	<fieldset>
	<?php
		echo $this->Form->input('Profile.h_c', array('type'=>'select', 'label'=>__('Hair Color', true).':',
			'options'=>array(
				1=>__('Auburn', true),
				2=>__('Black', true),
				3=>__('Blonde', true),
				4=>__('Brown', true),
				5=>__('Gray', true),
				6=>__('Red', true),
				
				0=>__('None', true),
				-1=>__('Other', true),
			), 'empty'=>''));
		echo $this->Form->input('Profile.e_c', array('type'=>'select', 'label'=>__('Eye Color', true).':',
			'options'=>array(
				1=>__('Amber', true),
				2=>__('Blue', true),
				3=>__('Brown', true),
				4=>__('Grey', true),
				5=>__('Green', true),
				6=>__('Hazel', true),
				
				-1=>__('Other', true),
			), 'empty'=>''));
		echo $this->Form->input('Profile.n_n', array('type'=>'text', 'label'=>__('Nick Names', true).':'));
	?>
	</fieldset>
	</div>
	<div class="tab" id="EditProfileTabInterests">
	<h1><?php 
		echo $this->Sanitize->html($d_n).': '.__('Interests', true);
	?>
	</h1>
	<fieldset>
	<?php
		echo $this->Form->input('Profile.in_i', array('type'=>'text', 'label'=>__('Interests', true).':'));
		echo $this->Form->input('Profile.in_a', array('type'=>'text', 'label'=>__('Activities', true).':'));
		echo $this->Form->input('Profile.in_p', array('type'=>'text', 'label'=>__('People/Heroes', true).':'));
		echo $this->Form->input('Profile.in_c', array('type'=>'text', 'label'=>__('Cuisines', true).':'));
		echo $this->Form->input('Profile.in_q', array('type'=>'text', 'label'=>__('Quotes', true).':'));
		echo $this->Form->input('Profile.in_m', array('type'=>'text', 'label'=>__('Movies', true).':'));
		echo $this->Form->input('Profile.in_tv', array('type'=>'text', 'label'=>__('TV Shows', true).':'));
		echo $this->Form->input('Profile.in_mu', array('type'=>'text', 'label'=>__('Music', true).':'));
		echo $this->Form->input('Profile.in_b', array('type'=>'text', 'label'=>__('Books', true).':'));
		echo $this->Form->input('Profile.in_s', array('type'=>'text', 'label'=>__('Sports', true).':'));
	?>
	</fieldset>
	</div>
	
	<div class="tab" id="EditProfileTabRelationships">
	<h1><?php 
		echo $this->Sanitize->html($d_n).': '.__('Relationships', true);
	?>
	</h1>
	<fieldset>
	<?php
		if ($this->Html->value('Profile.g')=='m') {
			$marriage_type = array(
				't'=>__('Wife', true),
				'f'=>__('Fiancee', true),
				'p'=>__('Partner', true),
				'd'=>__('Ex-wife (deceased)', true),
				'e'=>__('Ex-wife', true),
			);
			$marriage_string = __('%s is his', true);
		} else {
			$marriage_type = array(
				't'=>__('Husband', true),
				'f'=>__('Fiancee', true),
				'p'=>__('Partner', true),
				'd'=>__('Ex-husband (deceased)', true),
				'e'=>__('Ex-husband', true),
			);
			$marriage_string = __('%s is her', true);
		}
		
		if (@$Auth['User']['id']) {
			$marriage_string = __('%s is my', true);
		}
		
		$i = 0;
		foreach ($this->Html->value('Union') as $marriage) {
			if ($i>0) echo '<hr />';
			
			if ($spouse = array_pop(Set::extract('/Unit[union_id='.$this->Html->value('Union.'.$i.'.id').']/../Profile', $spouses))) {
			
				$spouse_string = '';
				if (!empty($spouse['Profile']['mdn']) && $spouse['Profile']['mdn'] != $spouse['Profile']['ln']) {
					$spouse_string = sprintf(' <span class="small">('.__('born %s', true).')</span>', $spouse['Profile']['mdn']);
				}
				$spouse_string = $this->Html->link($spouse['Profile']['d_n'], array(
					'controller' => 'profiles',
					'action'     => 'view',
					$spouse['Profile']['id']
				)) . $spouse_string;
				
				echo $this->Form->input('Union.'.$i.'.id', array('type'=>'hidden'));
				echo $this->Form->input('Union.'.$i.'.t', array('type'=>'select', 'label'=>__('Partner', true).':',
					'options' => $marriage_type, 'between' => sprintf($marriage_string, $spouse_string).' '
				));
				?>
				<div class="input text">
					<label><?php __('Married On'); ?>:</label>
				<?php
					echo $this->Date->day('Union.'.$i.'.dom_d');
					echo $this->Date->month('Union.'.$i.'.dom_m');
					echo $this->Form->input('Union.'.$i.'.dom_y', array('type'=>'text', 'label'=>false, 'div'=>false));
				?>
				</div>
				<?php
				echo $this->Form->input('Union.'.$i.'.loc', array('type'=>'text', 'label'=>__('Married In', true).':'));
			}
			$i++;
		}
	?>
	</fieldset>
	</div>
	
<?php
		echo '<div class="input submit">';
		echo $this->Form->submit(__('Save', true), array('div' => false, 'id' => 'ProfileSubmitButton'));
		
		if ($referer = trim(base64_decode($this->Html->value('Profile.referer')))) {
			echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), $referer);
		}
		echo '</div>';
		
		echo $form->end();
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#EditProfileTabPersonal").hide();
			$("#EditProfileTabInterests").hide();
			$("#EditProfileTabRelationships").hide();
			
			<?php if ($this->Html->value('Profile.l')) { ?>
				// hide death info form living people
				$("#EditProfileDeathInfo").hide();
			<?php } ?>
			
			$("#ProfileL0").click(function(){
				$("#EditProfileDeathInfo").show();
			});
			$("#ProfileL1").click(function(){
				$("#EditProfileDeathInfo").hide();
			});
			
			
			$("#EditProfileLinkBasics").click(function(){
				$("#SidebarProfileEditMenu li.active").removeClass("active");
				$("#EditProfileLinkBasics").parent().addClass("active");
				$(".tab").hide();
				$("#EditProfileTabBasics").show();
				return false;
			});
			
			$("#EditProfileLinkPersonal").click(function(){
				$("#SidebarProfileEditMenu li.active").removeClass("active");
				$("#EditProfileLinkPersonal").parent().addClass("active");
				$(".tab").hide();
				$("#EditProfileTabPersonal").show();
				return false;
			});
			
			$("#EditProfileLinkInterests").click(function(){
				$("#SidebarProfileEditMenu li.active").removeClass("active");
				$("#EditProfileLinkInterests").parent().addClass("active");
				$(".tab").hide();
				$("#EditProfileTabInterests").show();
				return false;
			});
			
			$("#EditProfileLinkRelationships").click(function(){
				$("#SidebarProfileEditMenu li.active").removeClass("active");
				$("#EditProfileLinkRelationships").parent().addClass("active");
				$(".tab").hide();
				$("#EditProfileTabRelationships").show();
				return false;
			});
			
			$('#ProfileEditForm').submit(function(){
				$('#ProfileSubmitButton').attr('disabled', true);
			});
		});
	</script>
</div>