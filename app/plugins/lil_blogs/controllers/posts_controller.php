<?php
/* SVN FILE: $Id: posts_controller.php 193 2009-11-29 16:33:35Z miha@nahtigal.com $ */
/**
 * Short description for posts_controller.php
 *
 * Long description for posts_controller.php
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
 * @subpackage    lil_blogs.controllers
 * @since         v 1.0
 * @version       $Revision: 193 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-11-29 17:33:35 +0100 (ned, 29 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * PostsController class
 *
 * @uses          LilBlogsAppController
 * @package       lil_blogs
 * @subpackage    lil_blogs.controllers
 */
class PostsController extends LilBlogsAppController {
/**
 * name property
 *
 * @var string 'Posts'
 * @access public
 */
	var $name = 'Posts';
/**
 * helpers property
 *
 * @var array
 * @access public
 */
	var $helpers = array('Time', 'Paginator', 'Text');
/**
 * paginate property
 *
 * @var array
 * @access public
 */
	var $paginate = array('limit' => 25);
/**
 * components property
 *
 * @var array
 * @access public
 */
	var $components = array('RequestHandler', 'Security');
/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = array('LilBlogs.Post', 'LilBlogs.AuthorsBlog');
/**
 * beforeFilter method
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		$this->Auth->allow('index', 'view');
		parent::beforeFilter();
	}
/**
 * isAuthorized method
 *
 * @access public
 * @return bool
 */
	function isAuthorized() {
		if (!parent::isAuthorized()) return false;
		
		// plugins can also change authorization
		$c = $this->callPluginHandlers('isAuthorizedPosts', array('return' => true, 'continue' => true));
		if (!isset($c['return']) || $c['return'] == false) return false;
		
		// plugin can determine if it wants to end authorization check
		if (!isset($c['continue']) || $c['continue'] == true) {
			if (in_array($this->params['action'], array('index', 'view'))) return true;

			if (isset($this->Auth) && !Configure::read('LilBlogs.allowAuthorsAnything')) {
				if (@$this->params['admin'] && !$this->Auth->user()) return false;

				if (in_array($this->params['action'], array('admin_edit', 'admin_delete'))) {
					if (!$blog_id = $this->getAdminBlog()) return false;

					// check target post's blog id
					if (empty($this->params['pass'][0])) return false;

					// read posts's blog_id
					if (!$posts_blog_id = $this->Post->field('blog_id',
						array('Post.id' => $this->params['pass'][0])))
					{
						return false;
					}

					// check if post belongs to current blog
					if ($posts_blog_id != $blog_id) return false;

				} else if (in_array($this->params['action'], array('admin_index'))) {
					if (!$blog_id = $this->getAdminBlog()) return false;
				}

				// test if user has rights to current blog
				return
					Configure::read('LilBlogs.noBlogs') ||
					$this->Auth->user(Configure::read('LilBlogs.authorAdminField')) ||
					$this->AuthorsBlog->hasAny(array(
						'author_id' => $this->Auth->user('id'),
						'blog_id'   => $blog_id
					));
			}
		}
		return true;
	}
/**
 * beforeRender method
 *
 * @access public
 * @return void
 */
	function beforeRender() {
		// determine theme
		if (!empty($this->params['named']['theme_preview'])) {
			$this->theme = $this->params['named']['theme_preview'];
		} else {
			if ($blogname = $this->_extractBlogName()) {
				if ($blogs = Configure::read('LilBlogs.noBlogs')) {
					foreach ($blogs as $blog) {
						if ($blog['Blog']['short_name'] == $blogname && !empty($blog['Blog']['theme'])) {
							$this->theme = $blog['Blog']['theme'];
						}
					}
				} else if (
					$theme = $this->Post->Blog->field(
						'theme',
						array('Blog.short_name' => $blogname)
					)
				) {
					$this->theme = $theme;
				}
			} else if ($theme = Configure::read('LilBlogs.defaultTheme')) {
				$this->theme = $theme;
			}
		}

		parent::beforeRender();
	}
/**
 * index method
 *
 * @access public
 * @return bool
 */
	function index() {
		// extract blog data from params
		$this->params['blogname'] = $this->_extractBlogName();
		
		if ((!$blog = $this->_findBlogByShortName($this->params['blogname'])) && ($this->params['blogname'] != 'all')) {
			return $this->error404();
		}

		$search = false;
		$params = array(
			'conditions'=> array(
				'Post.status'  => 2
			),
			'limit' => Configure::read('LilBlogs.mainPageItems'),
			'order' => 'Post.created DESC',
			'contain' => array('Author')
		);
		
		if (!Configure::read('LilBlogs.noCategories')) {
			$params['contain'][] = $this->Post->hasAndBelongsToMany['Category']['withClassName'];
			$params['contain'][] = 'Category';
		}
		
		// search
		if (!empty($this->data['Post']['criterio'])) {
			if ($this->Post->lilSearchEnabled()) {
				$params['conditions']['Post.id'] = 
					$this->Post->lilSearch($this->data['Post']['criterio']);
				unset($params['order']);
				unset($params['limit']);
			} else {
				$params['conditions']['OR'] = array(
					'Post.title LIKE' => '%' . $this->data['Post']['criterio'] . '%',
					'Post.body LIKE' => '%' . $this->data['Post']['criterio'] . '%',
				);
			}
			$search['criterio'] = $this->data['Post']['criterio'];
		}
		
		// filter by category
		if (!Configure::read('LilBlogs.noCategories') &&
			!empty($this->params['named']['category']))
		{
			$this->Post->bindModel(
				array('hasOne' => array(
					$this->Post->hasAndBelongsToMany['Category']['withClassName'] => array(
						'className' => $this->Post->hasAndBelongsToMany['Category']['with'],
					)
				)), false
			);
			$params['conditions'][$this->Post->hasAndBelongsToMany['Category']['withClassName'].'.'.$this->Post->hasAndBelongsToMany['Category']['associationForeignKey']] = $this->params['named']['category'];
			$search['category'] = $this->params['named']['category'];
		}
		
		if ($this->params['blogname'] == 'all') {
			// show all posts
			$blog_name        = __d('lil_blogs', 'LilBlogs posts', true);
			$blog_description = __d('lil_blogs', 'Posts from every single blog on', true).' '.Router::url('/', true);
		} else {
			// filter posts by blog
			$params['conditions']['Post.blog_id'] = $blog['Blog']['id'];
			
			$blog_name        = $blog['Blog']['name'];
			$blog_description = $blog['Blog']['description'];
		}
		
		// is it rss?
		if ($this->RequestHandler->prefers('rss') == 'rss') {
			Configure::write('debug', 0);
			$this->set('channel', array('title' => $blog_name, 'description' => $blog_description));
			$params['limit'] = Configure::read('LilBlogs.rssItems');
		}
		
		$this->paginate = $params;
		$recent_posts = $this->paginate('Post');
		
		// add blog name
		if ($blogs = Configure::read('LilBlogs.noBlogs')) {
			foreach ($recent_posts as $k => $p) {
				$recent_posts[$k]['Blog'] = $blogs[$p['Post']['blog_id']]['Blog'];
			}
		} else {
			foreach ($recent_posts as $k => $p) {
				$recent_posts[$k]['Blog'] = $blog['Blog'];
			}
		}
		
		$this->set(compact('blog', 'search'));
		$this->set('recentposts', $recent_posts);
	}
/**
 * view method
 *
 * @access public
 * @return bool
 */
	function view() {
		if (!empty($this->params['postid']) && is_numeric($this->params['postid'])) {
			$conditions = array('Post.id'=>$this->params['postid'], 'Post.status'=>2);
		} else if (!empty($this->params['named']['postid']) && is_numeric($this->params['named']['postid'])) {
			$conditions = array('Post.id'=>$this->params['named']['postid'], 'Post.status'=>2);
		} else if (!empty($this->params['post']) && !empty($this->params['blogname'])) {
			$conditions = array('Post.slug'=>$this->params['post'], 'Post.status'=>2, 'Blog.short_name'=>$this->params['blogname']);
		} else if (!empty($this->params['named']['post']) && !empty($this->params['named']['blogname'])) {
			$conditions = array('Post.slug'=>$this->params['named']['post'], 'Post.status'=>2, 'Blog.short_name'=>$this->params['named']['blogname']);
	 	} else if (!empty($this->params['pass'][0]) && is_numeric($this->params['pass'][0])) {
	 		$conditions = array('Post.id'=>$this->params['pass'][0], 'Post.status'=>2);
	 	} else if (!empty($this->params['pass'][0]) && !empty($this->params['pass'][1])) {
			$conditions = array('Post.slug'=>$this->params['pass'][1], 'Post.status'=>2, 'Blog.short_name'=>$this->params['pass'][0]);
		} else {
			$conditions = array('Post.id'=>-1);
		}
		
		$params = array(
			'conditions' => $conditions,
			'contain'    => array('Author')
		);
		
		if (!Configure::read('LilBlogs.noCategories')) {
			$params['contain'][] = 'Category';
		}
		
		if ($blog = Configure::read('LilBlogs.noBlogs')) {
			if (isset($params['conditions']['Blog.short_name'])) {
				unset($params['conditions']['Blog.short_name']);
			}
		} else {
			$params['contain'][] = 'Blog';
		}
		
		// read post
		if (!$post = $this->Post->find('first', $params)) {
			$this->error404();
			return;
		}
		
		// read comments separately so comment_filter plugin could work
		$comments = $this->Post->Comment->find('all', array(
			'conditions' => array(
				'Comment.post_id' => $post['Post']['id'],
				'Comment.status' => LILCOMMENT_APPROVED
			),
			'recursive'  => -1
		));
		// clean up the HTML of each comment and restructure
		foreach ($comments as $comment) {
			$this->_clean($comment['Comment']);
			$post['Comment'][] = $comment['Comment'];
		}
		
		// read blog if not previously set
		if (empty($blog)) {
			$blog_params = array(
				'conditions' => array(
					'Blog.id' => $post['Post']['blog_id']
				),
				'contain' => array()
			);
			if (!Configure::read('LilBlogs.noCategories')) {
				$blog_params['contain'][] = 'Category';
			}
			$blog = $this->Post->Blog->find('first', $blog_params);
		}
		
		$this->set(compact('post', 'blog'));
		
		// save comment
		if (!empty($this->data)) {
			if ($data = $this->Post->Comment->add($this->data)) {
				$data['Comment']['id'] = $this->data['Comment']['id'] = $this->Post->Comment->id;
				$this->callPluginHandlers('after_save_comment', array('data' => $data));
			
				// notify user
				if ($data['Comment']['status'] == LILCOMMENT_APPROVED) {
					$this->Session->setFlash(__d('lil_blogs', 'Your comment has been successfully saved.', true));
				} else {
					$this->Session->setFlash(__d('lil_blogs', 'Your comment has gone into moderation.', true));
				}
			
				// redirect to self
				$this->redirect(Router::url(null, true));
			
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Uh oh, we weren\'t able to save your comment.', true), 'error');
			}
		}
		
		if ($this->RequestHandler->prefers('rss') == 'rss') {
			Configure::write('debug', 0);
			$this->set('channel', array(
				'title'       => $blog['Blog']['name'], 
				'description' => $blog['Blog']['description']
			));
		}
	}
/**
 * admin_index method
 *
 * @param int $blogid
 * @access public
 * @return void
 */
	function admin_index() {
		$blogid = $this->getAdminBlog();
		
		if (is_numeric($blogid)) { 
			$this->paginate = array(
				'conditions' => array(
					'Post.blog_id' => $blogid
				),
				'limit'   => 10,
				'order'   => 'Post.created DESC',
				'contain' => array('Author')
			);
			
			$data = $this->paginate('Post');
			$this->set(compact('data'));
		} else {
			$this->error404();
		}
	}
/**
 * admin_add method
 *
 * @param int $blogid
 * @access public
 * @return void
 */
	function admin_add() {
		$blogid = $this->getAdminBlog();
		
		if (!empty($this->data)) {
			if ($this->Post->save($this->data)) {
				$this->Session->setFlash(__d('lil_blogs', 'A new post has been created.', true));
				$this->redirect(array('action'=>'admin_index', $this->data['Post']['blog_id']));
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Please verify that the information is correct.', true), 'error');
			}
		} else if (is_numeric($blogid)) {
			$this->data['Post']['blog_id'] = $blogid;
		}
		
		if (!empty($this->data['Post']['blog_id'])) {
			if (!$blog = Configure::read('LilBlogs.noBlogs')) {
				$blog = $this->Post->Blog->findById($this->data['Post']['blog_id']);
			}
			$this->set('blogid', $this->data['Post']['blog_id']);
			
			if (!Configure::read('LilBlogs.noCategories')) {
				$conditions = array();
				if (!Configure::read('LilBlogs.noBlogs')) {
					$conditions['Category.blog_id'] = $blog['Blog']['id'];
				}
				
				$this->set('categories', $this->Post->Category->find('list', array(
					'conditions' => $conditions
				)));
			}
			
			$this->set('authors', $this->Post->Author->find('list'));
		} else {
			$this->error404();
		}
	}
/**
 * admin_edit method
 *
 * @param int $id
 * @access public
 * @return void
 */
	function admin_edit($id=null) {
		$blogid = $this->getAdminBlog();
		
		if (!empty($this->data)) {
			if ($this->Post->save($this->data)) {
				$this->Session->setFlash(__d('lil_blogs', 'Post has been saved.', true));
				
				$referer = trim(base64_decode(@$this->data['Post']['referer']));
				if (empty($referer)) {
					$this->redirect(array('action'=>'admin_index', $this->data['Post']['blog_id']));
				} else {
				    $this->redirect(array_merge(
						array('admin' => false),
						$this->parseUrl($referer),
						array('highlight_post'=>$this->Post->id)
					));
				}
			} else {
				$this->Session->setFlash(__d('lil_blogs', 'Please verify that the information is correct.', true), 'error');
			}
		} else if (is_numeric($id) && $this->data = $this->Post->read(null, $id)) {
			$this->data['Post']['referer'] = base64_encode($this->referer(''));
		} else {
			$this->error404();
		}
		
		if (!Configure::read('LilBlogs.noCategories')) {
			$conditions = array();
			if (!Configure::read('LilBlogs.noBlogs')) {
				$conditions['Category.blog_id'] = $blogid;
			}
			
			$this->set('categories', $this->Post->Category->find('list', array(
				'conditions' => $conditions
			)));
		}
		$this->set('authors', $this->Post->Author->find('list'));
	}
/**
 * admin_delete method
 *
 * @param int $id
 * @access public
 * @return void
 */
	function admin_delete($id = null) {
		$this->Post->recursive = -1;
		if (is_numeric($id) && $data = $this->Post->findById($id)) {
			$this->Post->delete($id);
			$this->Session->setFlash(__d('lil_blogs', 'Post has been deleted.', true));
			$this->redirect($this->referer());
		} else {
			$this->error404();
		}
	}
/**
 * _clean method
 *
 * @param mixed $data
 * @access private
 * @return void
 */
	function _clean(&$data) {
		$data['body'] = $this->kses5Clean($data['body']);
	}
/**
 * _extractBlogName method
 *
 * Extract blogname form parameters.
 *
 * @access private
 * @return void
 */
	function _extractBlogName() {
		if (empty($this->params['blogname'])) {
			if (!empty($this->params['named']['blogname'])) {
				return $this->params['named']['blogname'];
			} else if (!empty($this->params['pass'][0])) {
				return $this->params['pass'][0];
			} else if ($blog_name = Configure::read('defaultBlog')) {
				return $blog_name;
			} else {
				return false;
			}
		} else {
		    return $this->params['blogname'];
		}
	}
/**
 * _findBlogByShortName method
 *
 * Find blog record by short name
 *
 * @param string $name
 * @access private
 * @return void
 */
	function _findBlogByShortName($name) {
		if ($blogs = Configure::read('LilBlogs.noBlogs')) {
			foreach ($blogs as $blog) {
				if ($blog['Blog']['short_name'] == $name) return $blog;
			}
			return false;
		} else {
			$this->Post->Blog->recursive = -1;
			return $this->Post->Blog->find('first', array('conditions' => array('Blog.short_name' => $name)));
		}
	}
}
?>