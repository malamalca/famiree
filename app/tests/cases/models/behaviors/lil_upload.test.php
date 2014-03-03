<?php
/* SVN FILE: $Id: lil_upload.test.php 68 2009-05-27 11:57:38Z miha.nahtigal $ */
/**
 * LilUploadBehaviorTest file
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package       lil
 * @subpackage    lil.tests.cases.model.lil_upload
 * @since         CakePHP(tm) v 1.2.0.5669
 * @version       $Revision: 68 $
 * @modifiedby    $LastChangedBy: miha.nahtigal $
 * @lastmodified  $Date: 2009-05-27 13:57:38 +0200 (sre, 27 maj 2009) $
 * @license       http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
App::import('core', 'AppModel');
App::import('behavior', 'LilUpload');

define('LIL_UPLOAD_TMP', TMP.'upload_tmp'.DS);
define('LIL_UPLOAD_DEST', TMP.'upload_dest'.DS);
 
/**
 * LilUpload class
 *
 * @uses          AppModel
 * @package       lil
 * @subpackage    lil.tests.cases.model.lil_upload.test
 */
class LilUploadModel extends AppModel {
/**
* name property
*
* @var string 'Log'
* @access public
*/
 	var $name = 'LilUploadModel';
}

require_once(dirname(__FILE__).DS.'lil_upload_test.php');

/**
 * LilUploadBehaviorTest class
 *
 * @package       lil
 * @subpackage    lil.tests.cases.libs.model.lil_upload
 */
class LilUploadBehaviorTest extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'app.lil_upload_model'
	);
/**
 * startCase method
 *
 * @access public
 * @return void
 */
	function startTest($method=null) {
		$this->LilUploadModelTest =& ClassRegistry::init('LilUploadModel');
		
		App::import('core', 'Folder');
		$f = new Folder();
		$f->delete(LIL_UPLOAD_TMP);
		$f->delete(LIL_UPLOAD_DEST);
		
		$f->create(LIL_UPLOAD_TMP);
		$f->create(LIL_UPLOAD_DEST);
		file_put_contents(LIL_UPLOAD_TMP.'asdf.tmp', file_get_contents(dirname(__FILE__).DS.'boat.jpg'));
	}
/**
 * endTest method
 *
 * @access public
 * @return void
 */
	function endTest($method) {
		unset($this->LilUploadModelTest);
		ClassRegistry::flush();
		
		$f = new Folder();
		$f->delete(LIL_UPLOAD_TMP);
		$f->delete(LIL_UPLOAD_DEST);
	}
/**
 * testLilUploadEmptyUploadedFile method
 *
 * @access public
 * @return void
 */
	function testLilUploadEmptyUploadedFile() {
		$this->LilUploadModelTest->Behaviors->attach('LilUploadTest', array(
			'baseDir'    => LIL_UPLOAD_DEST,
			'dirFormat'  => NULL,
			'fileFormat' => '{$filename}',
			'mandatory'  => true
		));
		
		$result = $this->LilUploadModelTest->save(
			array('LilUploadModel'=>array(
				'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
				'description' => 'A boat an a water',
				'filename' => array()
			))
		);
		
		$this->assertFalse($result);
	}
/**
 * testLilUploadNoUploadedFile method
 *
 * @access public
 * @return void
 */
	function testLilUploadNoUploadedFile() {
		$this->LilUploadModelTest->Behaviors->attach('LilUploadTest', array(
			'baseDir'    => LIL_UPLOAD_DEST,
			'dirFormat'  => NULL,
			'fileFormat' => '{$filename}',
			'mandatory'  => false
		));
		
		$result = $this->LilUploadModelTest->save(
			array('LilUploadModel'=>array(
				'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
				'description' => 'A boat an a water',
				'filename' => 'boat.jpg'
			))
		);
		
		$this->assertFalse(empty($result));
	}
/**
 * testLilUploadToBaseDir method
 *
 * @access public
 * @return void
 */
	function testLilUploadToBaseDir() {
		$this->LilUploadModelTest->Behaviors->attach('LilUploadTest', array(
			'baseDir'    => LIL_UPLOAD_DEST,
			'dirFormat'  => NULL,
			'fileFormat' => '{$filename}.{$extension}',
		));
		
		$result = $this->LilUploadModelTest->save(
			array('LilUploadModel'=>array(
				'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
				'description' => 'A boat an a water',
				'filename' => array(
					'size' => filesize(LIL_UPLOAD_TMP.'asdf.tmp'),
					'error' => false,
					'tmp_name' => LIL_UPLOAD_TMP.'asdf.tmp',
					'name' => 'boat.jpg',
					'type' => 'image/jpeg'
				)
			))
		);
		
		$this->assertFalse(empty($result));
		$this->assertTrue(file_exists(LIL_UPLOAD_DEST.'boat.jpg'));
	}
/**
 * testLilUploadWithoutExtension method
 *
 * @access public
 * @return void
 */
	function testLilUploadWithoutExtension() {
		$this->LilUploadModelTest->Behaviors->attach('LilUploadTest', array(
			'baseDir'    => LIL_UPLOAD_DEST,
			'dirFormat'  => NULL,
			'fileFormat' => 'original',
		));
		
		$result = $this->LilUploadModelTest->save(
			array('LilUploadModel'=>array(
				'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
				'description' => 'A boat an a water',
				'filename' => array(
					'size' => filesize(LIL_UPLOAD_TMP.'asdf.tmp'),
					'error' => false,
					'tmp_name' => LIL_UPLOAD_TMP.'asdf.tmp',
					'name' => 'boa+t.jpg',
					'type' => 'image/jpeg',
				)
			))
		);
		$this->assertFalse(empty($result));
		$this->assertTrue(file_exists(LIL_UPLOAD_DEST.'original'));
		
		$data = $this->LilUploadModelTest->read();
		$this->assertEqual($data['LilUploadModel']['filename'], 'original');
	}
/**
 * testLilUploadWithoutTitleFieldFormat method
 *
 * @access public
 * @return void
 */
	function testLilUploadWithoutTitleFieldFormat() {
		$this->LilUploadModelTest->Behaviors->attach('LilUploadTest', array(
			'baseDir'    => LIL_UPLOAD_DEST,
			'dirFormat'  => NULL,
			'fileFormat' => '{$filename}.{$extension}',
			'titleField' => 'original',
		));
		
		$result = $this->LilUploadModelTest->save(
			array('LilUploadModel'=>array(
				'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
				'description' => 'A boat an a water',
				'filename' => array(
					'size' => filesize(LIL_UPLOAD_TMP.'asdf.tmp'),
					'error' => false,
					'tmp_name' => LIL_UPLOAD_TMP.'asdf.tmp',
					'name' => 'boa+t.jpg',
					'type' => 'image/jpeg',
				)
			))
		);
		$this->assertFalse(empty($result));
		$this->assertTrue(file_exists(LIL_UPLOAD_DEST.'boat.jpg'));
		
		$data = $this->LilUploadModelTest->read();
		$this->assertEqual($data['LilUploadModel']['original'], 'boa+t.jpg');
		$this->assertEqual($data['LilUploadModel']['filename'], 'boat.jpg');
	}
/**
 * testLilUploadWithTitleFieldFormat method
 *
 * @access public
 * @return void
 */
	function testLilUploadWithTitleFieldFormat() {
		$this->LilUploadModelTest->Behaviors->attach('LilUploadTest', array(
			'baseDir'    => LIL_UPLOAD_DEST,
			'dirFormat'  => NULL,
			'fileFormat' => '{$filename}.{$extension}',
			'titleField' => 'original',
			'titleFormat' => '{$full_name}',
		));
		
		$result = $this->LilUploadModelTest->save(
			array('LilUploadModel'=>array(
				'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
				'description' => 'A boat an a water',
				'filename' => array(
					'size' => filesize(LIL_UPLOAD_TMP.'asdf.tmp'),
					'error' => false,
					'tmp_name' => LIL_UPLOAD_TMP.'asdf.tmp',
					'name' => 'boa+t.jpg',
					'type' => 'image/jpeg',
				)
			))
		);
		$this->assertFalse(empty($result));
		$this->assertTrue(file_exists(LIL_UPLOAD_DEST.'boat.jpg'));
		
		$data = $this->LilUploadModelTest->read();
		$this->assertEqual($data['LilUploadModel']['original'], 'boat.jpg');
		$this->assertEqual($data['LilUploadModel']['filename'], 'boat.jpg');
	}
/**
 * testLilUploadToIdDir method
 *
 * @access public
 * @return void
 */
	function testLilUploadToIdDir() {
		$this->LilUploadModelTest->Behaviors->attach('LilUploadTest', array(
			'baseDir'    => LIL_UPLOAD_DEST,
			'dirFormat'  => '{$id}',
			'fileFormat' => '{$filename}.{$extension}',
			'sizeField'  => 'filesize',
			'extField'   => 'ext',
		));
		
		$result = $this->LilUploadModelTest->save(
			array('LilUploadModel'=>array(
				'id' => '49fb3c94-0780-4b0c-b892-0cccbcc1ccb9',
				'description' => 'A boat an a water',
				'filename' => array(
					'size' => filesize(LIL_UPLOAD_TMP.'asdf.tmp'),
					'error' => false,
					'tmp_name' => LIL_UPLOAD_TMP.'asdf.tmp',
					'name' => 'boat.jpg',
					'type' => 'image/jpeg'
				)
			))
		);
		$this->assertFalse(empty($result));
		$this->assertTrue(file_exists(LIL_UPLOAD_DEST.'49fb3c94-0780-4b0c-b892-0cccbcc1ccb9'.DS.'boat.jpg'));
	}
}
