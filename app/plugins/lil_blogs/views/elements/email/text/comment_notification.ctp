<?php
	if ($comment['Comment']['status']==BLOGSPAM_HAM) {
	printf(__d('lil_blogs', 'A new comment on the post #%1$s "%2$s" has been posted.', true)."\n",
			$post['Post']['id'], $post['Post']['title']);
	} else {
	printf(__d('lil_blogs', 'A new comment on the post #%1$s "%2$s" is waiting for moderation.', true)."\n",
			$post['Post']['id'], $post['Post']['title']);
	}
	echo $html->url(array(
		'admin' => false,
		'controller' => 'posts',
		'action' => 'view',
		'blogname' => $post['Blog']['short_name'],
		'post' => $post['Post']['slug'] . '#c' . $comment['Comment']['id']
	), true);
	echo "\n\n";
	printf(__d('lil_blogs', 'Author : %1$s', true)."\n", $comment['Comment']['author']);
	printf(__d('lil_blogs', 'E-mail : %1$s', true)."\n", $comment['Comment']['email']);
	printf(__d('lil_blogs', 'Url    : %1$s', true)."\n", $comment['Comment']['url']);
	printf(__d('lil_blogs', 'Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%1$s', true)."\n", $comment['Comment']['ip']);
	echo "\n";
	printf(__d('lil_blogs', 'Comment:', true)."\n");
	printf($comment['Comment']['body']."\n");
	echo "\n\n";
	echo __d('lil_blogs', 'Approve it', true).': ';
	echo $html->url(array(
		'admin' => true,
		'controller' => 'comments',
		'action' => 'categorize',
		$comment['Comment']['id'],
		BLOGSPAM_HAM
	), true)."\n";
	
	echo __d('lil_blogs', 'Delete it', true).': ';
	echo $html->url(array(
		'admin' => true,
		'controller' => 'comments',
		'action' => 'delete',
		$comment['Comment']['id']
	), true)."\n";
	
	echo __d('lil_blogs', 'Spam it', true).': ';
	echo $html->url(array(
		'admin' => true,
		'controller' => 'comments',
		'action' => 'categorize',
		$comment['Comment']['id'],
		BLOGSPAM_SPAM
	), true)."\n";
?>
