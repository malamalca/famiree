<?php
/* SVN FILE: $Id: lil_blogs_app_controller.php 195 2009-12-04 13:19:59Z miha@nahtigal.com $ */
/**
 * Short description for lil_blogs_app_controller.php
 *
 * Long description for lil_blogs_app_controller.php
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
 * @subpackage    lil_blogs
 * @since         v 1.0
 * @version       $Revision: 195 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-12-04 14:19:59 +0100 (pet, 04 dec 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * LilBlogsAppController class
 *
 * @uses          AppController
 * @package       lil_blogs
 * @subpackage    lil_blogs
 */
class LilBlogsAppController extends AppController {
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('LilBlogs.Sanitize', 'LilBlogs.Auth', 'LilBlogs.LilBlogs', 'Text', 'Form', 'Html', 'Javascript', 'Time', 'Session');
/**
 * view property
 *
 * @var string
 * @access public
 */
	var $view = 'LilBlogsApp';
/**
 * theme property
 *
 * @var string
 * @access public
 */
	var $theme = 'default';
/**
 * hasData property
 *
 * @var bool
 * @access public
 */
	var $hasData = false;
/**
 * __construct method
 *
 * @access private
 * @return void
 */
	function __construct() {
		$plugins = Configure::read('LilBlogs.plugins');
		$this->plugins = array();
		
		$i = 0;
		foreach ((array)$plugins as $plugin) {
			$plugin_name = 'LilBlogs' . $plugin;
			if (App::import('Controller', 'LilBlogs' . $plugin . '.' . $plugin_name . 'Plugin')) {
				$controller_name = $plugin_name . 'PluginController';
				if ($this->plugins[$i] = new $controller_name()) {
					$this->plugins[$i]->constructClasses();
					$this->plugins[$i]->beforeFilter();
					$this->plugins[$i]->Component->initialize($this->plugins[$i]);
					$this->plugins[$i]->Component->startup($this->plugins[$i]);
					
					// have to set this manually so finding view paths work (Email component)
					$this->plugins[$i]->plugin = Inflector::underscore($plugin_name);
					
					ClassRegistry::addObject(Inflector::underscore($plugin_name), $this->plugins[$i]);
					
					$i++;
				}
			}
		}
		
		$this->callPluginHandlers('initialize');
		$this->callPluginHandlers('before_construct_controller');
		parent::__construct();
		$this->callPluginHandlers('after_construct_controller');
	}
/**
 * beforeFilter method
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		require_once(dirname(__FILE__) . DS . 'lil_blogs_app_view.php');
		parent::beforeFilter();

		// set display field for authors
		if (isset($this->Author)) {
			Configure::write('LilBlogs.authorDisplayField', $this->Author->displayField);
		}
		
		// set blog_id
		if (!empty($this->params['admin'])) {
			if ($this->params['controller'] == 'blogs' && $this->params['action'] == 'admin_list') {
				// clear default blog
				$this->Session->delete('admin.blog_id');
			} else {
				// set from session if not in params
				if (!empty($this->params['named']['blog_id'])) {
					$this->setAdminBlog($this->params['named']['blog_id']);
				} else {
					$this->setAdminBlog($this->Session->read('admin.blog_id'));
				}
			}
		}
		$this->callPluginHandlers('before_filter');
		$this->callPluginHandlers('before_' . $this->params['controller'] . '_' . $this->params['action']);
	}
/**
 * afterFilter method
 *
 * @access public
 * @return void
 */
	function afterFilter() {
		parent::afterFilter();

		$this->callPluginHandlers('after_filter');
	}
/**
 * setAdminBlog method
 *
 * This functions sets blog id which is used as selected blog in administration.
 *
 * @param string $id
 * @access public
 * @return void
 */
	function setAdminBlog($id) {
		$this->params['admin.blog_id'] = $id;
		$this->Session->write('admin.blog_id', $this->params['admin.blog_id']);
	}
/**
 * getAdminBlog method
 *
 * This functions returns current blog id in administration.
 *
 * @access public
 * @return int
 */
	function getAdminBlog() {
		return @$this->params['admin.blog_id'];
	}
/**
 * beforeRender method
 *
 * @access public
 * @return void
 */
	function beforeRender() {
		if (!empty($this->params['admin.blog_id'])) {
			// set blog
			if ($blogs = Configure::read('LilBlogs.noBlogs')) {
				foreach ($blogs as $b) {
					if ($b['Blog']['id'] == $this->params['admin.blog_id']) {
						$blog = $b;
						break;
					}
				}
			} else {
				$blog =& ClassRegistry::init('Blog');
				$blog->recursive = -1;
				$this->set('blog', $blog->find('first', array(
					'conditions' => array('Blog.id' => $this->getAdminBlog())
				)));
			}
			
			// construct sidebar
			$sidebar = array(
				'common' => array(
					'visible' => true,
					'items' => array(
						'posts' => array(
							'visible' => true,
							'title' => __d('lil_blogs', 'Posts', true),
							'url'   => array(
								'controller' => 'posts',
								'action'     => 'index',
								'admin'      => true,
							),
							'params' => array(),
							'active' => in_array($this->params['controller'], array('posts', 'categories')),
							'expand' => in_array($this->params['controller'], array('posts', 'categories')),
							'submenu' => array(
								'edit' => array(
									'visible' => true,
									'title' => __d('lil_blogs', 'Edit', true),
									'url'   => array(
										'controller' => 'posts',
										'action'     => 'index',
										'admin'      => true,
									),
									'params' => array(),
									'active' =>
										$this->params['controller'] == 'posts' &&
										in_array($this->params['action'], array('admin_edit', 'admin_index'))
								),
								'add' => array(
									'visible' => true,
									'title' => __d('lil_blogs', 'Add New', true),
									'url'   => array(
										'controller' => 'posts',
										'action'     => 'add',
										'admin'      => true,
									),
									'params' => array(),
									'active' =>
										($this->params['controller']=='posts') &&
										($this->params['action'] == 'admin_add')
								),
								'categories' => array(
									'visible' => true,
									'title' => __d('lil_blogs', 'Categories', true),
									'url'   => array(
										'controller' => 'categories',
										'action'     => 'index',
										'admin'      => true,
									),
									'params' => array(),
									'active' => $this->params['controller']=='categories'
								),
							)
						),
						'comments' => array(
							'visible' => true,
							'title' => __d('lil_blogs', 'Comments', true),
							'url'   => array(
								'controller' => 'comments',
								'action'     => 'index',
								'admin'      => true,
							),
							'params' => array(),
							'active' => in_array($this->params['controller'], array('comments')),
							'expand' => $this->params['controller'] == 'comments',
							'submenu' => array(
								'all' => array(
									'visible' => true,
									'title' => !isset($this->Comment) ? __d('lil_blogs', 'All', true) :
									sprintf(__d('lil_blogs', 'All (%d)', true), $this->Comment->find('count', array(
										'conditions' => array_merge(
											empty($this->params['named']['post_id']) ? array() : array('Comment.post_id' => $this->params['named']['post_id']),
											array('Post.blog_id' => $this->params['admin.blog_id'])
										),
										'contain'    => array('Post'),
									))),
									'url'   => array_merge(
										array(
											'plugin' => 'lil_blogs',
											'prefix' => 'admin',
											'controller' => 'comments',
											'action' => 'index',
										),
										empty($this->params['named']['post_id']) ? array() : array('post_id' => $this->params['named']['post_id'])
									),
									'params' => array(),
									'active' => (!isset($this->params['named']['status']))
								),
								'pending' => array(
									'visible' => true,
									'title' => !isset($this->Comment) ? __d('lil_blogs', 'Pending', true) :
									sprintf(__d('lil_blogs', 'Pending (%d)', true), $this->Comment->find('count', array(
										'conditions' => array_merge(
											empty($this->params['named']['post_id']) ? array() : array('Comment.post_id' => $this->params['named']['post_id']),
											array(
												'Post.blog_id' => $this->params['admin.blog_id'],
												'Comment.status' => LILCOMMENT_PENDING
											)
										),
										'contain'    => array('Post'),
									))),
									'url'   => array_merge(
										array(
											'plugin' => 'lil_blogs',
											'prefix' => 'admin',
											'controller' => 'comments',
											'action' => 'index',
											'status' => 'pending'
										),
										empty($this->params['named']['post_id']) ? array() : array('post_id' => $this->params['named']['post_id'])
									),
									'params' => array(),
									'active' => (isset($this->params['named']['status']) && ($this->params['named']['status'] == 'pending'))
								),
								'approved' => array(
									'visible' => true,
									'title' => !isset($this->Comment) ? __d('lil_blogs', 'Approved', true) :
									sprintf(__d('lil_blogs', 'Approved (%d)', true), $this->Comment->find('count', array(
										'conditions' => array_merge(
											empty($this->params['named']['post_id']) ? array() : array('Comment.post_id' => $this->params['named']['post_id']),
											array(
												'Post.blog_id' => $this->params['admin.blog_id'],
												'Comment.status' => LILCOMMENT_APPROVED
											)
										),
										'contain'    => array('Post'),
									))),
									'url'   => array_merge(
										array(
											'plugin' => 'lil_blogs',
											'prefix' => 'admin',
											'controller' => 'comments',
											'action' => 'index',
											'status' => 'approved'
										),
										empty($this->params['named']['post_id']) ? array() : array('post_id' => $this->params['named']['post_id'])
									),
									'params' => array(),
									'active' => (isset($this->params['named']['status']) && ($this->params['named']['status'] == 'approved'))
								),
							)
						)
					)
				),
				'admin' => array(
					'visible' => true,
					'items' => array(
						'authors' => array(
							'visible' => true,
							'title' => __d('lil_blogs', 'Authors', true),
							'url'   => array(
								'controller' => 'authors',
								'action'     => 'index',
								'admin'      => true,
							),
							'params' => array(),
							'active' => in_array($this->params['controller'], array('authors')),
							'expand' => in_array($this->params['controller'], array('authors')),
							'submenu' => array(
								'edit' => array(
									'visible' => true,
									'title' => __d('lil_blogs', 'Edit', true),
									'url'   => array(
										'controller' => 'authors',
										'action'     => 'index',
										'admin'      => true,
									),
									'params' => array(),
									'active' =>
										$this->params['controller'] == 'authors' &&
										in_array($this->params['action'], array('admin_edit', 'admin_index'))
								),
								'add' => array(
									'visible' => true,
									'title' => __d('lil_blogs', 'Add New', true),
									'url'   => array(
										'controller' => 'authors',
										'action'     => 'add',
										'admin'      => true,
									),
									'params' => array(),
									'active' =>
										($this->params['controller']=='authors') &&
										($this->params['action'] == 'admin_add')
								),
							)
						),
						'blogs' => array(
							'visible' =>
								(Configure::read('LilBlogs.allowAuthorsAnything') ||
								$this->Auth->user(Configure::read('LilBlogs.authorAdminField'))),
							'title' => __d('lil_blogs', 'Blogs', true),
							'url'   => array(
								'controller' => 'blogs',
								'action'     => 'index',
								'admin'      => true,
							),
							'params' => array(),
							'active' => in_array($this->params['controller'], array('blogs')),
							'expand' => in_array($this->params['controller'], array('blogs')),
							'submenu' => array(
								'edit' => array(
									'visible' => true,
									'title' => __d('lil_blogs', 'Edit', true),
									'url'   => array(
										'controller' => 'blogs',
										'action'     => 'index',
										'admin'      => true,
									),
									'params' => array(),
									'active' =>
										$this->params['controller'] == 'blogs' &&
										in_array($this->params['action'], array('admin_edit', 'admin_index'))
								),
								'add' => array(
									'visible' => true,
									'title' => __d('lil_blogs', 'Add New', true),
									'url'   => array(
										'controller' => 'blogs',
										'action'     => 'add',
										'admin'      => true,
									),
									'params' => array(),
									'active' =>
										($this->params['controller']=='blogs') &&
										($this->params['action'] == 'admin_add')
								),
							)
						)
					) // end if items
				)
			);
			
			$sidebar = $this->callPluginHandlers('admin_sidebar', $sidebar);
			$this->set('sidebar', $sidebar);
		}
		
		$useLayout = Configure::read('LilBlogs.useAdminLayout');
		if($useLayout && isset($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			$this->layout = 'blogs_admin';
		}
		
		parent::beforeRender();
		
		$this->callPluginHandlers('before_render');
	}
/**
 * isAuthorized method
 *
 * @access public
 * @return void
 */
	function isAuthorized() {
		return $this->callPluginHandlers('isAuthorizedLilBlogs', true);
	}
/**
 * parseUrl function
 *
 * @param string $url
 * @access public
 * @return string
 */
	function parseUrl($url) {
		$url = Router::parse($url);
		$url = am($url, $url['named'], $url['pass']);
		unset($url['named']); unset($url['pass']); unset($url['url']);
		return $url;
	}
/**
 * error404 function
 *
 * @access public
 * @return void
 */
	function error404() {
		$this->cakeError('error404', array());
	}
/**
 * kses5Setup method
 *
 * @access public
 * @return void
 */
	function kses5Setup() {
		App::import('Vendor', 'LilBlogs.kses5');

		$this->kses = new kses5();

		$this->kses->AddHTML('p', array());
		$this->kses->AddHTML('b', array());
		$this->kses->AddHTML('strong', array());
		$this->kses->AddHTML('i', array());
		$this->kses->AddHTML('em', array());
		$this->kses->AddHTML('br', array());
		$this->kses->AddHTML('address', array());
		$this->kses->AddHTML('code', array());
		$this->kses->AddHTML('pre', array());
		$this->kses->AddHTML('ol', array());
		$this->kses->AddHTML('ul', array());
		$this->kses->AddHTML('li', array());
		$this->kses->AddHTML('dl', array());
		$this->kses->AddHTML('dt', array());
		$this->kses->AddHTML('dd', array());
		$this->kses->AddHTML('blockquote', array());
		$this->kses->AddHTML('strike', array());
		$this->kses->AddHTML('q', array());
		$this->kses->AddHTML('ins', array());
		$this->kses->AddHTML('del', array());
		$this->kses->AddHTML('tt', array());
		$this->kses->AddHTML('sub', array());
		$this->kses->AddHTML('sup', array());
		$this->kses->AddHTML('var', array());
		$this->kses->AddHTML('cite', array());
		$this->kses->AddHTML('acronym', array('lang' => 1, 'title' => 1));
		$this->kses->AddHTML('abbr', array('lang' => 1, 'title' => 1));
		$this->kses->AddHTML('a', array('href' => 1, 'hreflang' => 1,'rel' => 1));
	}
/**
 * kses5Clean function
 *
 * @param string $item
 * @access public
 * @return string
 */
	function kses5Clean($item) {
		if (empty($this->kses)) {
			$this->kses5Setup();
		}
		$item = preg_replace('/<([^a-zA-Z\/])/', '&lt;$1', $item);
		$item = $this->kses->Parse($item);
		return $item;
	}
/**
 * callPluginHandlers method
 *
 * @param string $handler
 * @access public
 * @return void
 */
	function callPluginHandlers($handler, $args = null) {
		// execute plugin handlers
		$ret = $args;
		foreach ((array)$this->plugins as $plugin) {
			if (!empty($plugin->handlers[$handler]) &&
			method_exists($plugin, $plugin->handlers[$handler]['function']))
			{
				$ret = call_user_func_array(
					array($plugin, $plugin->handlers[$handler]['function']),
					array(
						'controller'  => $this,
						'args'   => (array)$ret,
						'params' => (array)$plugin->handlers[$handler]['params'],
					)
				);
			}
		}
		return $ret;
	}
}
?>