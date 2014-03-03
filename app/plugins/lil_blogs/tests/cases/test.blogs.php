 <?php
 /* SVN FILE: $Id: test.blogs.php 133 2009-08-04 19:00:26Z miha@nahtigal.com $ */
/**
 * Short description for test.blogs.php
 *
 * Long description for test.blogs.php
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
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases
 * @since         v 1.0
 * @version       $Revision: 133 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-08-04 21:00:26 +0200 (tor, 04 avg 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
Configure::write('LilBlogsPlugin.tablePrefix', '');

Configure::write('LilBlogsPlugin.userTable', array(
	'className'  => 'LilBlogs.Author',
	'foreignKey' => 'author_id'
));

Configure::write('LilBlogsPlugin.categoryTable', array(
	'className'  => 'LilBlogs.Category',
	'with' => 'LilBlogs.CategoriesPost',
	'withClassName' => 'CategoriesPost',
));

Configure::write('LilBlogsPlugin.authorDisplayField', 'title');
Configure::write('LilBlogsPlugin.authorsBlogTable', 'authors');
Configure::write('LilBlogsPlugin.useAdminLayout', true);

Configure::write('LilBlogsPlugin.spamFilter', 'Snook');
Configure::write('LilBlogsPlugin.allowAuthorsAnything', true);
 	
if (!class_exists('AppTestCase')) {
/**
 * AppTestCase class
 *
 * @uses          CakeTestCase
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases
 */
class AppTestCase extends CakeTestCase {
/**
 * assert404 method
 *
 * @access public
 * @return void
 */
	function assert404() {
		$this->assertError(true, 'error404');
	}
/**
 * assertRedirect method
 *
 * @param array $url
 * @access public
 * @return void
 */
	function assertRedirect($url = '') {
		if (!empty($url)) {
			$this->assertError(new PatternExpectation('/^redirect:' .
				str_replace('/', '\/', Router::url($url, true)) . '$/i'
			));
		} else {
			$this->assertError(new PatternExpectation('/redirect:/i'));
		}
	}
}
}
?>
