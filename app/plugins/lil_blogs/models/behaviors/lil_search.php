<?php 
/* SVN FILE: $Id: lil_search.php 123 2009-06-24 17:17:12Z miha@nahtigal.com $ */
/**
 * Short description for lil_search.php
 *
 * Long description for lil_search.php
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
 * @package       lil
 * @subpackage    lil.models.behaviors
 * @since         v 1.0
 * @version       $Revision: 123 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-06-24 19:17:12 +0200 (sre, 24 jun 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * LilSearchBehavior class
 *
 * @uses          ModelBehavior
 * @package       lil
 * @subpackage    lil.models.behaviors
 */
class LilSearchBehavior extends ModelBehavior {
/**
 * $__defaultSettings property
 *
 * Default model settings  
 *
 * @var array
 * @access private
 */
	var $__defaultSettings = array(
		'enabled'			=> true,
		
		'indexPath'			=> null,
		'findOptions'		=> array(),						// options for find indexing - contain, fields, conditions, order... 
		'analyzer'			=> 'Utf8Num_CaseInsensitive',	// Default, Text, TextNum, Utf8, Utf8Num, Utf8_CaseInsensitive, Utf8Num_CaseInsensitive
		'storeFields'		=> false,						// should behavior store fields in index - default: false (return only Model ids)
		'pathReplacements'	=> array(),
	);
/**
 * setup method
 * 
 * This is a behavior's setup callback.
 * $config for LilSearchBehavior can include any of variables from $this->__defaultSettings
 * $config could also be empty - then it would be configured with default settings    
 *
 * @param object &$model - instance of model
 * @param array $config
 * @access public
 * @return void
 */
	function setup(&$model, $config = array()) {
		$this->settings[$model->alias] = array_merge($this->__defaultSettings, (array)$config);
		
		$this->lilSearchAddReplace($model, '{WWW_ROOT}', WWW_ROOT);
		$this->lilSearchAddReplace($model, '{APP}' , APP);
		$this->lilSearchAddReplace($model, '{TMP}', TMP);
		$this->lilSearchAddReplace($model, '{DS}', DS);
				
		if (empty($config['indexPath'])) {
			$this->settings[$model->alias]['indexPath'] = TMP.'lucene'.DS.$model->alias;
		} else {
			$this->settings[$model->alias]['indexPath'] = $this->__replacePseudoConstants($model, $config['indexPath']);
		}
		
		if (!empty($config['findOptions'])) {
			$this->settings[$model->alias]['findOptions'] = (array)$config['findOptions'];
		}
		if (!isset($this->settings[$model->alias]['findOptions']['conditions'])) $this->settings[$model->alias]['findOptions']['conditions'] = array();
		
		if (isset($config['storeFields'])) {
			$this->settings[$model->alias]['storeFields'] = (boolean)$config['storeFields'];
		}
		
		if (isset($config['enabled'])) {
			$this->settings[$model->alias]['enabled'] = $config['enabled'];
		}
		
		if (isset($config['analyzer']) && in_array($config['analyzer'], array('Text', 'TextNum', 'Utf8', 'Utf8Num', 'Utf8_CaseInsensitive', 'Utf8Num_CaseInsensitive'))) {
			$this->settings[$model->alias]['analyzer'] = $config['analyzer'];
		}

		if (!App::import('Vendor', 'Zend/Search/Lucene', array('file' => 'Zend/Search/Lucene.php'))) {
			$this->settings[$model->name]['enabled'] = false;
			return false;
		}

		// try to open index. create index on failure
		try {
			$this->settings[$model->alias]['index'] = Zend_Search_Lucene::open($this->settings[$model->alias]['indexPath']);
		} catch (Exception $e) {
			$this->settings[$model->alias]['index'] = Zend_Search_Lucene::create($this->settings[$model->alias]['indexPath']);
		}
		
		// init analyzer
		if ($this->settings[$model->alias]['analyzer']!='Default') {
			$analyzer = 'Zend_Search_Lucene_Analysis_Analyzer_Common_'.$this->settings[$model->alias]['analyzer'];
			Zend_Search_Lucene_Analysis_Analyzer::setDefault(new $analyzer());
		}
	}
/**
 * afterSave method
 * 
 * @param object &$model - instance of model
 * @access public
 * @return bool
 */
	function afterSave(&$model) {
		if (!$this->settings[$model->alias]['enabled']) return true;
		if (!empty($model->id)) {
			$this->lilSearchIndex($model, $model->id);			
		}
		
		return true;
	}
/**
 * lilSearchEnabled method
 *
 * Return lilSearch status
 *
 * @param object &$model - instance of model
 * @access public
 * @return bool
 */
	function lilSearchEnabled(&$model) {
		return $this->settings[$model->name]['enabled'];
	}
/**
 * lilSearchEnable method
 *
 * Enable or disable LilSearch (in Model's callback actions)
 *
 * @param object &$model - instance of model
 * @param bool $enable - enable/disable
 * @access public
 * @return bool
 */
	function lilSearchEnable(&$model, $enable = null) {
		if ($enable !== null) {
			$this->settings[$model->name]['enabled'] = $enable;
		}
		return $this->settings[$model->name]['enabled'];
	}
/**
 * lilSearch method
 *
 * Execute Lucene search 
 *
 * @param object &$model - instance of model
 * @param mixed $params - string with field or array with 'conditions', 'order', 'fields' and 'limit' parameters
 * @access public
 * @return array Result set similar to cake's result from find function.
 */
	function lilSearch(&$model, $params=null) {
		if (!$this->settings[$model->name]['enabled']) return false;
		
		$criterio = '';
		if (is_string($params)) {
			$criterio = $params;
		} else if (is_array($params['conditions'])) {
			// TODO: build custom query
			$terms = array();
			foreach ($params['conditions'] as $condition) {
			}
			$criterio = new Zend_Search_Lucene_Search_Query_Term($term);
		} else if (isset($params['conditions'])) {
			
			// query is a user defined string
			$criterio = $params['conditions'];
		}
		
		$orders = array();
		if (is_array($params)) {
			if (!empty($params['order'])) {
				if (is_string($params['order'])) {
					$orders[] = $params['order'];
				}
				
				$orders = array();
				foreach ((array)$params['order'] as $order_value) {
					
					$orders = array_merge($orders, explode(' ', str_replace('.', '___', $order_value)));
				}
				
				foreach ((array)$orders as $key=>$order_value) {
					if ($order_value=='SORT_NUMERIC') $orders[$key] = SORT_NUMERIC;
					else if ($order_value=='SORT_STRING') $orders[$key] = SORT_STRING;
					else if ($order_value=='SORT_REGULAR') $orders[$key] = SORT_REGULAR;
					else if ($order_value=='ASC') $orders[$key] = SORT_ASC;
					else if ($order_value=='DESC') $orders[$key] = SORT_DESC;
				}
			}
		}
		
		//$hits = $this->settings[$model->alias]['index']->find($criterio);
		$hits = call_user_func_array(array($this->settings[$model->alias]['index'], 'find'), array_merge((array)$criterio, $orders));
		
		$data = array();
		foreach ($hits as $hit) {
			$fields = $hit->getDocument()->getFieldNames();
			
			$record = array();
			
			if ($this->settings[$model->alias]['storeFields']) {
				// remove id field
				if (($id_pos=array_search('___id', $fields))!==false) {
					unset($fields[$id_pos]);
				}
				// parse resultset in cakephp resultset array
				foreach ($fields as $key=>$field_name) {
					$field_array = explode('___', $field_name);
					
					$field_pos = &$record;
					$i = 1;
					foreach ($field_array as $key_name) {
						if ($i==sizeof($field_array)) {
							$field_pos[$key_name] = $hit->$field_name;
						} else  {
							if (!isset($field_pos[$key_name])) $field_pos[$key_name] = array();
							$field_pos = &$field_pos[$key_name];
						}
						$i++;
					}
					
				}
			} else {
				$record = $hit->___id;
			}
			$data[] = $record;
		}
		return $data;
	}
/**
 * lilSearchIndex method
 *
 * Index model with specified id
 *
 * @param object &$model - instance of model
 * @param integer $id - model id
 * @access public
 * @return void
 */
	function lilSearchIndex(&$model, $id) {
		$this->lilSearchDelete($model, $id);
		
		$doc = new Zend_Search_Lucene_Document();
		$data = $model->find('first', array_merge((array)$this->settings[$model->alias]['findOptions'], array('conditions'=>array($model->alias.'.id'=>$id))));
		
		$doc->addField(Zend_Search_Lucene_Field::Keyword ('___id', $id));
		$this->__addFields($model, $data, '', $doc);
		
		// Add document to the index
		$this->settings[$model->alias]['index']->addDocument($doc);
		$this->settings[$model->alias]['index']->commit();
	}
/**
 * lilSearchCount method
 *
 * Get index document count
 *
 * @param object &$model - instance of model
 * @access public
 * @return integer Count
 */
	function lilSearchCount(&$model) {
		return $this->settings[$model->alias]['index']->numDocs();
	}
/**
 * lilSearchDelete method
 *
 * Delete id from index
 *
 * @param object &$model - instance of model
 * @param integer $id - model id
 * @access public
 * @return bool Success
 */
	function lilSearchDelete(&$model, $id) {
		if (($doc_id = $this->lilSearchGetDocId($model, $id)) !== false) {
			$result = $this->lilSearchDeleteDoc($model, $doc_id);
			$this->settings[$model->alias]['index']->commit();
			return $result;
		} 
		return false;
	}
/**
 * lilSearchDeleteDoc method
 *
 * Delete doc from index
 *
 * @param object &$model - instance of model
 * @param integer $doc_id
 * @access public
 * @return bool success
 */
	function lilSearchDeleteDoc(&$model, $doc_id) {
		return $this->settings[$model->alias]['index']->delete($doc_id);
	}
/**
 * lilSearchGetDocId method
 *
 * Return doc id for Model id or false
 *
 * @param object &$model - instance of model
 * @param integer $maxBufferedDocs - minimal number of documents required before the buffered in-memory documents are written into a new segment
 * @param integer $mergeFactor - determines how often segment indices are merged by addDocument()
 * @access public
 * @return void
 */
	function lilSearchOptimize(&$model, $maxBufferedDocs = 10, $mergeFactor = 10) {
		$index = $this->settings[$model->alias]['index'];
		$index->setMaxBufferedDocs($maxBufferedDocs);
		$index->setMergeFactor($mergeFactor);
		$index->optimize();
	}
/**
 * lilSearchGetDocId method
 *
 * Return doc id for Model id or false
 *
 * @param object &$model - instance of model
 * @param integer $id - doc id
 * @access public
 * @return mixed Return doc_id on success, false on non-existing doc.
 */
	function lilSearchGetDocId(&$model, $id) {
		$term  = new Zend_Search_Lucene_Index_Term($id, '___id');
		$query = new Zend_Search_Lucene_Search_Query_Term($term);
		$hits  = $this->settings[$model->alias]['index']->find($query);
		foreach ($hits as $hit) {
			return $hit->id;
		}
		return false;
	}
/**
 * lilSearchAddReplace method
 *
 * Add pseudo constant or replacement needle for config parameters
 *
 * @param object &$model - instance of model
 * @param string $find - needle
 * @param string $replace - replace with
 * @access public
 * @return void
 */
	function lilSearchAddReplace(&$model, $find, $replace = '') {
		$this->settings[$model->name]['pathReplacements'][$find] = $replace;
	}
/**
 * __addFields method
 *
 * Add fields from data returned by Model->find()
 *
 * @param object &$model - instance of model
 * @param array $data
 * @param string $prefix
 * @param object instance of doc
 * @access private
 * @return void
 */
	function __addFields(&$model, $data, $prefix, &$doc) {
		if (empty($data)) {
			$doc->addField(Zend_Search_Lucene_Field::UnStored($prefix, '', 'utf-8'));
		} else {
			foreach ($data as $param_name=>$param_data) {
				if (is_array($param_data)) {
					$this->__addFields($model, $param_data, $prefix.$param_name.'___', $doc);
				} else {
					$field_name = $prefix.$param_name;
					if ($this->settings[$model->alias]['storeFields']) {
						$doc->addField(Zend_Search_Lucene_Field::Text($field_name, $param_data, 'utf-8'));
					} else {
						$doc->addField(Zend_Search_Lucene_Field::UnStored($field_name, $param_data, 'utf-8'));
					}
				}
			}
		}
	}
/**
 *  __replacePseudoConstants method
 *
 * Replace pseudo constants like {APP} with real ones
 *
 * @param object &$model - instance of model
 * @param string $string
 * @access private
 * @return string
 */
	function __replacePseudoConstants(&$model, &$string) {
		extract($this->settings[$model->name]);
		preg_match_all('@{\$([^{}]*)}@', $string, $r);
		foreach ($r[1] as $i => $match) {
			if (!isset($this->settings[$model->alias]['pathReplacements'][$r[0][$i]])) { 
				if (isset($$match)) {
					$this->addReplace($model, $r[0][$i], $$match);
				} elseif (isset($model->data[$model->alias][$match])) {
					$this->addReplace($model, $r[0][$i], $model->data[$model->alias][$match]);
				} else {
					trigger_error(sprintf('Cannot replace %1$s as the variable $%2$s cannot be determined ', $match, $match), E_USER_WARNING);
				}
			}
		}
		$markers = array_keys($this->settings[$model->name]['pathReplacements']);
		$replacements = array_values($this->settings[$model->name]['pathReplacements']);
		return str_replace($markers, $replacements, $string);
	}
}
?>
