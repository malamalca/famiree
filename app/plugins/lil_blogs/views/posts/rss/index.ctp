<?php
echo $rss->items($recentposts, 'transformRSS');

function transformRSS($post) {
	$html = new HtmlHelper();
	return array(
		'title' => $post['Post']['title'],
		'link' => $html->url(array(
			'admin'      => false,
			'plugin'     => 'lil_blogs',
			'controller' => 'posts',
			'action'     => 'view',
			'blogname'   => $post['Blog']['short_name'],
			'post'       => $post['Post']['slug']
		), true),
		'guid'  => $html->url(array(
			'admin'      => false,
			'plugin'     => 'lil_blogs',
			'controller' => 'posts',
			'action'     => 'view',
			$post['Post']['id']), true),
		'description' => $post['Post']['body'],
		'author' => $post['Author'][Configure::read('LilBlogs.authorDisplayField')],
		'pubDate' => $post['Post']['created']
	);
}
?>
