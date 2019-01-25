<div id="SidebarProfileEdit" class="tab_list">
<ul id="SidebarProfileEditMenu">
	<li class="active"><?php echo $this->Html->link(__('Basics'), '#', array('id'=>'EditProfileLinkBasics')); ?></li>
	<li><?php echo $this->Html->link(__('Personal'), '#', array('id'=>'EditProfileLinkPersonal')); ?></li>
	<li><?php echo $this->Html->link(__('Interests'), '#', array('id'=>'EditProfileLinkInterests')); ?></li>
	<li><?php echo $this->Html->link(__('Relationships'), '#', array('id'=>'EditProfileLinkRelationships')); ?></li>
    <?php
        if ($this->currentUser->get('lvl') <= LVL_ADMIN) {
    ?>
    <li><?php echo $this->Html->link(__('Administration'), '#', array('id'=>'EditProfileLinkAdmin')); ?></li>
    <?php
        }
    ?>
</ul>
</div>
