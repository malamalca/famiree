<?php
    use Cake\Core\Configure;

    $this->set('title_for_layout', __('Reset Email'));
?>
<div class="form" id="ProfileLogin">
    <span id="YourFather"><?= __('Your Father') ?></span>
    <span id="YourMother"><?= __('Your Mother') ?></span>
<?php
    echo $this->Form->create(null);
    echo $this->Form->control('email', ['label' => __('Email') . ':']);

    echo $this->Form->submit(__('Request new Password'));
    echo $this->Form->end();
