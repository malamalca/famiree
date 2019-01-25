<?php
    use Cake\Utility\Hash;

    $this->set('sidebar', '');
?>
<div class="form" id="EditProfile">
<?php
    echo $this->Form->create($user, ['id' => 'ProfileEditForm', 'idPrefix' => 'profile']);
    echo $this->Form->control('id');
    echo $this->Form->control('referer', ['type' => 'hidden']);
?>
    <div class="tab" id="EditProfileTabBasics">
    <h1><?= __('Settings') ?></h1>
        <fieldset>
        <?php
            echo $this->Form->control('u', ['label' => __('Username') . ':', 'error' => __('Invalid username.')]);
            echo $this->Form->control('e', ['label' => __('Email') . ':', 'error' => __('Invalid email.')]);
        ?>
        </fieldset>
        <fieldset>
        <?php
            echo $this->Form->control('old_pass', ['type' => 'password', 'label' => __('Old Password') . ':', 'value' => '', 'error' => __('Invalid current password.')]);
            echo $this->Form->control('p', ['type' => 'password', 'label' => __('New Password') . ':', 'value' => '', 'error' => __('Password is required, format must be valid.'), 'value' => '']);
            echo $this->Form->control('repeat_pass', ['type' => 'password', 'label' => __('Repeat Password') . ':', 'value' => '', 'error' => __('Passwords do not match.')]);
        ?>
        </fieldset>
    </div>


<?php
        echo '<div class="input submit">';
        echo $this->Form->button(__('Save'), ['type' => 'submit', 'id' => 'ProfileSubmitButton']);
        if ($referer = trim(base64_decode($this->getRequest()->getData('referer')))) {
            echo ' '.__('or').' '.$this->Html->link(__('Cancel'), $referer);
        }
        echo '</div>';

       echo $this->Form->end();
?>
