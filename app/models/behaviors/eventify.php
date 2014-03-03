<?php
/**
 * Eventify Behavior.
 *
 * Allow the model to be associated with any other model object
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
 * @link          www.nahtigal.com
 * @package       base
 * @subpackage    base.models.behaviors
 * @since         v 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Eventify class
 *
 * @uses          ModelBehavior
 * @package       base
 * @subpackage    base.models.behaviors
 */
class EventifyBehavior extends ModelBehavior {
/**
 * defaultSettings property
 *
 * @var array
 * @access protected
 */
	var $_defaultSettings = array(
		'classField' => 'class',
		'foreignKey' => 'foreign_id'
	);
/**
 * setup method
 *
 * @param mixed $model
 * @param array $config
 * @return void
 * @access public
 */
	function setup(&$model, $config = array()) {
		$this->settings[$model->alias] = am($this->_defaultSettings, $config);
	}
/**
 * afterSave method
 *
 * @param mixed $model
 * @param bool $created
 * @access public
 * @return void
 */
	function afterSave(&$model, $created) {
		extract($this->settings[$model->alias]);
		$this->Event =& ClassRegistry::init('Event');
		
		return true;
	}
}
?>