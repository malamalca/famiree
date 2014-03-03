<!-- BLOG INFORMATION -->
<div id="post-view">
<div class="post">
	<h1><?php echo $this->pageTitle = $sanitize->html($post['Post']['title']); ?></h1>
	<div class="meta">
		<?php
			if (!empty($post['Author'][Configure::read('LilBlogs.authorDisplayField')])) {
				printf(__d('lil_blogs', 'Posted %1$s by %2$s', true),
					$time->timeAgoInWords($post['Post']['created']),
					$post['Author'][Configure::read('LilBlogs.authorDisplayField')]
				);
			} else {
				printf(__d('lil_blogs', 'Posted %s', true),
					$time->timeAgoInWords($post['Post']['created'])
				);
			}
			
			// display list of categories
			if (!empty($post['Category'])) {
				echo ' '.__d('lil_blogs', 'in', true).' ';
				
				echo $text->toList(
					Set::format(
						$post, 
						$html->link('%1$s', array(
							'controller' => 'posts',
							'action'     => 'index',
							'blogname'   => $post['Blog']['short_name'],
							'category'   => '%2$s'
						)), 
						array('Category.{n}.name', 'Category.{n}.id')
					),
					__d('lil_blogs', 'and', true)
				);
			}
			
			// show no of comments
			echo ' (';
			// this is neccessary because of i18n extraction
			$comment_count = $post['Post']['no_comments'];
			echo $html->link(sprintf(
				__dn('lil_blogs', '1 comment', '%d comments', $comment_count, true),	$comment_count),
				array(
					'plugin'     => 'lil_blogs',
					'controller' => 'posts',
					'action'     => 'view',
					'blogname'   => $blog['Blog']['short_name'],
					'post'       => $post['Post']['slug'],
					'comment'    => 'comments'
				));
			echo ')';
			
			// show "edit this post" link
			if ($auth->user('id')) {
				echo ' | '.$html->link(__d('lil_blogs', 'Edit this post', true), array(
					'admin'=>true,
					'action'=>'edit',
					$post['Post']['id']
				));
			}
		?>
	</div>
	<div class="body">
	<?php
		echo $sanitize->wpautop($post['Post']['body']);
	?>
	</div>
</div>
<div class="commentlist" id="CommentList">
	<h1><?php __d('lil_blogs', 'Comments'); ?></h1><a name="comments"></a>
<cake:nocache>
<?php 
	if (empty($post['Comment'])) {
		if ($post['Post']['allow_comments']) {
?>
	<div id="NoComments"><?php __d('lil_blogs', 'No comments yet. Please do add yours.'); ?></div>
<?php
		}
	} else {
	$i = 1;
	foreach($post['Comment'] as $comment) {
?>
	<div class="comment" id="c<?php echo $comment['id']; ?>">
		<div class="meta">
			<span class="commentnumber"><a href="#c<?php echo $comment['id']; ?>"><?php echo $i++; ?></a></span>
			<span class="poster"><?php echo (strlen($comment['url'])>0) ? $html->link($comment['author'], $comment['url']) : $comment['author']; ?></span>
			<?php __d('lil_blogs', 'said'); ?> <?php echo $time->timeAgoInWords($comment['created']); ?>:
		</div>
		<div class="message">
			<?php echo $sanitize->wpautop($comment['body']); ?>
		</div>
	</div>
<?php }} ?>
</cake:nocache>
</div>

<?php if ($post['Post']['allow_comments']) { ?>
	<div class="form" id="CommentForm">
		<a name="addcomment"></a>
		<h1 id="PostComment"><?php __d('lil_blogs', 'Leave a Comment'); ?></h1>
<cake:nocache>
		<?php
			echo $form->create('Comment', array('url'=>Router::url(null, true).'#addcomment'));
			echo $form->hidden('post_id', array('value'=>$post['Post']['id']));
			echo $form->input('author', array('label'=>__d('lil_blogs', 'Author', true).':', 'error'=> __d('lil_blogs', 'Author is required.', true)));
			echo $form->input('email', array('label'=>__d('lil_blogs', 'Email', true).':', 'error'=> __d('lil_blogs', 'Email in proper form is required.', true)));
			echo $form->input('url', array('label'=>__d('lil_blogs', 'Url', true).':'));
			echo $form->input('body', array('label'=>__d('lil_blogs', 'Body', true).':', 'error'=> __d('lil_blogs', 'Body is required.', true)));
			echo $form->submit(__d('lil_blogs', 'Add my comment', true));
		?>
			<div id="formnote"><?php __d('lil_blogs', 'Please be respectful; your comment my be edited or marked as spam, if necessary.'); ?></div>
</cake:nocache>
		<?php echo $form->end(); ?>
	</div>
<?php } else { ?>
	<div id="CommentsClosed"><?php __d('lil_blogs', 'Sorry, comments are closed for this post. If you have any further questions or comments, feel free to contact us.');?></div>
<?php } ?>
</div>
