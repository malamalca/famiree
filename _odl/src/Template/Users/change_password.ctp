<?php
    use Cake\Core\Configure;

    $this->set('title_for_layout', __('Set new password for {0}', h($user->name)));
?>
<div class="form" id="ProfileLogin">
    <span id="YourFather"><?= __('Your Father') ?></span>
    <span id="YourMother"><?= __('Your Mother') ?></span>
<?php
    echo $this->Form->create($user);
    echo $this->Form->hidden('id');
    echo $this->Form->hidden('rst', ['value' => '']);
    echo $this->Form->control('p', ['type' => 'password', 'label' => __('New Password') . ':', 'error' => __('Password is required, format must be valid.'), 'value' => '']);
    echo $this->Form->control('repeat_pass', ['type' => 'password', 'label' => __('Repeat Password') . ':', 'error' => __('Passwords do not match.')]);

    echo '<br />';
    echo $this->Form->submit(__('Change'));
    echo $this->Form->end();
