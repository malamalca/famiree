<?php
class Comment extends AppModel {
	var $name = 'Comment';
	var $actsAs = array('Containable');
	var $belongsTo = array('Post' => array(
		'counterCache' => 'no_comments',
		'counterScope' => 'Comment.status = 2'
	));
	var $order = 'created DESC';
	
	var $validate = array(
		'post_id'   => array('rule'=>'checkAllowed', 'required'=>true),
		'body'		=> array('rule'=>array('minLength', '1'), 'required'=>true)
	);
	
	/*function checkAllowed($data) {
		return (boolean)$this->Post->field('allow_comments', array('Post.id'=>$data['post_id']));
	}*/
}
?>
