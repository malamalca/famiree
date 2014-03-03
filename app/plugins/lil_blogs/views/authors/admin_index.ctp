<div class="head">
	<h1><?php __d('lil_blogs', 'Authors'); ?></h1>
</div>
<div class="index">
<table cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th><?php __d('lil_blogs', 'ID'); ?></th>
			<th><?php __d('lil_blogs', 'Name'); ?></th>
			<th><?php __d('lil_blogs', 'Email'); ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
<?php foreach($authors as $item) { ?>
	<tr>
		<td><?php echo $item['Author']['id']; ?></td>
		<td><?php echo $sanitize->html($item['Author'][Configure::read('LilBlogs.authorDisplayField')]); ?></td>
		<td><?php echo $html->link($item['Author']['email'], 'mailto:'.$item['Author']['email']); ?></td>
		<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/edit.gif', array('alt'=>__d('lil_blogs', 'Edit', true))), array('action'=>'admin_edit', $item['Author']['id']), array('title'=>__d('lil_blogs', 'Edit', true)), null, false); ?></td>
		<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/delete.gif', array('alt'=>__d('lil_blogs', 'Delete', true))), array('action'=>'admin_delete', $item['Author']['id']), array('title'=>__d('lil_blogs', 'Comments', true)), null, false); ?></td>
	</tr>
<?php } ?>
</table>
</div>
