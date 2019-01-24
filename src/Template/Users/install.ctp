<?php
    use Cake\Core\Configure;

    $this->set('title_for_layout', __('Add First User'));
    $this->set('sidebar', '');
?>
<div class="form" id="ProfileInstall">
    <h1><?= __('Welcome to Famiree') ?></h1>
    <p>
        <?= __('Please enter user data for a first Famiree profile.') ?><br />
        <?= __('This user will also be a first site admin with highest privileges.') ?><br /><br /></p>
<?php
    echo $this->Form->create(null);
    echo $this->Form->hidden('lvl', ['value' => LVL_ROOT]);
    echo $this->Form->hidden('l', ['value' => true]);
    echo $this->Form->control('fn', ['label' => __('First Name') . ':']);
    echo $this->Form->control('ln', ['label' => __('Last Name') . ':']);
    echo $this->Form->control('e', ['label' => __('Email') . ':']);

    echo '<br />';
    echo $this->Form->control('u', ['label' => __('Username') . ':']);
    echo $this->Form->control('p', ['type' => 'password', 'label' => __('Password') . ':']);
    echo $this->Form->control('repeat_pass', ['type' => 'password', 'label' => __('Repeat Password') . ':']);

    echo '<br />';
    echo $this->Form->submit(__('Save'));
    echo $this->Form->end();
?>
</div>
