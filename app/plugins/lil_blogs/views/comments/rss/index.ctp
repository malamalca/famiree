<?php
echo $rss->items($recentcomments, 'transformRSS');

function transformRSS($comment) {
	$html = new HtmlHelper();
	return array(
		'title' => __d('lil_blogs', 'Comment on', true).' "'.$comment['Post']['title'].'" '.__d('lil_blogs', 'by', true).' '.$comment['Comment']['author'],
		'link' => $html->url(array('admin'=>false, 'plugin'=>'lil_blogs', 'controller'=>'posts', 'action'=>'view', 'blogname'=>$comment['Post']['Blog']['short_name'], 'post'=>$comment['Post']['slug'].'#c'.$comment['Comment']['id']), true),
		'guid'  => $html->url(array('admin'=>false, 'plugin'=>'lil_blogs', 'controller'=>'posts', 'action'=>'view', $comment['Post']['id'].'#c'.$comment['Comment']['id']), true),
		'description' => $comment['Comment']['body'],
		'author' => $comment['Comment']['author'],
		'pubDate' => $comment['Comment']['created']
	);
}
?>