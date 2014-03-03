<div class="head">
	<h1><?php __d('lil_blogs', 'Blogs'); ?></h1>
</div>
<div class="index">
	<table cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php __d('lil_blogs', 'ID'); ?></th>
				<th class="left"><?php __d('lil_blogs', 'Name'); ?></th>
				<th class="left"><?php __d('lil_blogs', 'Short'); ?></th>
				<th class="left"><?php __d('lil_blogs', 'Description'); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
	<?php
		$i = 1; 
		foreach($blogs as $item) { 
	?>
		<tr<?php if($i++%2==0) echo ' class="altrow"'; ?>>
			<td class="center"><?php echo $item['Blog']['id']; ?></td>
			<td><?php echo $html->link($item['Blog']['name'], array('admin'=>'', 'controller'=>'posts', 'action'=>'index', 'blogname'=>$item['Blog']['short_name'])); ?></td>
			<td><?php echo $item['Blog']['short_name']; ?></td>
			<td><?php echo $sanitize->html($item['Blog']['description']); ?></td>
			<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/edit.gif', array('alt'=>__d('lil_blogs', 'Edit', true))), array('action'=>'admin_edit', $item['Blog']['id']), array('title'=>__d('lil_blogs', 'Edit', true)), null, false); ?></td>
			<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/delete.gif', array('alt'=>__d('lil_blogs', 'Delete', true))), array('action'=>'admin_delete', $item['Blog']['id']), array('title'=>__d('lil_blogs', 'Delete', true)), null, false); ?></td>
			<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/category.gif', array('alt'=>__d('lil_blogs', 'Show Categories', true))), array('controller'=>'categories', 'action'=>'admin_index', $item['Blog']['id']), array('title'=>__d('lil_blogs', 'Show Categories', true)), null, false); ?></td>
			<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/posts.gif', array('alt'=>__d('lil_blogs', 'Show Posts', true))), array('controller'=>'posts', 'action'=>'admin_index'), array('title'=>__d('lil_blogs', 'Show Posts', true)), null, false); ?></td>
		</tr>
	<?php } ?>
	</table>
</div>
