<div class="head">
	<h1><?php __d('lil_blogs', 'Please select your blog'); ?></h1>
</div>
<div class="index">
	<table cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php __d('lil_blogs', 'ID'); ?></th>
				<th class="left"><?php __d('lil_blogs', 'Name'); ?></th>
				<th class="left"><?php __d('lil_blogs', 'Short'); ?></th>
				<th class="left"><?php __d('lil_blogs', 'Description'); ?></th>
			</tr>
		</thead>
	<?php
		$i = 1; 
		foreach($blogs as $item) { 
	?>
		<tr<?php if($i++%2==0) echo ' class="altrow"'; ?>>
			<td class="center"><?php echo $item['Blog']['id']; ?></td>
			<td><?php echo $html->link($item['Blog']['name'], array('admin'=>true, 'controller'=>'blogs', 'action'=>'select', $item['Blog']['id'])); ?></td>
			<td><?php echo $item['Blog']['short_name']; ?></td>
			<td><?php echo $sanitize->html($item['Blog']['description']); ?></td>
		</tr>
	<?php } ?>
	</table>
</div>
