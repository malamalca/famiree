<?php
	foreach ($profiles as $profile) {
		echo $profile['Profile']['id'].'|'.$this->Quicks->profileCaption($profile['Profile']);
		echo chr(10);
	}
?>