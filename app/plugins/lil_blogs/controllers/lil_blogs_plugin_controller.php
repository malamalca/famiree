<?php
/* SVN FILE: $Id: lil_blogs_plugin_controller.php 186 2009-11-26 20:03:18Z miha@nahtigal.com $ */
/**
 * Short description for lil_blogs_plugin_controller.php
 *
 * Long description for lil_blogs_plugin_controller.php
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
 * @since         v 1.1
 * @version       $Revision: 186 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-11-26 21:03:18 +0100 (čet, 26 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * LilBlogsPluginController class
 *
 * @uses          Controller
 * @package       lil_blogs
 * @subpackage    lil_blogs.controllers
 */
class LilBlogsPluginController extends Controller {
/**
 * name property
 *
 * @var string 'LilBlogsPlugin'
 * @access public
 */
	var $name = 'LilBlogsPlugin';
/**
 * autoRender property
 *
 * @var boolean
 * @access public
 */
	var $autoRender = NULL;
/**
 * autoLayout property
 *
 * @var boolean
 * @access public
 */
	var $autoLayout = NULL;
/**
 * uses property
 *
 * @var array
 * @access public
 */
	var $uses = NULL;
/**
 * handlers property
 *
 * @var array
 * @access public
 */
	var $handlers = array('initialize' => array('function' => '_initialize', 'params' => array()));
/**
 * __construct method
 *
 * @access public
 * @return void
 */
	function __construct() {
		parent::__construct();
		$this->params = Dispatcher::parseParams(Dispatcher::getUrl());
	}
/**
 * attachHandler method
 *
 * @param string $action
 * @param string $function_name
 * @param mixed $params
 * @access public
 * @return void
 */
	function attachHandler($action, $function_name, $params = array()) {
		$this->handlers[$action] = array('function' => $function_name, 'params' => (array)$params);
	}
}
?>