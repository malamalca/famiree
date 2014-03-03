<?php
/**
 * LilFloat Behavior for CakePHP 1.2
 * 
 * @copyright     Copyright 2008, Miha Nahtigal (http://www.nahtigal.com)
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 * 
 * 
 * This LilFloat behavior for CakePHP allows you to auto convert float values from local
 * format to default format suitable for saving into database
 * 
 */
class LilFloatBehavior extends ModelBehavior {
    
/**
 * An array of settings set by the $actsAs property
 */
    var $_settings = array();

/**
 * Instantiates the behavior and sets the magic methods
 * 
 * @param object $model The Model object
 * @param array $settings Array of scope properties
 */
    function setup($model, $settings = array()) {
        $settings = (array)$settings;
        foreach ($settings as $named => $options) {
            if (!is_array($options)) {
                unset($settings[$named]);
                $settings[$options] = array();
                $named = $options;
            }
        }
        if (empty($settings['decimalSeparator'])) {
            if (!$settings['decimalSeparator'] = Configure::read('decimalSeparator')) {
                $settings['decimalSeparator'] = '.';
            }
        }
        if (!isset($settings['thousandsSeparator'])) {
            if (!$settings['thousandsSeparator'] = Configure::read('thousandsSeparator')) {
                $settings['thousandsSeparator'] = ',';
            }
        }
        $this->_settings[$model->alias] = $settings;
    }

/**
 * Model callback function which converts local float values to international representation
 * 
 */
    function beforeSave(&$model) {
        $fields = $model->schema();
        foreach ($fields as $field_name=>$field_data) {
            if (($field_data['type']=='float' || $field_data['type']=='double') && 
				!empty($this->data[$model->alias][$field_name])) 
			{
                $this->data[$model->alias][$field_name] = 
					strtr($this->data[$model->alias][$field_name], array(
						$this->_settings[$model->alias]['thousandsSeparator'] => '',
						$this->_settings[$model->alias]['decimalSeparator'] => '.',
					));
            }
        }
    }

/**
 * Sets new decimal separator
 *
 * @param string $decimalSeparator New decimal separator
 * @access public
 */
	function setDecimalSeparator(&$model, $decimalSeparator = null) {
		if (!empty($decimalSeparator)) {
			$this->_settings[$model->alias]['decimalSeparator'] = $decimalSeparator;
		}
	}

/**
 * Sets new thousands separator
 *
 * @param string $decimalSeparator New decimal separator
 * @access public
 */
	function setThousandsSeparator(&$model, $thousandsSeparator = null) {
		if (!empty($thousandsSeparator)) {
			$this->_settings[$model->alias]['thousandsSeparator'] = $thousandsSeparator;
		}
	}
    
/**
 * Checks that a value is a valid float. If $places is null, field type length is used.
 * If no decimal point is found a false will be returned. The sign is optional.
 *
 * @param array $data Field-value array pair to check
 * @param mixed $places if set $check value must have exactly $places after the decimal point
 * @return boolean Success
 * @access public
 */
	function isValidFloat(&$model, $data, $places = null) {
		// try to get decimal places from db schema
		if (is_null($places)) {
			$fields = $model->schema();
			$field_name = reset(array_keys($data));
			if (isset($fields[$field_name]) && 
				($fields[$field_name]['type']=='float' || $fields[$field_name]['type']=='double') && 
				strpos($fields[$field_name]['length'], ',')!==false) 
			{
				$places = substr($fields[$field_name]['length'], 
					strpos($fields[$field_name]['length'], ',')+1);
			}
		}
		
		//$regex = '/^[-+]?(0|([1-9]([0-9]*|([0-9]{0,2}(\,[0-9]{3})*))))(\.{1}[0-9]{2})?$/';
		$regex = '/^[-+]?(0|([1-9]([0-9]*';
		if (!empty($this->_settings[$model->alias]['thousandsSeparator'])) {
			$regex .= '|([0-9]{0,2}(\\'.$this->_settings[$model->alias]['thousandsSeparator'].'[0-9]{3})*)';
		}
		$regex .= ')))';
		
		if (!empty($places)) {
			$regex .= '\\'.$this->_settings[$model->alias]['decimalSeparator'].'{1}[0-9]{'.$places.'}';
		}
		$regex .= '$/';

		return preg_match($regex, reset($data));
		
	}
  
}