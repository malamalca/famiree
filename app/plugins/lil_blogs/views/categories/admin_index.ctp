<div class="head">
	<h1><?php echo __d('lil_blogs', 'Categories for', true).' '.$sanitize->html($blog['Blog']['name']); ?></h1>
</div>
<div class="index">
<table cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th><?php __d('lil_blogs', 'ID'); ?></th>
			<th class="left"><?php __d('lil_blogs', 'Name'); ?></th>
			<th class="left">&nbsp;</th>
			<th class="left">&nbsp;</th>
		</tr>
	</thead>
<?php foreach($data as $item) { ?>
	<tr>
		<td class="center"><?php echo $item['Category']['id']; ?></td>
		<td><?php echo $sanitize->html($item['Category']['name']); ?></td>
		<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/edit.gif', array('alt'=>__d('lil_blogs', 'Edit', true))), array('action'=>'admin_edit', $item['Category']['id']), array('title'=>__d('lil_blogs', 'Edit', true)), null, false); ?></td>
		<td class="center"><?php echo $html->link($html->image('/lil_blogs/img/delete.gif', array('alt'=>__d('lil_blogs', 'Delete', true))), array('action'=>'admin_delete', $item['Category']['id']), array('title'=>__d('lil_blogs', 'Delete', true)), null, false); ?></td>
	</tr>
<?php } ?>
</table>
</div>
<div><?php echo $html->link(__d('lil_blogs', 'Create a new Category', true), array('action'=>'admin_add'), null, null, false); ?></div>