<div id="header">
	<div id="header_logo">
		<?php echo $this->Html->link($this->Html->image('family_tree.jpg'), '/', array('escape' => false)); ?>
	</div>
	<h1><?php echo $this->Html->link('FAMIREE', '/'); ?></h1>
	<h2><?php __('own your family tree...'); ?></h2>
</div>
<?php
	if (!empty($Auth)) {
?>
<div id="navigation">
	<div id="user_info" style="float:right">
	<?php
		printf(__('Hello, %s.', true), '<b>' . @$Auth['Profile']['d_n'] . '</b>');
		echo ' ';
		echo $this->Html->link(__('Settings', true), array('admin' => false, 'plugin' => null, 'controller'=>'settings'));
		echo ' ' . __('or', true) . ' ';
		echo $this->Html->link(__('Logout', true), '/logout');
	?>.
	</div>
	<ul>
		<li<?php if ($this->params['controller']=='pages' && $this->params['action']=='dashboard') echo ' class="active"'; ?>>
			<?php echo $this->Html->link(__('Home', true), array(
				'admin' => false, 'plugin' => null, 'controller'=>'pages', 'action'=>'dashboard'));
			?>
		</li>
		<li<?php if ($this->params['controller']=='profiles' && $this->params['action']=='tree') echo ' class="active"'; ?>>
			<?php echo $this->Html->link(__('Tree', true), array('admin' => false, 'plugin' => null, 'controller'=>'profiles', 'action'=>'tree')); ?>
		</li>
		<li<?php if ($this->params['controller']=='attachments' && $this->params['action']=='index') echo ' class="active"'; ?>>
			<?php echo $this->Html->link(__('Photos', true), array('admin' => false, 'plugin' => null, 'controller'=>'attachments', 'action'=>'index')); ?>
		</li>
		<li<?php if ($this->params['controller']=='profiles' && $this->params['action']=='view') echo ' class="active"'; ?>>
			<?php echo $this->Html->link(__('Profiles', true), array('admin' => false, 'plugin' => null, 'controller'=>'profiles', 'action'=>'view')); ?>
		</li>
	</ul>
</div>
<?php
	}
?>