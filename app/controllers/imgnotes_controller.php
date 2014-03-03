<?php
/* SVN FILE: $Id: imgnotes_controller.php 156 2010-01-15 14:26:08Z miha.nahtigal $ */
/**
 * Short description for imgnotes_controller.php
 *
 * Long description for imgnotes_controller.php
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
 * @package       famiree 
 * @subpackage    famiree.app.controllers
 * @since         v 1.0
 * @version       $Revision: 156 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2010-01-15 15:26:08 +0100 (pet, 15 jan 2010) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * ImgnotesController class
 *
 * @uses          AppController
 * @package       base
 * @subpackage    base.controllers
 */
class ImgnotesController extends AppController {
/**
 * name property
 *
 * @var string 'Imgnotes'
 * @access public
 */
	var $name = 'Imgnotes';
/**
 * add function
 *
 * @access public
 * @return void
 */
	function add() {
		if (!empty($this->data)) {
			$this->Imgnote->create();
			
			$this->data['Imgnote']['user_id'] = $this->Auth->user('id');
			
			if ($this->Imgnote->save($this->data)) {
				if (!empty($this->data['Imgnote']['profile_id'])) {
					$al_data = array(
						'attachment_id' => $this->data['Imgnote']['attachment_id'],
						'class'         => 'Profile',
						'foreign_id'    => $this->data['Imgnote']['profile_id']
					);
					$AttachmentsLink =& ClassRegistry::init('AttachmentsLink');
					if (!($AttachmentsLink->hasAny($al_data))) {
						$AttachmentsLink->save($al_data);
					}
				}
				
				// crop to a new image
				if (!empty($this->data['Imgnote']['crop_to_new']) &&
					($size_large = $this->Imgnote->Attachment->getImageSize(
						$this->data['Imgnote']['attachment_id'], 'large')) &&
					($size_original = $this->Imgnote->Attachment->getImageSize(
						$this->data['Imgnote']['attachment_id'], 'original')))
				{
					$scale_factor = $size_original['width'] / $size_large['width'];
					$x1 = round($this->data['Imgnote']['x1'] * $scale_factor);
					$y1 = round($this->data['Imgnote']['y1'] * $scale_factor);
					$width = round($this->data['Imgnote']['width'] * $scale_factor);
					$height = round($this->data['Imgnote']['height'] * $scale_factor);
					
					if ($uuid = $this->Imgnote->Attachment->cropToNewImage(
						$this->data['Imgnote']['attachment_id'], 
						$x1, $y1, $width, $height, 
						//$this->data['Imgnote']['profile_title'], 
						$this->data['Imgnote']['note'], 
						$this->data['Imgnote']['profile_id'])) 
					{
						$this->setFlash(__('Image Note has been successfully saved plus new image has been created.', true));
						return $this->redirect(array('controller'=>'attachments', 'action'=>'view', $uuid));
					}
				}
				
				$this->setFlash(__('Image Note has been successfully saved.', true));
				
				$referer = trim(base64_decode(@$this->data['Imgnote']['referer']));
				if (!empty($referer)) {
					return $this->redirect($referer);
				} else {
					return $this->redirect(array('controller' => 'imgnotes', 'action' => 'view', $this->Imgnote->id));
				}
			} else {
				$this->setFlash(__('There are some errors in the form. Please correct all marked fields below.', true), 'error');
			}
		} else {
			if (is_numeric($id)) {
				if (!$this->data = $this->Imgnote->read(null, $id)) {
					$this->error404();
				}
			} else if (!is_null($id)) {
				$this->error404();
			}
			$this->data['Imgnote']['referer'] = base64_encode($this->referer(''));
		}
		$this->set('sidebar', '');
	}

/**
 * delete function
 *
 * @param mixed $id
 * @access public
 * @return void
 */
	function delete($id=null) {
		if (is_numeric($id)) {
			if ($this->Imgnote->delete($id)) {
				$this->setFlash(__('Image note has been successfully deleted.', true));
			} else {
				$this->setFlash(__('Deleting Image note has failed.', true), 'error');
			}
			$this->redirect($this->referer());
		} else {
			$this->error404();
		}
	}
}

?>