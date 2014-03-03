<?php
class BlogSpamNone extends Object {

    function categorize($c) {
      return BLOGSPAM_HAM;
    }
    
   	function untrain($id) {
		return true;
	}
	
	function train($id, $status, $comment) {
		return true;
	}

}
?>