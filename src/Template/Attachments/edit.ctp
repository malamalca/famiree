<?php
    use Cake\Core\Configure;

    $this->set('sidebar', 'Attachments/edit');
?>
<h1>
<?php
    echo __('Attachment');
    echo ': ';
if ($attachment->isNew()) {
    echo __('Add');
} else {
    echo h($attachment->title);
}
?>
</h1>
<div class="form" id="FormAttachment">
<div class="panel">
    <div class="legend"><?= __('Basic Info') ?></div>
    <div class="dropdown">
    <?php
        echo $this->Form->create($attachment, ['type' => 'file', 'id' => 'AttachmentForm']);
        echo $this->Form->control('id', ['type' => 'hidden']);
        echo $this->Form->control('user_id', ['type' => 'hidden', 'default' => $this->currentUser->get('id')]);

        if ($this->getRequest()->getQuery('foreignid')) {
            echo $this->Form->control('attachments_links.0.id', ['type' => 'hidden']);
            echo $this->Form->control('attachments_links.0.attachment_id', ['type' => 'hidden']);
            echo $this->Form->control('attachments_links.0.foreign_id', ['type' => 'hidden', 'default' => $this->getRequest()->getQuery('foreignid')]);
            echo $this->Form->control('attachments_links.0.class', ['type' => 'hidden', 'default' => $this->getRequest()->getQuery('class')]);
        }

        echo $this->Form->control('referer', ['type' => 'hidden', 'default' => base64_encode($this->getRequest()->referer())]);

        echo $this->Form->control('title', [
            'label' => __('Title') . ':',
            'class' => 'big',
            'id' => 'AttachmentTitle'
        ]);
        echo $this->Form->control('filename', [
            'type' => 'file',
            'label' => __('Filename') . ':',
            'id' => 'AttachmentFilename'
        ]);
        echo $this->Form->control('description', [
            'label' => __('Description') . ':',
            'rows' => 4,
            'id' => 'AttachmentDescription'
        ]);
        ?>
    </div>
</div>
<div class="panel">
    <div class="legend"><?= __('Additional Properties') ?></div>
    <div class="dropdown">
    <?php
        echo $this->Form->control('created', [
            'label' => __('Created') . ':',
            //'dateFormat' => Configure::read('dateFormat'),
            //'timeFormat' => Configure::read('timeFormat'),
            //'separator' => Configure::read('dateSeparator'),
            'id' => 'AttachmentCreated'
        ]);
        echo $this->Form->control('creator_id', ['type' => 'hidden']);
        ?>
    </div>
</div>
    <?php
        echo '<div class="input submit">';
        echo $this->Form->button(__('Save'), [
            'type' => 'submit',
            'id' => 'AttachmentSubmitButton'
        ]);

        if ($referer = trim(base64_decode($this->getRequest()->getData('referer')))) {
            echo ' '.__('or', true).' '.$this->Html->link(__('Cancel'), $referer);
        }

        echo '</div>';
        echo $this->Form->end();

        echo $this->Html->script('jquery.textarearesizer.min');
        ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // add resizer to textarea
        $('#AttachmentDescription:not(.processed)').TextAreaResizer();

        $('#AttachmentForm').submit(function(){
            $('#AttachmentSubmitButton').attr('disabled', true);
        });
        $('#AttachmentFilename').change(function(){
            if ($('#AttachmentTitle').val()=='') {
                var fileName = $('#AttachmentFilename').val();
                var extractStart = fileName.lastIndexOf('\\')+1;
                var extractStart2 = fileName.lastIndexOf('/')+1;
                if (extractStart2 > extractStart) extractStart = extractStart2;

                fileName = fileName.substring(extractStart, fileName.length);

                var extractEnd = fileName.lastIndexOf('.');
                if (extractEnd>=0) fileName = fileName.substring(0, extractEnd);

                $('#AttachmentTitle').val(fileName);
            }
        });
    });
</script>
