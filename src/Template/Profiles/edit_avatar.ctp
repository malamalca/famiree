<?php
    $this->set('sidebar', 'Profiles/edit_avatar');
?>
<div class="form" id="EditProfileAvatar">
    <h1><?= h($profile->d_n) . ': ' . __('Select Avatar');
    ?>
    </h1>
    <?php
    foreach ($attachments as $attachment) {
        echo '<div class="index_attachment">';
        // display image with link to view attachment
        echo $this->Html->link(
            $this->Html->image('thumbs/' . $attachment->id . '.png'),
            ['action' => 'edit_avatar', $profile->id, $attachment->id],
            ['escape' => false]
        );
        echo '<div class="_title">';
        echo h($attachment->title);
        echo '</div>';
        echo '</div>';
    }
    ?>
</div>
