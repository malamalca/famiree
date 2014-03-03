<?php
echo $rss->items($post['Comment'], 'transformRSS');

function transformRSS($comment) {
	$html = new HtmlHelper();
    return array(
            'title' => __d('lil_blogs', 'Comment from').' '.$comment['author'],
        	'link'  => $html->url(array('admin'=>false, 'plugin'=>'lil_blogs', 'controller'=>'posts', 'action'=>'view', $comment['post_id'], '#c'.$comment['id']), true),
            'guid'  => $comment['id'],
            'description' => $comment['body'],
            'author' => $comment['author'],
            'pubDate' => $comment['created']
    );
}

?>
