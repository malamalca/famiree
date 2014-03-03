<?php
/* SVN FILE: $Id: lil_blogs.php 199 2010-01-10 19:43:50Z miha@nahtigal.com $ */
/**
 * Short description for lil_blogs.php
 *
 * Long description for lil_blogs.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          http://www.nahtigal.com/
 * @package       lil_blogs
 * @subpackage    lil_blogs.views.helpers
 * @since         v 1.0
 * @version       $Revision: 199 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2010-01-10 20:43:50 +0100 (ned, 10 jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * LilBlogsHelper class
 *
 * @uses          Helper
 * @package       lil_blogs
 * @subpackage    lil_blogs.views.helpers
 */
class LilBlogsHelper extends AppHelper {
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Text');
/**
 * findPosts method
 *
 * @param string $kind Search type
 * @param mixed $params Search parameters
 * @access public
 * @return void
 */
	function findPosts($kind, $params) {
		App::import('Model', 'LilBlogs.Post');
		$Post =& ClassRegistry::init('Post');
		return $Post->find($kind, $params);
	}
/**
 * permaLink method
 *
 * @param string $blog_name
 * @param mixed $post
 * @param array $options
 * @access public
 * @return void
 */
	function permalink($blog_name, $post, $options = array()) {
		$default_options = array(
			'caption' => $post['Post']['title']
		);
		$options = array_merge($default_options, (array)$options);
		
		return $this->Html->link($options['caption'], array(
			'admin'      => false,
			'plugin'     => 'lil_blogs',
			'controller' => 'posts',
			'action'     => 'view',
			'blogname'   => $blog_name,
			'post'       => $post['Post']['slug']
		));
	}
/**
 * function exceprt
 * 
 * Extracts excerpt from text
 *
 * @param string $body
 * @param int $max_length
 * @param string $page_delimiter 
 * @return string
 * @access public
 * @static
 */
	function excerpt($body = null, $max_length=300, $page_delimiter='<!-- -- -->') {
		$ret = '';
		if (stripos($body, $page_delimiter)!==false) {
			$ret = substr($body, 0, stripos($body, $page_delimiter));
		} else {
			$ret = $this->Text->truncate($body, $max_length, '...', false, true);
		}
		return $ret;
	}
}
?>