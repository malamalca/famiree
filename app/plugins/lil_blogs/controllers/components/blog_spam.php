<?php
/* SVN FILE: $Id: blog_spam.php 126 2009-07-02 07:21:52Z miha@nahtigal.com $ */
/**
 * Short description for blog_spam.php
 *
 * Long description for blog_spam.php
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
 * @subpackage    lil_blogs.controllers.components
 * @since         v 1.0
 * @version       $Revision: 126 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-07-02 09:21:52 +0200 (čet, 02 jul 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
define('BLOGSPAM_SPAM', 0);
define('BLOGSPAM_UNKNOWN', 1);
define('BLOGSPAM_HAM', 2);
/**
 * BlogSpamComponent class
 *
 * @uses          Object
 * @package       lil_blogs
 * @subpackage    lil_blogs.controllers.components
 */
class BlogSpamComponent extends Object {
/**
 * kses property
 *
 * @var object
 * @access public
 */
	var $kses;
/**
 * filter property
 *
 * @var object
 * @access public
 */
	var $filter = null;
/**
 * startup method
 *
 * @access public
 * @return void
 */
    function startup() {
     	App::import('Vendor', 'LilBlogs.kses5');

      	$this->kses = new kses5();

     	$this->kses->AddHTML('p',array());
     	$this->kses->AddHTML('b',array());
     	$this->kses->AddHTML('strong',array());
     	$this->kses->AddHTML('i',array());
     	$this->kses->AddHTML('em',array());
     	$this->kses->AddHTML('br',array());
     	$this->kses->AddHTML('address',array());
     	$this->kses->AddHTML('code',array());
     	$this->kses->AddHTML('pre',array());
     	$this->kses->AddHTML('ol',array());
     	$this->kses->AddHTML('ul',array());
     	$this->kses->AddHTML('li',array());
     	$this->kses->AddHTML('dl',array());
     	$this->kses->AddHTML('dt',array());
     	$this->kses->AddHTML('dd',array());
     	$this->kses->AddHTML('blockquote',array());
     	$this->kses->AddHTML('strike',array());
     	$this->kses->AddHTML('q',array());
     	$this->kses->AddHTML('ins',array());
     	$this->kses->AddHTML('del',array());
     	$this->kses->AddHTML('tt',array());
     	$this->kses->AddHTML('sub',array());
     	$this->kses->AddHTML('sup',array());
     	$this->kses->AddHTML('var',array());
     	$this->kses->AddHTML('cite',array());
     	$this->kses->AddHTML('acronym',array('lang'=>1,'title'=>1));
     	$this->kses->AddHTML('abbr',array('lang'=>1,'title'=>1));
     	$this->kses->AddHTML('a',array('href'=>1,'hreflang'=>1,'rel'=>1));
     	
     	$filter_name = Configure::read('LilBlogsPlugin.spamFilter');
     	if (empty($filter_name)) $filter_name = 'Snook';
     	$filter_name = 'BlogSpam'.$filter_name;
     	App::import('Vendor', 'LilBlogs.'.$filter_name);
		$this->filter = new $filter_name();
    }
/**
 * categorize method
 * 
 * Categorize comment into categories.
 *
 * @param string $comment  
 * @access public
 * @return int
 */
	function categorize($comment) {
		if ($this->filter && method_exists($this->filter, 'categorize')) {
	    	return $this->filter->categorize($comment);
 		} else {
 			return BLOGSPAM_HAM;
 		}
    }
/**
 * untrain method
 *
 * @param mixed $id
 * @access public
 * @return bool
 */
   	function untrain($id) {
   		if (method_exists($this->filter, 'untrain')) {
			return $this->filter->untrain($id);
		} else return true;
	}
/**
 * untrain method
 *
 * @param mixed $id
 * @param int $status
 * @param string $comment
 * @access public
 * @return bool
 */
	function train($id, $status, $comment) {
		if (method_exists($this->filter, 'train')) {
			return $this->filter->train($id, $status, $comment);
		} else return true;
	}
/**
 * clean method
 *
 * @param mixed $item
 * @access public
 * @return mixed
 */
	function clean($item) {
		$item = preg_replace('/<([^a-zA-Z\/])/','&lt;$1',$item);
	    $item = $this->kses->Parse($item);
  	  	return $item;
	}
}
?>
