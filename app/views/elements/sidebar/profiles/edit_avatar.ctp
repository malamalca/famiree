<div id="ProfileHeadshot">
<?php
	//attachment id
	if (!empty($profile['Profile']['ta'])) {
		echo $this->Html->image($this->Html->url(array('controller'=>'attachments',
			'action'=>'display', $profile['Profile']['ta'], 'medium'), true));
	} else {
		echo $this->Html->image('add_photo_'.$profile['Profile']['g'].'.gif');
	}
?>
</div>
<div id="sidebar_hint">
	<p><?php __('Click on image you wish to set as users new avatar.'); ?></p>
	<p>
	<?php
		echo $this->Quicks->link(__('[Click here] if you wish to remove this profile\'s.', true), array(
			'controller' => 'profiles',
			'action' => 'edit_avatar',
			$profile['Profile']['id'],
			'remove')); 
	?>
	</p>
</div>