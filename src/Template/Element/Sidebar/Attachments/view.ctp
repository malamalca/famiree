<?php
if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
    ?>
<h1><?= __('Operations') ?></h1>
<ul>
    <li>
    <?php
        echo $this->Html->image('ico_avatar.gif', ['class' => 'icon']);
        echo $this->Html->link(
            __('Point a Person'),
            [
                'controller' => 'Attachments',
                'action' => 'addNote',
                $attachment->id
            ],
            [
                'id' => 'AddNoteLink'
            ]
        );
    ?>
    </li>
    <li><?php
    echo $this->Html->image('ico_profile_edit.gif', ['class' => 'icon']);
    echo $this->Famiree->link(__('[Edit] properties'), ['controller' => 'Attachments', 'action' => 'edit', $attachment->id]);
    ?></li>
</ul>
<div>&nbsp;</div>
    <?php
} // level check
?>
<h1><?= __('Image Properties') ?></h1>
<ul class="label_value">
    <li>
        <span class="label"><?= __('Dimensions') ?>:</span>
        <span class="value"><?= $attachment->width ?><span class="light"> x </span><?= $attachment->height ?><span class="light"> px</span></span>
    </li>
    <li>
        <span class="label"><?= __('Size') ?>:</span>
        <span class="value"><?php echo $this->Number->toReadableSize($attachment->filesize); ?></span>
    </li>
</ul>
