<?php
/* SVN FILE: $Id: routes.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          www.nahtigal.com
 * @package       famiree 
 * @subpackage    famiree.app.config
 * @since         v 1.0
 * @version       $Revision: 113 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-08-16 12:09:41 +0200 (ned, 16 avg 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'dashboard'));
	
	Router::connect('/login', array(
		'admin' => null,
		'plugin' => 'lil_users',
		'controller' => 'users',
		'action' => 'login'
	));
	Router::connect('/logout', array(
		'admin' => null,
		'plugin' => 'lil_users',
		'controller' => 'users',
		'action' => 'logout'
	));
	
	Router::connect('/settings', array('controller' => 'settings', 'action' => 'lang'));
	
	Router::connect('/memories', array(
		'plugin'     => 'lil_blogs',
		'controller' => 'posts', 
		'action'     => 'index', 
		'blogname'   => 'memories'
	));
	
	Router::connect('/memories/feed', array(
		'plugin'     => 'lil_blogs',
		'controller' => 'posts', 
		'action'     => 'index', 
		'blogname'   => 'memories',
		'url'        => array('ext' => 'rss')
	));
	
	Router::connect('/memories/view/:post', array(
		'plugin'     => 'lil_blogs',
		'controller' => 'posts', 
		'action'     => 'view', 
		'blogname'   => 'memories',
	), array(
		'post' => '[A-Za-z0-9_-]+(\.rss)?'
	));
	
	Router::connect('/memories/view/:post/#:comment', array(
		'plugin'     => 'lil_blogs',
		'controller' => 'posts', 
		'action'     => 'view', 
		'blogname'   => 'memories',
	), array(
		'post'=>'[A-Za-z0-9_-]+',
		'comment'=>'[A-Za-z0-9_-]+'
	));
?>