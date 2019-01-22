<?php
    $this->set('sidebar', '');
    $this->set('title_for_layout', __('Edit Post'));
?>
<h1><?= __('Edit Post') ?></h1>
<div class="form" id="FormEditPost">
<div id="FormEditPostMain" class="panel">
    <div class="legend"><?= __('Basic Info') ?></div>
    <div class="dropdown">
<?php
    echo $this->Form->create($post);
    echo $this->Form->hidden('id');
    echo $this->Form->hidden('referer', ['default' => base64_encode($this->getRequest()->referer())]);

    if ($this->getRequest()->getQuery('foreignid')) {
        echo $this->Form->control('posts_links.0.id', ['type' => 'hidden']);
        echo $this->Form->control('posts_links.0.post_id', ['type' => 'hidden']);
        echo $this->Form->control('posts_links.0.foreign_id', ['type' => 'hidden', 'default' => $this->getRequest()->getQuery('foreignid')]);
        echo $this->Form->control('posts_links.0.class', ['type' => 'hidden', 'default' => $this->getRequest()->getQuery('class')]);
    }

    echo $this->Form->control('title', ['label' => __('Title') . ':', 'class' => 'big', 'id' => 'PostTitle']);
    echo $this->Form->control('body', ['label' => __('Body') . ':', 'rows' => 4, 'id' => 'PostBody']);
    echo $this->Html->script('jquery.textarearesizer.min');
    ?>
    </div>
</div>
<div class="panel">
    <div class="legend"><?= __('Additional Properties') ?></div>
    <div class="dropdown">
    <?php
        echo $this->Form->control('created', ['label' => __('Created') . ':']);

        echo $this->Form->control('creator_id', [
            'type' => 'select',
            'options' => $authors,
            'label' => __('Creator') . ':',
            'id' => 'PostCreatorId'
        ]);
        ?>
    </div>
</div>
<?php
    // repeat submit
    echo '<div class="input submit">';
    echo $this->Form->button(__('Save'), ['type' => 'submit']);

    if ($referer = trim(base64_decode($this->getRequest()->getData('referer')))) {
        echo ' ' . __('or') . ' ' . $this->Html->link(__('Cancel'), $referer);
    }
    echo '</div>';

    echo $this->Form->end();
    echo $this->Html->script('ui.core');
    echo $this->Html->script('ui.autocomplete');
    echo $this->Html->css('ui.core');
    echo $this->Html->css('ui.theme');
    echo $this->Html->css('ui.autocomplete');
?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // add resizer to textarea
        $('#PostBody:not(.processed)').TextAreaResizer();
    });
</script>
