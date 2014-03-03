<div id="ProfileHeadshot">
<?php
	//attachment id
	if (!empty($profile['Profile']['ta'])) {
		echo $this->Html->image($this->Html->url(array('controller'=>'attachments',
			'action'=>'display', $profile['Profile']['ta'], 'medium'), true), array(
			'id' => 'SidebarProfileViewAvatar'
		));
		//echo $this->Html->image('thumbs/Profile/'.$profile['Profile']['id'].'/'.$profile['Profile']['ta']);
	} else {
		echo $this->Html->image('add_photo_'.$profile['Profile']['g'].'.gif', array(
			'id' => 'SidebarProfileViewAvatar'
		));
	}
?>
</div>
<?php
	if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
?>
<ul>
	<li><?php 
		echo $this->Html->image('ico_family_tree.gif', array('class'=>'icon'));
		echo $this->Quicks->link(__('[Show tree] for this person', true), array('controller'=>'profiles', 'action'=>'tree', $profile['Profile']['id']));
	?></li>
	<?php
		if (!empty($Auth)) {
	?>
	<li><?php 
		echo $this->Html->image('ico_profile_edit.gif', array('class'=>'icon'));
		echo $this->Quicks->link(__('[Edit] person\'s data', true), array('controller'=>'profiles', 'action'=>'edit', $profile['Profile']['id']));
	?></li>
	<li><?php 
		echo $this->Html->image('ico_avatar.gif', array('class'=>'icon'));
		echo $this->Quicks->link(__('[Change] person\'s avatar', true), array('controller'=>'profiles', 'action'=>'edit_avatar', $profile['Profile']['id']));
	?></li>
	<li><?php 
		echo $this->Html->image('ico_reorder.gif', array('class'=>'icon'));
		echo $this->Quicks->link(__('[Reorder] children', true), array('controller'=>'profiles', 'action'=>'reorder_children', $profile['Profile']['id']));
	?></li>
		<li><?php
		if (empty($family['children']) && empty($family['marriages'])) {
			echo $this->Html->image('ico_delete.png', array('class'=>'icon'));
			echo $this->Quicks->link(__('[Delete] profile', true), array(
				'controller' => 'profiles',
				'action'     => 'delete',
				$profile['Profile']['id']), null, __('Are you sure you want to delete profile?', true)
			);
		}
	?></li>
	<?php
		}
	?>
</ul>
<br />
<?php
	} // level check
?>
<?php
	if (!empty($profile['Profile']['h_c']) || !empty($profile['Profile']['e_c']) || 
		!empty($profile['Profile']['n_n'])) {
?>
<div class="panel">
	<div class="inner">
	<div class="legend"><?php __('Personal'); ?></div>
	<ul class="label_value">
		<?php
			if (!empty($profile['Profile']['h_c'])) {
		?>
		<li>
			<span class="label"><?php __('Hair Color'); ?>:</span>
			<span class="value">
				<?php
					$hair_colors = array(
						1=>__('Auburn', true),
						2=>__('Black', true),
						3=>__('Blonde', true),
						4=>__('Brown', true),
						5=>__('Gray', true),
						6=>__('Red', true),
						
						0=>__('None', true),
						-1=>__('Other', true),
					);
					echo $hair_colors[$profile['Profile']['h_c']];
				?>
			</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['e_c'])) {
		?>
		<li>
			<span class="label"><?php __('Eye Color'); ?>:</span>
			<span class="value">
				<?php
					$eye_colors = array(
						1=>__('Amber', true),
						2=>__('Blue', true),
						3=>__('Brown', true),
						4=>__('Grey', true),
						5=>__('Green', true),
						6=>__('Hazel', true),
						
						-1=>__('Other', true),
					);
					echo $eye_colors[$profile['Profile']['e_c']];
				?>
			</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['n_n'])) {
		?>
		<li>
			<span class="label"><?php __('Nick Names'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['n_n']); ?>&nbsp;</span>
		</li>
		<?php
			}
		?>
	</ul>
	</div>
</div>
<?php
	}
	
	// display interests panel
	if (!empty($profile['Profile']['in_i']) || 
		!empty($profile['Profile']['in_a']) || 
		!empty($profile['Profile']['in_p']) || 
		!empty($profile['Profile']['in_c']) || 
		!empty($profile['Profile']['in_q']) || 
		!empty($profile['Profile']['in_m']) || 
		!empty($profile['Profile']['in_tv']) || 
		!empty($profile['Profile']['in_mu']) || 
		!empty($profile['Profile']['in_b']) || 
		!empty($profile['Profile']['in_s'])
	) {
?>
<div class="panel">
	<div class="inner">
	<div class="legend"><?php __('Interests'); ?></div>
	<ul class="label_value">
		<?php
			if (!empty($profile['Profile']['in_i'])) {
		?>
		<li>
			<span class="label"><?php __('Interests'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_i']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_a'])) {
		?>
		<li>
			<span class="label"><?php __('Activities'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_a']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_p'])) {
		?>
		<li>
			<span class="label"><?php __('People/Heroes'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_p']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_c'])) {
		?>
		<li>
			<span class="label"><?php __('Cuisines'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_c']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_q'])) {
		?>
		<li>
			<span class="label"><?php __('Quotes'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_q']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_m'])) {
		?>
		<li>
			<span class="label"><?php __('Movies'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_m']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_tv'])) {
		?>
		<li>
			<span class="label"><?php __('TV Shows'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_tv']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_mu'])) {
		?>
		<li>
			<span class="label"><?php __('Music'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_mu']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_b'])) {
		?>
		<li>
			<span class="label"><?php __('Books'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_b']); ?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['in_s'])) {
		?>
		<li>
			<span class="label"><?php __('Sports'); ?>:</span>
			<span class="value"><?php 
				echo $this->Sanitize->html($profile['Profile']['in_s']); ?>&nbsp;</span>
		</li>
		<?php
			}
		?>
	</ul>
	</div>
</div>
<?php
	}
?>