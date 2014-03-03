<?php
/* SVN FILE: $Id: lil_blogs_app_view.php 162 2009-10-14 19:48:36Z miha@nahtigal.com $ */
/**
 * A custom view class that is used for themeing
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
 * @version       $Revision: 162 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-14 21:48:36 +0200 (sre, 14 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * LilBlogsAppView view class
 *
 * @package       lil_blogs
 * @subpackage    lil_blogs
 */
class LilBlogsAppView extends View {
/**
 * System path to themed element: themed . DS . theme . DS . elements . DS
 *
 * @var string
 */
	var $themeElement = null;
/**
 * System path to themed layout: themed . DS . theme . DS . layouts . DS
 *
 * @var string
 */
	var $themeLayout = null;
/**
 * System path to themed: themed . DS . theme . DS
 *
 * @var string
 */
	var $themePath = null;
/**
 * Controller
 *
 * @var controller
 */
	var $pluginName = null;
/**
 * Enter description here...
 *
 * @param unknown_type $controller
 */
	function __construct (&$controller) {
		$plugins = Configure::read('LilBlogs.plugins');
		$this->plugins = array();
		foreach ((array)$plugins as $plugin) {
			$plugin_name = 'LilBlogs' . $plugin;
			if ($p = ClassRegistry::getObject(Inflector::underscore($plugin_name))) {
				$this->plugins[] = $p;
			}
		}
		$this->callPluginHandlers('before_construct_view');
		parent::__construct($controller);
		
		$this->theme =& $controller->theme;
		$this->pluginName =& $controller->params['plugin'];

		if (!empty($this->theme)) {
			if (is_dir(WWW_ROOT . 'plugins' . DS . $this->pluginName . DS . 'themed' . DS . $this->theme)) {
				$this->themeWeb = 'plugins/' . $this->pluginName . '/themed/' . $this->theme .'/';
			}
			/* deprecated: as of 6128 the following properties are no longer needed */
			$this->themeElement = 'themed'. DS . $this->theme . DS .'elements'. DS;
			$this->themeLayout =  'themed'. DS . $this->theme . DS .'layouts'. DS;
			$this->themePath = 'themed'. DS . $this->theme . DS;
		}
		
		$this->callPluginHandlers('after_construct_view');
	}

/**
 * Return all possible paths to find view files in order
 *
 * @param string $plugin
 * @return array paths
 * @access private
 */
	function _paths($plugin = null, $cached = true) {
		$paths = parent::_paths($plugin, $cached);

		if (!empty($this->theme)) {
			$paths = array_merge(array(WWW_ROOT . 'plugins' . DS . $this->pluginName . DS . 'themed' . DS . $this->theme . DS), $paths);
		}

		if (empty($this->__paths)) {
			$this->__paths = $paths;
		}

		return $paths;
	}
/**
 * callPluginHandlers method
 *
 * @param string $handler
 * @access public
 * @return void
 */
	function callPluginHandlers($handler, $args = null) {
		$ret = $args;
		// execute plugin handlers
		foreach ((array)$this->plugins as $plugin) {
			if (!empty($plugin->handlers[$handler]) &&
			method_exists($plugin, $plugin->handlers[$handler]['function']))
			{
				$ret = call_user_func_array(
					array($plugin, $plugin->handlers[$handler]['function']),
					array(
						'view'  => $this,
						'args'   => (array)$args,
						'params' => (array)$plugin->handlers[$handler]['params'],
					)
				);
			}
		}
		return $ret;
	}
}
?>
