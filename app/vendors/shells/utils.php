<?php
class UtilsShell extends Shell {
	var $uses = array('Attachment', 'Profile');
	
	function initialize () {
		App::import('model', 'Attachment');
		$this->Attachment = new Attachment();
	}
	
	function main() {
		if (empty($this->args)) {
			$this->help();
		} else {
			$action = low($this->args[0]);
			if ($action=='regen_thumbs') {
				$this->regen_thumbs();
			} else if (!method_exists($this, $action)) {
				$this->help();
			} else {
				$this->{$action}();
			}
		}
	}
	
	function regen_thumbs() {
		require_once 'wideimage/WideImage.inc.php';
		
		$folders = scandir(APP.'uploads');
		
		foreach ($folders as $fldr) {
			var_dump($fldr);
			$imageName = APP.'uploads'.DS.$fldr.DS.'original';
			if (!file_exists($imageName)) {
				$imageName = APP.'uploads'.DS.$fldr.DS.'large';
			}
			if (!empty($fldr) && substr($fldr, 0, 1)!='.' && file_exists($imageName) ) {
				$data = file_get_contents($imageName);
				$image = wiImage::load($data);
				
				$thumb = $image->resize(75, 75, 'outside');
				
				if ($thumb->getWidth() > $thumb->getHeight()) {
					$thumb = $thumb->crop(floor(($thumb->getWidth()-75)/2), 0, 75, 75);
				} else if ($thumb->getWidth() < $thumb->getHeight()) {
					$thumb = $thumb->crop(0, floor(($thumb->getHeight()-75)/2), 75, 75);
				}
				$thumb->saveToFile(IMAGES.'thumbs'.DS.$fldr.'.png', null, 9);
			}
		}
	}
	
	function rebuild_index() {
		App::import('model', 'Profile');
		$Profile = new Profile();
		
		$data = $Profile->find('all', array('fields' => array('id'), 'contain' => array()));
		foreach ($data as $profile) {
			$Profile->lilSearchIndex($profile['Profile']['id']);
		}
		$Profile->lilSearchOptimize();
	}
	
	function uuid() {
		App::import('Core', 'String');
		$this->out(String::uuid());
	}
	
	function help() {
		$head  = "Usage: cake utils [<action>]\n";
		$head .= "---------------------------------------------------------------\n";
		$head .= "Actions:";

		$commands = array(
			'uuid' =>
				"\t'uuid' - Create new uuid.",
			'regen_thumbs' =>
				"\t'regen_thumbs' - Regenerate thumbnails from original.",
			'rebuild_index' =>
				"\t'rebuild_index' - Regenerate Lucene index.",
		);

		$this->out($head);
		if (!isset($this->args[1])) {
			foreach ($commands as $cmd) {
				$this->out("{$cmd}");
			}
		}
	}
}
?>