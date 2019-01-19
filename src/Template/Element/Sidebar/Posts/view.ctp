<?php
foreach ($post->profiles as $profile) {
    //attachment id
    if (!empty($profile->ta)) {
        echo '<div id="ProfileHeadshot">';
        echo $this->Html->image(
            ['controller' => 'Attachments', 'action' => 'display', $profile->ta, 'medium'],
            ['id' => 'SidebarAttachmentPreviewImage']
        );
        echo '</div>';
    }
}
