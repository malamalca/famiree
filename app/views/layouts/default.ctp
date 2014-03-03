<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<?php echo $this->Html->charset().PHP_EOL; ?>
	<title><?php 
		echo __('Famiree', true);
		if (!empty($title_for_layout)) {
			echo ' :: ';
			echo $title_for_layout; 
		}
	?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<?php echo $this->Html->css('/lil/css/lil').PHP_EOL; ?>
	
	<?php echo $this->Html->css('header').PHP_EOL; ?>
	<?php echo $this->Html->css('main').PHP_EOL; ?>
	
	<?php echo $this->Html->script('jquery.min').PHP_EOL; ?>
	<?php echo $this->Html->script('geni').PHP_EOL; ?>
	<?php echo $scripts_for_layout.PHP_EOL; ?>
</head>
<body>
	<div id="container">
<?php	echo $this->element('layouts/header'); ?>
		<div id="content">
<?php
	if ($this->Session->check('Message.flash')) echo $this->Session->flash();
	if ($this->Session->check('Message.auth')) echo $this->Session->flash('auth');

	if (isset($sidebar) || (!empty($this->params['plugin']) && (isset($sidebar) && !is_null($sidebar)))) {
?>
			<div id="sidebar">
			<?php					
				if (isset($sidebar)) {
					if (!empty($sidebar)) echo $this->element('sidebar'.DS.$sidebar); 
				} else if (@$this->params['prefix'] == 'admin') {
					echo $this->element('sidebar/admin');
					$sidebar = 'sidebar/admin';
				} else if (!empty($this->params['plugin'])) {
					$sidebar = 'sidebar'.DS.$this->params['plugin'].DS.$this->params['controller'].DS.$this->params['action'];
					echo $this->element($sidebar);
				} 
			?>&nbsp;
			</div>
<?php
	}
?>
			<div id="main"<?php if (isset($sidebar)) echo ' style="margin-left: 230px;"'; ?>>
<?php 			
				echo $content_for_layout;
?>
			</div>
			<div id="footer">
				&copy; Famiree.com 2009 | 
				<?php
					echo $this->Html->link(__('RSS Memories', true), '/memories/feed');
				?>
			</div>
		</div>

	</div>
<?php echo $this->element('sql_dump'); ?>
</body>
</html>