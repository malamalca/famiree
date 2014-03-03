<?php
/* SVN FILE: $Id: attachment.php 123 2009-11-29 18:38:55Z miha.nahtigal $ */
/**
 * Short description for attachment.php
 *
 * Long description for attachment.php
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
 * @package       famiree
 * @subpackage    famiree.app.models
 * @version       $Revision: 123 $ 
 * @modifiedby    $LastChangedBy: miha.nahtigal $ 
 * @lastmodified  $Date: 2009-11-29 19:38:55 +0100 (ned, 29 nov 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Attachment class
 *
 * @uses          AppModel
 * @package       famiree
 * @subpackage    famiree.app.models
 */
class Attachment extends AppModel {
/**
 * name property
 *
 * @var string 'Attachment'
 * @access public
 */
	var $name = 'Attachment';
/**
 * displayField property
 *
 * @var string 'title'
 * @access public
 */
	var $displayField = 'title';
/**
 * validate property
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'user_id' => array('numeric'),
		'title'   => array('rule' => array('minLength', 1))
	);
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'LilUpload.LilUpload' => array(
			'allowedMime' => '*',
			'allowedExt' => '*',
			'dirFormat' => '{$id}',
			
			'fileFormat' => 'original',
			
			'titleField' => 'original',
			'titleFormat' => '{$full_name}',
			'sizeField' => 'filesize',
			
			'overwriteExisting' => true,
			'mustUploadFile' => true,
		),
		'LilLog.LilLog' => array(
			'userModel' => 'Profile', 
			'userKey' => 'user_id', 
			'change' => 'serialize',
			'description_ids' => false,
			'classField' => 'class',
			'foreignKey' => 'foreign_id'
		)
	);
/**
 * hasMany property
 *
 * @var array
 * @access public
 */
	var $hasMany = array(
		'AttachmentsLink' => array(
			'dependent' => true,
		), 'Imgnote'
	);
/**
 * belongsTo property
 *
 * @var array
 * @access public
 */
	var $belongsTo = array(
		'Profile' => array(
			'foreignKey' => 'user_id',
		)
	);

/**
 * __construct function
 *
 * @param mixed $id
 * @param mixed $table
 * @param mixed $ds
 * @access private
 * @return void
 */
	function __construct($id = false, $table = null, $ds = null)	{
		$this->actsAs['LilUpload']['baseDir'] = $this->getTargetFolder('uploads');
		parent::__construct($id, $table, $ds);
	}
/**
 * beforeDelete function
 *
 * @access public
 * @return void
 */
	function beforeDelete() {
		unlink($this->getTargetFolder('thumbs').$this->id . '.png');
		$f = new Folder();
		$f->delete($this->getTargetFolder('versions').$this->id);
		unset($f);
		return true;
 	}
/**
 * afterProcessUpload function
 *
 * @param mixed $data
 * @param mixed $direct
 * @access public
 * @return void
 */
	function afterProcessUpload($data, $direct) {
		// create other versions
		if (substr($data[$this->name]['mimetype'], 0, 6) == 'image/' && 
			file_exists($original_path = 
				$this->getTargetFolder('versions') . $data[$this->name]['id'] . DS . 'original'
			))
		{
			$this->processImage(
				$data[$this->name]['id'], 
				$original_path, 
				$data[$this->name]['ext']
			);
		}
			
		return true;
	}
/**
 * filter function
 *
 * @param mixed $params
 * @access public
 * @return void
 */
	function filter($params)	{
		$data = array();
		if (!isset($params['filter'])) {
			if ($recent = $this->find('first', array(
				'order' => 'Attachment.created DESC',
				'fields' => 'created')))
			{
				$data = $this->find('all', array('conditions' => array(
					'created >=' => strftime('%Y-%m-%d', strtotime($recent['Attachment']['created']))
				)));
			}
		} else {
			switch ($params['filter']) {
				case 'all':
					$data = $this->find('all', array('order' => 'id'));
					break;
			}
		}
		return $data;
	}
/**
 * getTargetFolder function
 *
 * @param mixed $type
 * @access public
 * @return void
 */
	function getTargetFolder($type='thumbs') {
		if ($type=='thumbs') {
			return IMAGES.'thumbs'.DS;
		} else {
			return APP.'uploads'.DS;
		}
 	}
/**
 * getImageSize function
 *
 * @param mixed $id
 * @param mixed $size
 * @access public
 * @return void
 */
	function getImageSize($id = false, $size = 'original')	{
		$ret = false;
		if ($data = $this->find('first', array('conditions' => array('id'=>$id))) &&
			file_exists($filename = $this->getTargetFolder('versions').$id.DS.$size)
		) {
			if ($sizes = getimagesize($filename)) {
				$ret = array();
				$ret['width'] = $sizes[0];
				$ret['height'] = $sizes[1];
			}
		}
		return $ret;
	}
/**
 * processImage function
 *
 * @param mixed $path Path to the original file
 * @access public
 * @return void
 */
	function processImage($id, $path, $ext) {
		//require_once 'wideimage/WideImage.inc.php';
		App::import('Vendor', 'WideImage', array('file' => 'wideimage' . DS . 'WideImage.inc.php'));
		
		$image = wiImage::load($path, $ext);
		
		$image->resize(640, 480, 'inside')->saveToFile($this->getTargetFolder('versions').$id.DS.'large', $ext);
		$image->resize(200, 200, 'inside')->saveToFile($this->getTargetFolder('versions').$id.DS.'medium', $ext);
		
		$thumb = $image->resize(75, 75, 'outside');
		if ($thumb->getWidth() > $thumb->getHeight()) {
			$thumb = $thumb->crop(floor(($thumb->getWidth()-75)/2), 0, 75, 75);
		} else if ($thumb->getWidth() < $thumb->getHeight()) {
			$thumb = $thumb->crop(0, floor(($thumb->getHeight()-75)/2), 75, 75);
		}
		$thumb->saveToFile($this->getTargetFolder('thumbs').$id.'.png', null, 9);
 	}
/**
 * cropToNewImage function
 *
 * @param string $id Source attachment id
 * @param int $x
 * @param int $y
 * @param int $width
 * @param int $height
 * @param string $title
 * @param int $profile_id
 * @access public
 * @return string
 */
 	function cropToNewImage($id, $x, $y, $width, $height, $title, $profile_id) {
 		if (file_exists($this->getTargetFolder('versions').$id.DS.'original')) {
 		
			App::import('Vendor', 'WideImage', array('file' => 'wideimage' . DS . 'WideImage.inc.php'));
			
			App::import('core', 'String');
			App::import('core', 'Folder');
			$uuid = String::uuid();
			
			$at = $this->read(null, $id);
			
			$image = wiImage::load($this->getTargetFolder('versions').
				$at[$this->name]['id'].DS.'original', $at[$this->name]['ext']);
			
			// crop and save to new location
			$image = $image->crop($x, $y, $width, $height);
			
			$new_file = $this->getTargetFolder('versions').$uuid.DS.'original';
			
			//create folder and save to file
			$f = new Folder($this->getTargetFolder('versions').$uuid, true);
			$image->saveToFile($new_file, $at[$this->name]['ext']);
			$this->processImage($uuid, $new_file, $at[$this->name]['ext']);
			
			$im_data = array(
				$this->name => array(
					'id' => $uuid,
					'filename' => 'original',
					'original' => Inflector::slug($title).
						'.'.$at[$this->name]['ext'],
					'ext' => $at[$this->name]['ext'],
					'mimetype' => $at[$this->name]['mimetype'],
					'filesize' => filesize($new_file),
					'height' => $height,
					'width' => $width,
					'title' => $title,
					'description' => '',
					'checksum' => md5_file($new_file)
				),
				'AttachmentsLink' => array(0=>array(
					'attachment_id' => $uuid,
					'class'         => 'Profile',
					'foreign_id'    => $profile_id
				))
			);
			$this->create();
			$this->saveAll($im_data);
			
			return $uuid;
		}
		return false;
 	}
}
?>