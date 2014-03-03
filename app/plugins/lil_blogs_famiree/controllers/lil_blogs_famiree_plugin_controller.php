<?php
/* SVN FILE: $Id: lil_blogs_famiree_controller.php 184 2009-10-21 18:52:07Z miha@nahtigal.com $ */
/**
 * Short description for lil_blogs_famiree_controller.php
 *
 * Long description for lil_blogs_famiree_controller.php
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
 * @package       lil_blogs_famiree
 * @subpackage    lil_blogs_famiree
 * @since         v 1.0
 * @version       $Revision: 184 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-21 20:52:07 +0200 (sre, 21 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Controller', 'LilBlogs.LilBlogsPlugin');
/**
 * LilBlogsFamireePlugin class
 *
 * @uses          LilBlogsFamireePluginController
 * @package       lil_blogs_famiree
 * @subpackage    lil_blogs_famiree
 */
class LilBlogsFamireePluginController extends LilBlogsPluginController {
/**
 * name property
 *
 * @var string 'LilBlogsFamireePlugin'
 * @access public
 */
	var $name = 'LilBlogsFamireePlugin';
/**
 * initialize method
 *
 * This is a plugins initialization method
 *
 * @access public
 * @return void
 */
	function _initialize() {
		$this->attachHandler('isAuthorizedPosts', '_isAuthorizedPosts');
		$this->attachHandler('before_filter', '_beforeFilterAll');
	}
/**
 * _isAuthorizedPosts method
 *
 * By default posts/index and posts/view are open to public. This is not the case here.
 *
 * @access public
 * @return void
 */
	function _isAuthorizedPosts($controller, $ret) {
		$ret['return'] = (bool)$controller->Auth->user();
		if (@$controller->params['prefix']=='admin' && ($lvl = $controller->Auth->user('lvl') )) {
		    $ret['return'] = $lvl <= LVL_EDITOR;
		}
		$ret['continue'] = false;
	    return $ret;
	}
/**
 * _beforeFilterAll method
 *
 * By default posts/index and posts/view are open to public. This is not the case here.
 *
 * @access public
 * @return void
 */
	function _beforeFilterAll($controller, $ret) {
		$controller->Auth->deny('index', 'view');
	    return $ret;
	}
}
?>