<?php 
class LilBlogsSchema extends CakeSchema {
	var $name = 'LilBlogs';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $authors = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
			'name' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 100),
			'email' => array('type'=>'string', 'null' => false, 'default' => NULL),
			'username' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 50),
			'passwd' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 50),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $author_blogs = array(
			'id' => array('type'=>'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
			'author_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'blog_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $blogs = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
			'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
			'short_name' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 100),
			'description' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'theme' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'created' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $categories = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
			'blog_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 4),
			'name' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $categories_posts = array(
			'id' => array('type'=>'integer', 'null' => false, 'length' => 10, 'key' => 'primary'),
			'category_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'post_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 4),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $comments = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'post_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
			'body' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'author' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'url' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'email' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'ip' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 15),
			'status' => array('type'=>'integer', 'null' => true, 'default' => '1', 'length' => 4),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'post_id' => array('column' => 'post_id', 'unique' => 0))
		);
	var $nb_categories = array(
			'id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 100, 'key' => 'primary'),
			'probability' => array('type'=>'float', 'null' => false, 'default' => '0'),
			'word_count' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 10),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $nb_references = array(
			'id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 100, 'key' => 'primary'),
			'category_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 100, 'key' => 'index'),
			'content' => array('type'=>'text', 'null' => false, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'category_id' => array('column' => 'category_id', 'unique' => 0))
		);
	var $nb_wordfreqs = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'primary'),
			'word' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 100),
			'category_id' => array('type'=>'string', 'null' => false, 'length' => 100),
			'count' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 10),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
	var $posts = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
			'blog_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'author_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
			'status' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 2),
			'title' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'slug' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'body' => array('type'=>'text', 'null' => true, 'default' => NULL),
			'no_comments' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 4),
			'allow_comments' => array('type'=>'boolean', 'null' => false, 'default' => '1'),
			'allow_pingback' => array('type'=>'boolean', 'null' => false, 'default' => '1'),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
}
?>
