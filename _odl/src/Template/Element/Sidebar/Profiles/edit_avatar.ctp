<div id="ProfileHeadshot">
<?php
    //attachment id
    if (!empty($profile->ta)) {
        echo $this->Html->image([
            'controller' => 'Attachments',
            'action' => 'display',
            $profile->ta,
            'medium'
        ]);
    } else {
        echo $this->Html->image('add_photo_' . $profile->g . '.gif');
    }
?>
</div>
<div id="sidebar_hint">
    <p><?= __('Click on image you wish to set as users new avatar.') ?></p>
    <p>
    <?php
        echo $this->Famiree->link(__('[Click here] if you wish to remove this profile\'s.'), [
            'action' => 'edit_avatar',
            $profile->id,
            'remove'
        ]);
    ?>
    </p>
</div>
