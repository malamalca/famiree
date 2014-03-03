<div id="SidebarProfileSettings" class="tab_list">
	<ul id="SidebarProfileSettingsMenu">
		<li<?php if ($this->action == 'lang') echo ' class="active"'; ?>>
			<?php echo $this->Quicks->link(__('[Language]', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'lang'), array('id'=>'ProfileSettingsLinkDate')); ?>
		</li>
		<li<?php if ($this->action == 'datetime') echo ' class="active"'; ?>>
			<?php echo $this->Quicks->link(__('[Date and Time] format', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'datetime'), array('id'=>'ProfileSettingsLinkDate')); ?>
		</li>
		<li<?php if ($this->action == 'change_password') echo ' class="active"'; ?>>
			<?php echo $this->Quicks->link(__('Change [Password]', true), array('plugin' => 'lil_users', 'controller' => 'users', 'action' => 'change_password'), array('id'=>'ProfileSettingsLinkPassword')); ?>
		</li>
		<li<?php if (in_array($this->action, array('maintenance'))) echo ' class="active"'; ?>>
			<?php echo $this->Quicks->link(__('[Maintenance]', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'maintenance'), array('id'=>'SettingsMaintenance')); ?>
		</li>
	</ul>
</div>