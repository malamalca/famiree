<?php
/* SVN FILE: $Id: app_model.php 113 2009-08-16 10:09:41Z miha.nahtigal $ */
/**
 * Short description for app_model.php
 *
 * Long description for app_model.php
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
 * @subpackage    famiree.app
 * @since         v 1.0
 * @version       $Revision: 113 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-08-16 12:09:41 +0200 (ned, 16 avg 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       famiree
 * @subpackage    famiree.app
 */
class AppModel extends Model {
/**
 * recursive property
 *
 * @var int
 * @access public
 */
	var $recursive = -1;
/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array('Containable');
}
?>