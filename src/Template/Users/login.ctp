<?php
    $this->set('title_for_layout', __('Login'));
    $this->set('sidebar', null);
?>
<div class="form" id="ProfileLogin">
    <span id="YourFather"><?= __('Your Father') ?></span>
    <span id="YourMother"><?= __('Your Mother') ?></span>
<?php
    echo $this->Form->create(null);
    echo $this->Form->input('u', ['label' => __('Your name')]);
    echo $this->Form->input('p', ['label' => __('Password'), 'type' => 'password']);
    echo $this->Form->input('remember_me', [
        'label' => __('Remember me'),
        'type' => 'checkbox'
    ]);

    echo $this->Form->submit(__('Login'));
    echo $this->Form->end();
    ?>
</div>
