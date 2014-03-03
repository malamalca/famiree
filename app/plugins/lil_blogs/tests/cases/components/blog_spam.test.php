<?php
/* SVN FILE: $Id: blog_spam.test.php 126 2009-07-02 07:21:52Z miha@nahtigal.com $ */
/**
 * Short description for blog_spam.test.php
 *
 * Long description for blog_spam.test.php
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
 * @subpackage    lil_blogs.tests.cases.components
 * @since         v 1.0
 * @version       $Revision: 126 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-07-02 09:21:52 +0200 (čet, 02 jul 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::import('Component', 'LilBlogs.BlogSpam');
/**
 * BlogSpamTestCase class
 *
 * @uses          CakeTestCase
 * @package       lil_blogs
 * @subpackage    lil_blogs.tests.cases.components
 */
class BlogSpamTestCase extends CakeTestCase {
/**
 * fixtures property
 *
 * @var array
 * @access public
 */
	var $fixtures = array(
		'plugin.lil_blogs.author', 'plugin.lil_blogs.authors_blog',
		'plugin.lil_blogs.comment', 'plugin.lil_blogs.post', 'plugin.lil_blogs.blog',
		'plugin.lil_blogs.category', 'plugin.lil_blogs.nb_category', 'plugin.lil_blogs.nb_wordfreq',
		'plugin.lil_blogs.nb_reference', 'plugin.lil_blogs.categories_post'
	);
/**
 * testBlogSpamInstance method
 *
 * @access public
 * @return void
 */
	function testBlogSpamInstance() {
		$component = new BlogSpamComponent();
		$this->assertTrue(is_a($component, 'BlogSpamComponent'));
	}
/**
 * testCategorizeBayes method
 *
 * @access public
 * @return void
 */
	function testCategorizeBayes() {
		Configure::write('LilBlogsPlugin.spamFilter', 'Bayes');
		$component = new BlogSpamComponent(); $component->startup();
		
		$result = $component->train(1, BLOGSPAM_HAM, array(
			'body'   => 'This is a friendly comment by my true and only love.',
			'email'  => 'real.name@goddady.com',
			'author' => 'Daddy Pearson',
			'url'    => 'http://www.godaddy.com/',
			'ip'     => '213.11.23.33'
		));
		
		$result = $component->train(2, BLOGSPAM_SPAM, array(
			'body'   => 'comment with viagra in body is generally always spam aint it so?',
			'email'  => 'info@domain.pl',
			'author' => 'Evil Spammer',
			'url'    => 'http://www.spambot.pl/',
			'ip'     => '81.2.1.22'
		));
		
		$result = $component->categorize(array('body' => 'friendly comment'));
		$this->assertEqual($result, BLOGSPAM_HAM);
		
		$result = $component->categorize(array('body' => 'spam comment viagra'));
		$this->assertEqual($result, BLOGSPAM_SPAM);
		
		$result = $component->categorize(array('body' => 'must love true viagra spam '));
		$this->assertEqual($result, BLOGSPAM_UNKNOWN);
		
		unset($component);
	}
/**
 * testCategorizeSnook method
 *
 * @access public
 * @return void
 */
	function testCategorizeSnook() {
		Configure::write('LilBlogsPlugin.spamFilter', 'Snook');
		$component = new BlogSpamComponent(); $component->startup();
		$result = $component->categorize(array(
			'body'   => 'This is a friendly comment',
			'email'  => 'real.name@goddady.com',
			'author' => 'Daddy Pearson',
			'url'    => 'http://www.godaddy.com/',
			'ip'     => '213.11.23.33'
		));
		$this->assertEqual($result, 2);
		
		$result = $component->categorize(array(
			'body'   => 'This is a spam comment with viagra.',
			'email'  => 'info@domain.pl',
			'author' => 'Evil Spammer',
			'url'    => 'http://www.spambot.pl/',
			'ip'     => '81.2.1.22'
		));
		$this->assertEqual($result, 0);
		unset($component);
	}
}
?>
