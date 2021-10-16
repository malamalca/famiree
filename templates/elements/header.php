<div id="header">
    <div id="header_logo">
        <a href="<?= $this->url('/') ?>"><img src="<?= $this->url('/img/family_tree.jpg') ?>" alt="logo" /></a>
    </div>
    <h1><a href="<?= $this->url('/') ?>">FAMIREE</a></h1>
    <h2><?= _('own your family tree...') ?></h2>
</div>
<?php
if (1==2) {
    ?>
<div id="navigation">
    <div id="user_info" style="float:right">
    <?php
    printf(__('Hello, {0}.', '<b>' . $this->currentUser->get('d_n') . '</b>'));
    echo ' ';
    echo $this->Html->link(__('Settings'), ['controller' => 'Users', 'action' => 'settings']);
    echo ' ' . __('or') . ' ';
    echo $this->Html->link(__('Logout'), '/logout');
    ?>
    </div>
    <ul>
        <?php
            $controller = $this->getRequest()->getParam('controller');
            $action = $this->getRequest()->getParam('action');
        ?>
        <li<?php if ($controller == 'Pages' && $action == 'dashboard') {
            echo ' class="active"';
           } ?>>
        <?php echo $this->Html->link(__('Home'), '/');
        ?>
        </li>
        <li<?php if ($controller == 'Profiles' && $action == 'tree') {
            echo ' class="active"';
           } ?>>
            <?php echo $this->Html->link(__('Tree'), ['controller' => 'Profiles', 'action' => 'tree']); ?>
        </li>
        <li<?php if ($controller == 'Attachments' && $action == 'index') {
            echo ' class="active"';
           } ?>>
            <?php echo $this->Html->link(__('Photos'), ['controller' => 'Attachments', 'action' => 'index']); ?>
        </li>
        <li<?php if ($controller == 'Posts' && $action == 'index') {
            echo ' class="active"';
           } ?>>
            <?php echo $this->Html->link(__('Memories'), ['controller' => 'Posts', 'action' => 'index']); ?>
        </li>
        <li<?php if ($controller == 'Profiles' && $action == 'view') {
            echo ' class="active"';
           } ?>>
            <?php echo $this->Html->link(__('Profiles'), ['controller' => 'Profiles', 'action' => 'view']); ?>
        </li>
    </ul>
</div>
    <?php
}
?>
