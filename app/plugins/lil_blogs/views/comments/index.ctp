<h1><?php 
	if (!empty($blog['Blog']['name'])) {
		echo __d('lil_blogs', 'Comments for', true).' '.$blog['Blog']['name'];
	} else {
		echo __d('lil_blogs', 'All LilBlogs comments', true);
	}
?></h1>
<?php
	$i = 0;
	foreach($recentcomments as $comment) { 
?>
	<div class="comment" id="c<?php echo $comment['Comment']['id']; ?>">
		<div class="meta">
			<?php
				echo $html->link(
					__d('lil_blogs', 'Comment on', true).
						' "'.$comment['Post']['title'].'" '.(empty($blog['Blog']['name'])?' '.__d('lil_blogs', 'on', true).' "'.$blog['Blog']['name'].'" ':'').
						__d('lil_blogs', 'by', true).' '.
						@$comment['Comment']['author'],
					array(
						'admin'=>false,
						'plugin'=>'lil_blogs',
						'controller'=>'posts',
						'action'=>'view',
						'blogname'=>$comment['Blog']['short_name'],
						'post'=>$comment['Post']['slug'].'#c'.$comment['Comment']['id']
					)
				);
			?>
			<div class="commentdate"><?php echo $time->timeAgoInWords($comment['Comment']['created']); ?></div>
		</div>
		<div class="message"><?php echo $sanitize->wpautop($comment['Comment']['body']); ?></div>
	</div>	
<?php } ?>