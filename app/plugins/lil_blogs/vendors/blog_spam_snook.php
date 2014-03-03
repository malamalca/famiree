<?php
class BlogSpamSnook extends Object {

    function categorize($c) {
      $score = 0;
      
      App::import('model', 'LilBlogs.Comment');
      $comments =& new Comment();
      
      // check for link frequency
      $count = substr_count ( strtolower($c['body']), 'http://' );
      if($count > 2) $score -= $count;
      if($count < 2) $score += 2;
      if($count == 0 && strlen($c['body'] > 20)) $score += 2;
      if( strlen($c['body'] < 20) ) $score -= 1;

      // check for email frequency
      $score += $comments->findCount( array('Comment.email'=>$c['email'], 'Comment.status'=>2) );
      $score -= $comments->findCount( array('Comment.email'=>$c['email'], 'Comment.status'=>0) );
      $score -= $comments->findCount( array('Comment.ip'=>$c['ip'], 'Comment.status'=>0) );
      // $score -= $comments->findCount( "DATE_ADD(created, INTERVAL 1 day) > now()" );

      // check for banned keyword frequency
      $s = strtolower($c['body'] . $c['author'] . $c['url']); // lowercase it so that string matching is more reliable
      $words = Array('levitra','viagra','casino','plavix','cialis','ativan','fioricet','rape','acyclovir', 'penis','phentermine','porno','pharm','ringtone','pharmacy','url>');
      foreach($words AS $word)
      {
        if( strpos($s, $word) !== false ) $score--;
      }

      // check for bad URL signs
      $s = strtolower($c['url']); // lowercase it so that string matching is more reliable
      $words = Array('.html','.info','?','&','free');
      foreach($words AS $word)
      {
        if( strpos($s, $word) !== false ) $score--;
      }

  	  $score -= preg_match('/\.(de|pl|cn)(\/|$)/', $c['url']) ? 2:0; //check for .de or .pl domains since they tend to spam
  	  $score -= preg_match('/-.*-.*htm$/', $c['url']) ? 2 : 0; // mark this -2 because it's always spam
  	  $score -= ( strlen($c['url']) > 30 ? 1:0 ); //spam urls are on average 38 chars long

      $regexs = Array('/^interesting\r\n$/','/^Interesting...\r\n$/','/^Sorry \:\(\r\n$/','/^Nice(!|\.)*\r\n$/','/^Cool(!|\.)*\r\n$/');
      foreach($regexs AS $regex)
      {
        if( preg_match($regex, $c['body']) ) $score -= 10;
      }

      // check for spam phrases
//      $badphrases = array('Hi! Very nice site! Thanks you very much!');
//      foreach($badphrases AS $phrase)
//      {
//        similar_text($phrase, $c['body'], $percent);
//        if($percent > 50) $score -= round($percent / 10);
//      }

      // check if URL is in author
      $count = substr_count ( strtolower($c['author']), 'http://' );
      $score -= ($count * 2);

      // check if body has been used before
      $score -= $comments->findCount( array("Comment.body"=>$c['body']) );

      // check if we're coming up with nonsense words
      preg_match_all('/[bcdfghjklmnpqrstvwxz]{5}/',strtolower($c['email'].$c['author']),$m);
      $score -= count($m[0]);
      
      return ($score < 0) ? BLOGSPAM_SPAM : (($score == 0) ? BLOGSPAM_UNKNOWN : BLOGSPAM_HAM);
    }
    
   	function untrain($id) {
		return true;
	}
	
	function train($id, $status, $comment) {
		return true;
	}

}
?>
