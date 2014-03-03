<?php
/**
 * LilUploadTestBehavior class
 * 
 * This si a mock class for testing LilUpload
 *
 * @uses          LilUploadBehavior
 * @package       lil
 * @subpackage    lil.tests.cases.model.lil_upload.test
 */
App::import('behavior', 'LilUpload');
/**
 * LilUploadTestBehavior class
 *
 * @uses          AppModel
 * @package       lil
 * @subpackage    lil.tests.cases.model.lil_upload.test
 */
class LilUploadTestBehavior extends LilUploadBehavior {
/**
 * __moveUploadedFile method
 *
 * Override this function with different file move mechanism as move_uploaded_file()
 * which can't be used with fake upload data because of hack protection
 *
 * @access public
 * @return void
 */
	function __moveUploadedFile($source, $dest) {
		file_put_contents($dest, file_get_contents($source));
		return true;
	}
}
?>
