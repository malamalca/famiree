<?php
class LilUploadBehavior extends ModelBehavior {
	
	var $__defaultSettings = array(
		'enabled' => true,
		
		'fileField'     => 'filename',
		'dirField'      => 'dir',
		'titleField'    => 'title', // this is an original file name before applying conversion
		'mimeField'     => 'mimetype',
		'sizeField'     => 'size',
		'extField'      => 'ext',
		'widthField'    => 'width',
		'heightField'   => 'height',
		'checksumField' => 'checksum',
		
		'allowedMime' => '*', //array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png'),
		'allowedExt' => '*', //array('jpg','jpeg','gif','png'),
		'allowedSize' => '8', // '*' for no limit (in any event limited by php settings)
		'allowedSizeUnits' => 'MB', 
		'overwriteExisting' => false,
		'mustUploadFile' => false,
		/* Dynamic path/file names 
		 * The constants DS, APP, WWW_ROOT can be used if wrapped in {}
		 * To use a variable, wrap in {} if the var is not defined during setup it is assumed to be the name
		 * of a field in the submitted data
		 */
		'baseDir'     => '{APP}uploads{DS}',
		'dirFormat'   => '{$class}{DS}{$foreign_id}',  // include {$baseDir} to have absolute paths
		'fileFormat'  => '{$filename}_{$description}', // include {$dir} to store the dir & filename in one field
		//'titleFormat' => '{$full_name}',             //leave empty for unsanitized version
		
		'pathReplacements' => array(),
		'_setupError' => false
	);
	
	function setup(&$model, $config=array()) {
		$settings = am ($this->__defaultSettings, $config);
		uses('Folder');
		$this->settings[$model->name] = $settings;
		extract($this->settings[$model->name]);
		
		$this->addReplace($model, '{WWW_ROOT}', WWW_ROOT);
		$this->addReplace($model, '{APP}' , APP);
		$this->addReplace($model, '{DS}', DS);
		$path = $this->__replacePseudoConstants($model, $baseDir);	
		
		if (!file_exists($path)) {
			new Folder($path, true);
			if (!file_exists($path)) {
				trigger_error(sprintf('Base directory %s doesn\'t exist and cannot be created. ', $path), E_USER_WARNING);
				$this->settings[$model->name]['enabled'] = false;
				$this->settings[$model->name]['_setupError'] = true;
			}
		} elseif (!is_writable($path)) {
			trigger_error(sprintf('Base directory %s is not writable. ', $path), E_USER_WARNING);
			$this->settings[$model->name]['enabled'] = false;
			$this->settings[$model->name]['_setupError'] = true;
		};
		$this->settings[$model->name]['baseDir'] = $path;
		if (!$enabled) {
			return;
		}
		$this->setupUploadValidations($model);
	}

	function enableUpload(&$model, $enable = null) {
		if ($enable !== null) {
			$this->settings[$model->name]['enabled'] = $enable;
		}
		return $this->settings[$model->name]['enabled'];
	}
	
	function getLilUploadSettings(&$model) {
		return $this->settings[$model->name];
	}
	
	function addReplace(&$model, $find, $replace = '') {
		$this->settings[$model->name]['pathReplacements'][$find] = $replace;
	}
	
	function beforeDelete(&$model) {
		extract($this->settings[$model->name]);
		if (!$enabled) {
			return true;
		}
		if ($model->hasField($dirField)) {
			$data     = $model->read(array($dirField,$fileField));
			$dir      = $data[$model->name][$dirField];
			$filename = $data[$model->name][$fileField];
			$filename = $dir . DS . $filename;
		} else if ($model->hasField($fileField)) {
			$filename = $model->field($fileField);
		} else {
			// wasnt saved in a field
			return true;
		}

		if (!empty($filename) && file_exists($baseDir . $filename) && !unlink($baseDir . $filename)) {
			return false;
		}
       	return true;
 	}

	function beforeSave(&$model) {
		extract($this->settings[$model->name]);
		if (!$enabled) {
			return true;
		}
		return $this->_processUpload($model);
	}

	function checkUploadSetup(&$model, $fieldData) {
		extract($this->settings[$model->name]);
		$fieldData = $fieldData[$fileField];
		
		if ($_setupError) {
			return false;
		}
		if (!$enabled) {
			return true;
		}
		
		// dont process upload when no uploaded file exist
		if (!is_array($fieldData)) {
			$this->enableUpload($model, false);
		}
		return true;
	}

	function checkUploadError(&$model, $fieldData) {
		extract ($this->settings[$model->name]);
		$fieldData = $fieldData[$fileField];
		
		if (!$enabled || $_setupError || !is_array($fieldData)) {
			return true;
		}
		
		if (@$fieldData['size'] && $fieldData['error']) {
			return false;
		}
		return true;
	}
	
	function checkUploadExist(&$model, $fieldData) {
		extract ($this->settings[$model->name]);
		$fieldData = $fieldData[$fileField];
		
		if (!$enabled || $_setupError || !is_array($fieldData)) {
			return true;
		}
		if (empty($fieldData['name'])) {
			return false;
		}
		return true;
	}

	function checkUploadMime(&$model, $fieldData) {
		extract ($this->settings[$model->name]);
		$fieldData = $fieldData[$fileField];
		
		if (!$enabled || $_setupError || !is_array($fieldData) || !$fieldData['type'] || $allowedMime == '*') {
			return true;
		}
		if (is_array($allowedMime)) {
			if (in_array($fieldData['type'], $allowedMime)) {
				return true;
			}
		} elseif ($allowedMime == $fieldData['type']) {
			return true;
		}
		return false;
	}

	function checkUploadExt(&$model, $fieldData) {
		extract ($this->settings[$model->name]);
		$fieldData = $fieldData[$fileField];

		if (!$enabled || $_setupError || !is_array($fieldData) || !$fieldData['name'] || $allowedExt == '*') {
			return true;
		}
		$info = pathinfo($fieldData['name']);
		$fileExt = low(@$info['extension']);
		if (is_array($allowedExt)) {
			if (in_array($fileExt, $allowedExt)) {
				return true;
			}
		} elseif ($allowedExt == $fileExt) {
			return true;
		}
		return false;
	}

	function checkUploadSize(&$model, $fieldData) {
		extract ($this->settings[$model->name]);
		$fieldData = $fieldData[$fileField];
		
		if (!$enabled || $_setupError || !is_array($fieldData) || !@$fieldData['size'] || $allowedSize == '*') {
			return true;
		}
		$factor = 1;
		switch ($allowedSizeUnits) {
			case 'KB':
				$factor = 1024;
			case 'MB':
				$factor = 1024 * 1024;
		}
		
		if ($fieldData['size'] < ($allowedSize * $factor)) {
			return true;
		}
		return false;
	}

	function absolutePath(&$model, $id = null, $folderOnly = false) {
		if (!$id) {
			$id = $model->id;
		}
		extract($this->settings[$model->name]);
		
		$path = $baseDir;
		if ($model->hasField($dirField)) {
			if (isset($model->data[$model->name][$dirField])) {
				$dir = $model->data[$model->name][$dirField];
			} else {
				$dir = $model->field($dirField);
			}
			$path .= $dir . DS;
		}
		if ($folderOnly) {
			return $path;
		}
		if (isset($model->data[$model->name][$dirField])) {
			$path .= $model->data[$model->name][$fileField];
		} else {
			$path .= $model->field($fileField);
		}
		return $path; 
	}	
	
	function processUpload(&$model, $data = array()) {
		return $this->_processUpload($model, $data, true);
	}

	function setupUploadValidations(&$model) {
		extract ($this->settings[$model->name]);
		if (isset($model->validate[$fileField])) {
			$existingValidations = $model->validate[$fileField];
			if (!is_array($existingValidations)) {
				$existingValidations = array($existingValidations);
			}	
		} else {
			$existingValidations = array();
		}

		$validations['uploadSetup'] = array(
				'rule' => 'checkUploadSetup',
				'message' => __('Upload not possible. There is a problem with the setup on the server, more info available in the logs.', true)	
			);
		$validations['uploadError'] = array(
				'rule' => 'checkUploadError',
				'message' => 'An error was generated during the upload.'	
			);
		if ($allowedMime != '*') {
			if (is_array($allowedMime)) {
				$allowedMimes = implode(',', $allowedMime);
			} else {
				$allowedMimes = $allowedMime;
			}
			$validations['uploadMime'] = array(
				'rule' => 'checkUploadMime',
				'message' => sprintf(__('The submitted mime type is not permitted, only %s permitted.', true), $allowedMimes)
				);
		}
		if ($allowedExt != '*') {
			if (is_array($allowedExt)) {
				$allowedExts = implode(',', $allowedExt);
			} else {
				$allowedExts = $allowedExt;
			}
			$validations['uploadExt'] = array(
				'rule' => 'checkUploadExt',
				'message' => sprintf(__('The submitted file extension is not permitted, only %s permitted.', true), $allowedExts)
				);
		}
		if ($mustUploadFile) {
			$validations['uploadExist'] = array(
				'rule' => 'checkUploadExist',
				'message' => __('File was not uploaded.', true)
				);
		}
		$validations['uploadSize'] = array(
			'rule' => 'checkUploadSize',
			'message' => sprintf(__('The file uploaded is too big, only files less than %1$s %2$s permitted.', true), $allowedSize, $allowedSizeUnits)	
		);
		$model->validate[$fileField] = array_merge($validations, $existingValidations); //Run the behavior validations first.
	}

	function _getFilename($model, $string) {
		extract ($this->settings[$model->name]);
		if (strpos($string,'{') === false) {
			return Inflector::underscore(preg_replace('@[^\p{L}0-9]@u', '', $string));
		}
		$ret = $this->__replacePseudoConstants($model, $string);
		return $ret;
	}

	function _getPath ($model, $path) {
		extract ($this->settings[$model->name]);
		if (empty($path)) $path = '';
		
		if (strpos($path,'{') === false) {
			return $path;
		}
		if (!empty($path)) {
			$path = $this->__replacePseudoConstants($model, $path);
		}
		// try to create target folder
		new Folder ($baseDir . $path, true);
		return $path;
	}

	function _processUpload(&$model, $data = array(), $direct = false) {
		if ($data) {
			$model->data = $data;
		}
		// Double check for upload start
		extract ($this->settings[$model->name]);
		if(!isset($model->data[$model->name][$fileField])) {
			if ($direct) {
				trigger_error(sprintf('The method processUpload has been explicitly called but the filename field (%s) is not present in the submitted data. ', $fileField), E_USER_WARNING);
				return false;
			}
			return true; 
		}
		// Double check for upload end
		if (!empty($model->data[$model->name][$fileField]['name'])) {
			if (method_exists($model, 'beforeProcessUpload')) {
				if (!$model->beforeProcessUpload($model->data, $direct)) {
					return false;
				}
			}
			extract($this->settings[$model->name]);
			
			// Get file path
			$info = pathinfo($model->data[$model->name][$fileField]['name']);
			$extension = $info['extension'];
			$filename = $info['filename'];
			
			$dir = $this->_getPath($model, $dirFormat);
			if (!empty($dir)) {
				$this->addReplace($model, '{$dir}', $dir);
			}
	
			// Get filename
			uses('Sanitize');
			
			$f_name = Sanitize::paranoid($filename, array(' ', '_', '-'));
			$f_ext  = Sanitize::paranoid($extension, array(' ', '_', '-'));
			$f_full = $f_name.((!empty($f_ext))?'.'.$f_ext:'');
			
			$this->addReplace($model, '{$filename}', $f_name);
			$this->addReplace($model, '{$full_name}', $f_full);
			$this->addReplace($model, '{$extension}', $f_ext);
			
			// ability to store original filename (unsanitized) in a separate field
			if (empty($titleFormat)) {
				$model->data[$model->name][$titleField] = $filename.((!empty($extension))?'.'.$extension:'');
			} else {
				$model->data[$model->name][$titleField] = $this->__replacePseudoConstants($model, $titleFormat);
			}
			
			// this is a sanitized filename field
			$filename = $this->_getFilename($model, $fileFormat);
			$model->data[$model->name][$fileField]['name'] = $filename;
			
			// Create save path
			$saveAs = $dir . DS . $filename;
			
			// Check if file exists
			if(file_exists($baseDir . $saveAs)) {
				if($overwriteExisting) {
					if(!unlink($baseDir . $saveAs)) {
						trigger_error(sprintf('The file %s already exists and cannot be deleted. ', $saveAs), E_USER_WARNING);
						return false;
					}
				} else {
					$count = 2;
					while(file_exists($baseDir . $dir . DS . $filename . '_' . $count . '.' . $extension)) {
						$count++;
					}
					$model->data[$model->name][$fileField]['name'] = $filename . '_' . $count . '.' . $extension;
					$saveAs = $dir . DS . $filename . '_' . $count . '.' . $extension;
				}
			} 
				
			// Attempt to move uploaded file
			if(!$this->__moveUploadedFile($model->data[$model->name][$fileField]['tmp_name'], $baseDir . $saveAs)) {
				trigger_error('Couldn\'t move the uploaded file. ', E_USER_WARNING);
				return false;
			}
			
			// Update model data
			//if (!$model->hasField($dirField)) {
				//$model->data[$model->name][$fileField] = $dir . $model->data[$model->name][$fileField];
			//}
			$model->data[$model->name][$dirField] = $dir;
			$model->data[$model->name][$mimeField] = $model->data[$model->name][$fileField]['type'];				
			$model->data[$model->name][$sizeField] = $model->data[$model->name][$fileField]['size'];
			$model->data[$model->name][$fileField] = $model->data[$model->name][$fileField]['name'];
			$model->data[$model->name][$extField] = $info['extension'];
			$model->data[$model->name][$checksumField] = md5_file($baseDir . $saveAs);
			
			if (substr($model->data[$model->name][$mimeField], 0, 6)=='image/') {
				$image_info = getimagesize($baseDir . $saveAs);
				$model->data[$model->name][$widthField] = $image_info[0];
				$model->data[$model->name][$heightField] = $image_info[1];
			}
			if (method_exists($model, 'afterProcessUpload')) {
				$model->afterProcessUpload($model->data, $direct);
			}
			//$this->_afterProcessUpload($model, $data, $direct);
		} else {
			// if no file uploaded
			unset($model->data[$model->name][$fileField]);
		}
		return true;
	}
	
	function __moveUploadedFile($source, $dest) {
		return move_uploaded_file($source, $dest);
	}

	function __replacePseudoConstants($model, &$string) {
		extract($this->settings[$model->name]);
		$this->addReplace($model, '{HASH}', uniqid(''));
		
		preg_match_all('@{\$([^{}]*)}@', $string, $r);
		foreach ($r[1] as $i => $match) {
			if (!isset($this->settings[$model->name]['pathReplacements'][$r[0][$i]])) { 
				if (isset($$match)) {
					$this->addReplace($model, $r[0][$i], $$match);
				} elseif (isset($model->data[$model->name][$match])) {
					$this->addReplace($model, $r[0][$i], $model->data[$model->name][$match]);
				} else {
					trigger_error(sprintf('Cannot replace %1$s as the variable $%2$s cannot be determined ', $match, $match), E_USER_WARNING);
				}
			}
		}
		$markers = array_keys($this->settings[$model->name]['pathReplacements']);
		$replacements = array_values($this->settings[$model->name]['pathReplacements']);
		return str_replace ($markers, $replacements, $string);
	}
}
?>
