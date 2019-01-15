<?php
if (isset($attachment)) {
?>
    <div id="SidebarAttachmentPreview">
        <?php
        echo $this->Html->image('thumbs/' . $attachment->id . '.png', [
            'id' => 'SidebarAttachmentPreviewImage'
        ]);
        ?>
    </div>
<?php
}
?>
