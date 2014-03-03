<ul id="header_menu"<?php if (@$this->params['admin']) echo ' class="admin"'; ?>>
	<li<?php if (@$this->params['plugin']=='lil_blogs' && (@$this->params['controller']=='blogs' || @$this->params['controller']=='posts' || @$this->params['controller']=='comments'))
		echo ' class="active"'; ?>><?php echo $html->link(__d('lil_blogs', 'Blogs', true), array('plugin'=>'lil_blogs', 'controller'=>'blogs', 'action'=>'index', 'admin'=>true)); ?></li>
	<li<?php if (@$this->params['plugin']=='lil_blogs' && @$this->params['controller']=='authors') echo ' class="active"'; ?>><?php echo $html->link(__d('lil_blogs', 'Authors', true), array('plugin'=>'lil_blogs', 'controller'=>'authors', 'action'=>'index', 'admin'=>true)); ?></li>
</ul>
