<?php
/* SVN FILE: $Id: bootstrap.php 151 2010-01-12 11:46:22Z miha.nahtigal $ */
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
 * @version       $Revision: 151 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-12 12:46:22 +0100 (tor, 12 jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
	/*$vendorPaths = array(APP.'vendors'.DS, ROOT.'vendors'.DS, CAKE_CORE_INCLUDE_PATH.DS.'vendors'.DS);
	
	if (defined('LIL_PATH')) {
		$pluginPaths = array(LIL_PATH.DS.'plugins'.DS);
		$vendorPaths[] = LIL_PATH.DS.'vendors'.DS;
		$helperPaths = array(LIL_PATH.DS.'views'.DS.'helpers'.DS);
		$behaviorPaths = array(LIL_PATH.DS.'models'.DS.'behaviors'.DS);
	}
	
*/
	
	define('LVL_ROOT', 2);
	define('LVL_ADMIN', 4);
	define('LVL_EDITOR', 6);
	define('LVL_VIEWER', 8);
	
	ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.APP.'vendors');
	
	if (file_exists(dirname(__FILE__).DS.'bootstrap_local.php')) {
		include dirname(__FILE__).DS.'bootstrap_local.php';
	}
?>