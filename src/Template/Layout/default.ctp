<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset().PHP_EOL; ?>
	<title><?php
		echo __('Famiree');
		if (!empty($title_for_layout)) {
			echo ' :: ';
			echo $title_for_layout;
		}
	?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />

	<?php echo $this->Html->css('header').PHP_EOL; ?>
	<?php echo $this->Html->css('main').PHP_EOL; ?>

	<?php echo $this->Html->script('jquery.min').PHP_EOL; ?>
	<?= $this->fetch('script') ?>
</head>
<body>
	<div id="container">
<?php	echo $this->element('header'); ?>
		<div id="content">
	        <?= $this->Flash->render() ?>

<?php
	if (isset($sidebar)) {
        if (!empty($sidebar)) {
?>
			<div id="sidebar"><?= $this->element('Sidebar' . DS . $sidebar) ?>&nbsp;</div>
<?php
        }
	}
?>
			<div id="main"<?php if (isset($sidebar)) echo ' style="margin-left: 230px;"'; ?>>
                <?= $this->fetch('content') ?>
			</div>
			<div id="footer">
				&copy; Famiree.com 2009-2019
			</div>
		</div>

	</div>
</body>
</html>
