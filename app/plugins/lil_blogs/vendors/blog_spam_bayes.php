<?php
class BlogSpamBayes {
	
	var $bayes = null;
	
	function BlogSpamBayes() {
		App::import('Vendor', 'LilBlogs.NaiveBayesian');
		App::import('Model', 'LilBlogs.NbCategory');
		$nbs = new NbCategory();
		$this->bayes = new NaiveBayesian($nbs);
	}
	
	function categorize($comment) {
		$result = $this->bayes->categorize($comment['body']);
		asort($result, SORT_NUMERIC);
		$result = array_reverse($result, true);
		$probability = reset($result);
        if (abs($probability - 0.5) < 0.25) {
        	return BLOGSPAM_UNKNOWN;
        } else {
			$cat = reset(array_keys($result));
			$result = array('spam'=>BLOGSPAM_SPAM, 'ham'=> BLOGSPAM_HAM);
			return $result[$cat];
		}
	}
	
	function untrain($id) {
		$result = $this->bayes->untrain($id);
		$this->bayes->updateProbabilities();
		return $result;
		
	}
	
	function train($id, $status, $comment) {
		$result = $this->bayes->train($id, ($status==BLOGSPAM_HAM)?'ham':'spam', $comment['body']);
		$this->bayes->updateProbabilities();
		return $result;
	}
}

?>
