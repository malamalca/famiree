<?php
    $this->set('title_for_layout', __('Login'));
    $this->set('sidebar', null);
?>
<div class="form" id="ProfileLogin">
    <span id="YourFather"><?= __('Your Father') ?></span>
    <span id="YourMother"><?= __('Your Mother') ?></span>
<?php
    echo $this->Form->create(null);
    echo $this->Form->control('u', ['label' => __('Your name')]);
    echo $this->Form->control('p', ['label' => __('Password'), 'type' => 'password']);
    echo $this->Form->control('remember_me', [
        'label' => __('Remember me'),
        'type' => 'checkbox'
    ]);

    echo $this->Form->submit(__('Login'));
    echo $this->Form->end();
    ?>
</div>
