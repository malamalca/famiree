<?php
/**
 * Polymorphic Behavior.
 *
 * Allow the model to be associated with any other model object
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008, Andy Dawson
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2008, Andy Dawson
 * @link          www.ad7six.com
 * @package       base
 * @subpackage    base.models.behaviors
 * @since         v 0.1
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * PolymorphicBehavior class
 *
 * @uses          ModelBehavior
 * @package       base
 * @subpackage    base.models.behaviors
 */
class PolymorphicBehavior extends ModelBehavior {
/**
 * defaultSettings property
 *
 * @var array
 * @access protected
 */
	var $_defaultSettings = array(
		'classField' => 'class',
		'foreignKey' => 'foreign_id',
		'recursive'  => -1,
		'contain'    => null
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
 * afterFind method
 *
 * @param mixed $model
 * @param mixed $results
 * @param bool $primary
 * @access public
 * @return void
 */
	function afterFind (&$model, $results, $primary = false) {
		extract($this->settings[$model->alias]);
		if ($primary && isset($results[0][$model->alias][$classField]) && $model->recursive > 0) {
			foreach ($results as $key => $result) {
				$associated = array();
				$class = Inflector::classify($result[$model->alias][$classField]);
				$foreignId = $result[$model->alias][$foreignKey];
				if ($class && $foreignId) {
					$result = $result[$model->alias];
					if (!isset($model->$class)) {
						$model->bindModel(array('belongsTo' => array(
							$class => array(
								'conditions' => array($model->alias . '.' . $classField => $class),
								'foreignKey' => $foreignKey
							)
						)));
					}
					$conditions = array($class . '.id' => $foreignId);
					
					if (isset($this->settings[$model->alias][$class]['contain'])) {
						$contain = $this->settings[$model->alias][$class]['contain'];
					} else $contain = array();
					if (isset($this->settings[$model->alias][$class]['recursive'])) {
						$recursive = $this->settings[$model->alias][$class]['recursive'];
					} else $recursive = -1;
					
					$associated = $model->$class->find('first', compact('conditions', 'recursive', 'contain'));
					
					$name = $model->$class->find('list', compact('conditions'));
					
					if (isset($name[$foreignId])) {
						$associated[$class]['display_field'] = $name[$foreignId];
					}
					$results[$key] = Set::merge($results[$key], $associated);
				}
			}
		} elseif(isset($results[$model->alias][$classField])) {
			$associated = array();
			$class = $results[$model->alias][$classField];
			$foreignId = $results[$model->alias][$foreignKey];
			if ($class && $foreignId) {
				$result = $results[$model->alias];
				if (!isset($model->$class)) {
					$model->bindModel(array('belongsTo' => array(
						$class => array(
							'conditions' => array($model->alias . '.' . $classField => $class),
							'foreignKey' => $foreignKey
						)
					)));
				}
				$associated = $model->$class->find(array($class . '.id' => $foreignId), array('recursive' => -1));
				$associated[$class]['display_field'] = $associated[$class][$model->$class->displayField];
				$results[$class] = $associated[$class];
			}
		}
		return $results;
	}
}
?>