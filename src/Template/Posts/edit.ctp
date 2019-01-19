<?php
    $this->set('sidebar', '');
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

    echo $this->Form->input('title', [
        'label' => __('Title') . ':',
        'class' => 'big',
        'id' => 'PostTitle'
    ]);
    echo $this->Form->input('body', ['label' => __('Body') . ':', 'rows' => 4, 'id' => 'PostBody']);
    echo $this->Html->script('jquery.textarearesizer.min');
    ?>
    </div>
</div>
<div class="panel">
    <div class="legend"><?= __('Additional Properties') ?></div>
    <div class="dropdown">
    <?php
        //echo $this->Form->input('slug', ['label' => __('Slug') . ':', 'id' => 'PostSlug']);
        echo $this->Form->input('created', [
            'label' => __('Created') . ':',
            //'dateFormat' => Configure::read('dateFormat'),
            //'timeFormat' => Configure::read('timeFormat'),
            //'separator' => Configure::read('dateSeparator'),
        ]);

        echo $this->Form->input('creator_id', [
            'type' => 'select',
            'options' => @$authors,
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

        /*$('#PostAuthorTitle').autocomplete({
            url      : '<?php echo $this->Html->url(['controller' => 'Profiles', 'action' => 'autocomplete']); ?>',
            dataType : "text",
            width    : "500px",
            formatResult: function(row) {
                return row[1];
            },
            formatItem: function(data, i, total) {
                return data[1];
            },
            result: function(data, row) {
                $('#PostCreatorId').val(row[0]);
            }
        });*/
    });
</script>
