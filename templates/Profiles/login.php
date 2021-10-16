<?php
    $this->set('title', _('Login'));
    $this->set('sidebar', null);
?>
<div class="form" id="ProfileLogin">
    <span id="YourFather"><?= _('Your Father') ?></span>
    <span id="YourMother"><?= _('Your Mother') ?></span>
<?php
    echo $this->Form->create();
    echo $this->Form->control('u', ['label' => _('Your name')]);
    echo $this->Form->control('p', ['label' => _('Password'), 'type' => 'password']);
    echo $this->Form->control('remember_me', [
        'label' => _('Remember me'),
        'type' => 'checkbox'
    ]);

    echo $this->Form->submit(_('Login'));
    echo $this->Form->end();
    ?>
</div>
