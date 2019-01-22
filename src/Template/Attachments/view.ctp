<?php
    use Cake\Core\Configure;
    use Cake\Utility\Text;

    $this->set('sidebar', 'Attachments/view');
?>
<div id="AttachmentDetails">
<?php

    $added_by = __('anonymous');
    if (!empty($attachment->user_id)) {
        $added_by = $attachment->d_n;
        if ($this->currentUser->get('id') == $attachment->user_id) {
            $added_by = __('Me');
        }
        $added_by = $this->Html->link($added_by, ['controller' => 'Profiles', 'action' => 'view', $attachment->user_id]);
    }

    echo '<div>';
    echo __('Added {0} by {1}.',
        $this->Time->timeAgoInWords($attachment->created, ['format' => Configure::read('outputDateFormat').' HH:mm']),
        $added_by
    );
    echo '</div>';

    $belongsTo = [];
    foreach ($attachment->attachments_links as $alink) {
        if (empty($alink->profile)) {
            $belongsTo[] = __('Unknown');
        } else {
            $bName = $alink->profile->d_n;
            if ($this->currentUser->get('id') == $alink->profile->id) {
                $bName = __('Me');
            }
            $belongsTo[] = $this->Html->link($bName, ['controller' => 'Profiles', 'action' => 'view', $alink->profile->id]);
        }
    }

    if (empty($belongsTo)) {
        $belongsTo[] = __('nobody');
    }

    echo '<div>';
    echo __('Belongs to {0}.', $this->Text->toList($belongsTo, __('and')));
    echo '&nbsp;';

    echo $this->Html->link(__('Add link'), '#', ['id' => 'AddAttachmentsLink']);
    echo $this->Form->create(null, ['id' => 'AttachmentsLinkForm', 'url' => ['controller' => 'AttachmentsLinks', 'action' => 'add']]);
    $this->Form->unlockField('foreign_id');
    echo $this->Form->hidden('referer', ['value' => base64_encode($this->Url->build(null, true))]);
    echo $this->Form->hidden('attachment_id', ['value' => $attachment->id]);
    echo $this->Form->hidden('class', ['value' => 'Profile']);
    echo $this->Form->hidden('foreign_id', ['id' => 'AttachmentsLinksForeignId']);
    echo $this->Form->text('profile', ['id' => 'AttachmentsLinksProfile']);
    echo $this->Form->button(__('Add'), ['type' => 'submit']);
    echo __(' or ');
    echo $this->Html->link(__('Cancel'), '#', ['id' => 'AddAttachmentsCancelLink']);
    echo $this->Form->end();


    echo '</div>';
    ?>
</div>
<h1>
<?php
    echo __('Attachment');
    if (!empty($attachment->title)) {
        echo ': ';
        echo h($attachment->title);
    }
?>
</h1>
<div>
<?php
    echo $this->Html->image(
        ['action' => 'display', $attachment->id, 'large', Text::slug($attachment->title) . '.' . strtolower($attachment->ext)],
        ['id' => 'AttachmentImage']
    );
?>
</div>
<?php
if (!empty($attachment->description)) {
    echo '<div>';
    echo h($attachment->description);
    echo '</div>';
}
?>
<div id="NoteForm" class="form">
<?php
    echo $this->Form->Create(null, ['url' => ['controller' => 'Imgnotes', 'action' => 'add']]);
    echo '<fieldset>';
    echo '<legend>' . __('Add Note') . '</legend>';
    echo $this->Form->control('attachment_id', ['type' => 'hidden', 'value' => $attachment->id]);
    echo $this->Form->control('user_id', ['type' => 'hidden', 'value' => $this->currentUser->get('id')]);
    echo $this->Form->control('referer', ['type' => 'hidden', 'value' => base64_encode($this->Url->build(null, true))]);
    echo $this->Form->control('x1', ['type' => 'hidden', 'id' => 'ImgnoteX1']);
    echo $this->Form->control('y1', ['type' => 'hidden', 'id' => 'ImgnoteY1']);
    echo $this->Form->control('width', ['type' => 'hidden', 'id' => 'ImgnoteWidth']);
    echo $this->Form->control('height', ['type' => 'hidden', 'id' => 'ImgnoteHeight']);
    echo $this->Form->control('profile_id', ['type' => 'hidden', 'id' => 'ImgnoteProfileId']);

    $this->Form->unlockField('x1');
    $this->Form->unlockField('y1');
    $this->Form->unlockField('width');
    $this->Form->unlockField('height');
    $this->Form->unlockField('profile_id');

    //echo $this->Form->input('profile_title', array('type'=>'text', 'label'=>__('Profile', true).':'));

    echo '<div class="input text">';
    echo '<label for="Imgnote.note">' . __('Note') . ':</label>';
    echo $this->Form->text('note', ['id' => 'ImgnoteNote']);
    echo $this->Html->image('ico_avatar_check.gif', ['style' => 'display: none;', 'id' => 'ImageAvatarCheck']);
    echo '</div>';

    echo $this->Form->control('crop_to_new', ['type' => 'checkbox', 'label' => __('Crop and create new image')]);

    echo '<div class="input submit">';
    echo $this->Form->button(__('Save'), ['type' => 'submit']);
    echo ' ' . __('or') . ' <span class="link" id="CancelNoteLink">' . __('Cancel') . '</span>';
    echo '</div>';
    echo $this->Form->end();

    echo $this->Html->script('jquery.imgareaselect-0.8.min');
    echo $this->Html->script('jquery.imgnotes-0.2');

    echo $this->Html->script('ui.core');
    echo $this->Html->script('ui.autocomplete');
    echo $this->Html->css('imgnotes');
    echo $this->Html->css('ui.all');
    ?>
</div>
<script type="text/javascript">
    <?php
        echo 'var notes = [';
        $i = 0;
        foreach ($attachment->imgnotes as $imgnote) {
            if ($i++ > 0) {
                echo ',';
            }
            echo '{"x1":"'.$imgnote->x1.'","y1":"'.$imgnote->y1.'","height":"'.$imgnote->height.'","width":"'.$imgnote->width.
                '","note":"'.$imgnote->note.'","id":'.$imgnote->id.
                (!empty($imgnote->profile_id)?',"url":"'.$this->Url->build(['controller' => 'Profiles', 'action' => 'view', $imgnote->profile_id]).'"':'').'}';
        }
        echo '];';
    ?>

    $(document).ready(function() {
        $('#ImgnoteNote').autocomplete({
            url: '<?= $this->Url->build(['controller' => 'Profiles', 'action' => 'autocomplete']) ?>',
            dataType: "text",
            width: "240px",
            formatResult: function(row) {
                $('#ImageAvatarCheck').hide();
                return row[1];
            },
            formatItem: function(data, i, total) {
                return data[1];
            },
            search: function() {
                $('#ImgnoteProfileId').val('');
                $('#ImageAvatarCheck').hide();
            },
            result: function(data, row) {
                console.log(data);
                $('#ImgnoteProfileId').val(row[0]);
                $('#ImageAvatarCheck').show();
            }
        });

        $('#AttachmentImage').imgNotes({
            notes: notes,
            template: '<div class="note"><div class="_actions"><a href="<?= $this->Url->build(['controller' => 'Imgnotes', 'action' => 'delete', '__id__']) ?>">    <?= $this->Html->image('delete.gif') ?></a></div></div>'
        });

        $('#CancelNoteLink').click(function() {
            $('#AttachmentImage').imgAreaSelect({ hide: true });
            $('#NoteForm').hide();
        });

        $('#AddNoteLink').click(function() {
            <?php
                $large_sizes = $attachment->getImageSize('large');
                // do a 10% frame from middle
                $w = round($large_sizes['width'] * .2);
                $x1 = round($large_sizes['width'] / 2 - $w / 2);
                $x2 = $x1 + $w;
                $h = round($large_sizes['height'] * .2);
                $y1 = round($large_sizes['height'] / 2 - $h / 2);
                $y2 = $y1 + $h;

                printf('var frame = {onSelectChange: ShowAddNote, handles: true, x1:%1$s, x2:%2$s, y1:%3$s, y2:%4$s, width:%5$s, height:%6$s};', $x1, $x2, $y1, $y2, $w, $h).PHP_EOL;
            ?>
            ShowAddNote('#AttachmentImage', frame);

            $('#NoteForm').show();
            $('#AttachmentImage').imgAreaSelect(frame);

            return false;
        });

        $('#AddAttachmentsLink').click(function(e) {
            $('#AttachmentsLinkForm').css('display', 'inline');
            $('#AddAttachmentsLink').hide();
            e.preventDefault();
            return false;
        });
        $('#AddAttachmentsCancelLink').click(function(e) {
            $('#AttachmentsLinkForm').css('display', 'none');
            $('#AddAttachmentsLink').show();
            e.preventDefault();
            return false;
        });
        $('#AttachmentsLinksProfile').autocomplete({
            url: '<?= $this->Url->build(['controller' => 'Profiles', 'action' => 'autocomplete']) ?>',
            dataType: "text",
            width: "240px",
            formatResult: function(row) {
                return row[1];
            },
            formatItem: function(data, i, total) {
                return data[1];
            },
            search: function() {
                $('#AttachmentsLinksForeignId').val('');
            },
            result: function(data, row) {
                console.log(data);
                $('#AttachmentsLinksForeignId').val(row[0]);
            }
        });
    });

    function ShowAddNote(img, area) {
        imgOffset = $(img).position();
        form_left  = parseInt(imgOffset.left) + parseInt(area.x1);
        form_top   = parseInt(imgOffset.top) + parseInt(area.y1) + parseInt(area.height)+5;

        $('#NoteForm').css({ left: form_left + 'px', top: form_top + 'px'});

        $('#NoteForm').show();

        $('#NoteForm').css("z-index", 10000);
        $('#ImgnoteX1').val(area.x1);
        $('#ImgnoteY1').val(area.y1);
        $('#ImgnoteHeight').val(area.height);
        $('#ImgnoteWidth').val(area.width);

    }


</script>
