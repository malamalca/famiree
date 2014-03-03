<?php
/* SVN FILE: $Id: lil_blogs_app_model.php 171 2009-10-16 11:29:58Z miha@nahtigal.com $ */
/**
 * Short description for lil_blogs_app_model.php
 *
 * Long description for lil_blogs_app_model.php
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
 * @version       $Revision: 171 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-10-16 13:29:58 +0200 (pet, 16 okt 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * LilBlogsAppModel class
 *
 * @uses          AppModel
 * @package       lil_blogs
 * @subpackage    lil_blogs
 */
class LilBlogsAppModel extends AppModel {
/**
 * __construct method
 *
 * @param mixed $id
 * @param mixed $table
 * @param mixed $ds
 * @access private
 * @return void
 */
	function __construct($id = false, $table = null, $ds = null) {
		if ($table_prefix = Configure::read('LilBlogs.tablePrefix')) {
			$this->tablePrefix = $table_prefix;
		}
		
		$plugins = Configure::read('LilBlogs.plugins');
		$this->plugins = array();
		foreach ((array)$plugins as $plugin) {
			$plugin_name = 'LilBlogs' . $plugin;
			
			if ($p = ClassRegistry::getObject(Inflector::underscore($plugin_name))) {
				$this->plugins[] = $p;
			}
		}
		
		$this->callPluginHandlers('before_construct_model');
		parent::__construct($id, $table, $ds);
		$this->callPluginHandlers('after_construct_model');
	}
/**
 * beforeSave method
 *
 * @access public
 * @return boolean
 */
	function beforeSave() {
		$data = $this->callPluginHandlers('before_save_model', array('data' => $this->data));
		if (isset($data['data'])) $this->data = $data['data'];
		if (isset($this->data['return'])) return (boolean)$this->data['return'];
		return true;
	}
/**
 * afterSave method
 *
 * @param boolean $created true if a new object was created
 * @access public
 * @return void
 */
	function afterSave($created = null) {
		$data = $this->callPluginHandlers('after_save_model',
			array('data' => $this->data, 'id' => $this->id, 'created' => $created)
		);
		if (isset($data['data'])) $this->data = $data['data'];
	}
/**
 * beforeFind method
 *
 * @param mixed $queryData
 * @access public
 * @return void
 */
	function beforeFind($queryData = null) {
		$queryData = $this->callPluginHandlers('before_find_model', $queryData);
		return $queryData;
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
						'model'  => $this, 
						'args'   => (array)$args,
						'params' => (array)$plugin->handlers[$handler]['params'],
					)
				);
			}
		}
		if (isset($ret)) return $ret;
	}
}
?>
