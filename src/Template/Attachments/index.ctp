<?php
    use Cake\Core\Configure;

    $this->set('sidebar', 'Attachments/index');
    $this->set('title_for_layout', __('Attachments'));
?>
<h1><?= __('Attachments') ?></h1>
<div class="index" id="IndexAttachment">
<?php
    foreach ($attachments as $attachment) {
        echo '<div class="index_attachment">';
        // display image with link to view attachment
        echo $this->Html->link(
            $this->Html->image('thumbs/' . $attachment->id . '.png'), ['action' => 'view', $attachment->id],
            ['escape' => false]
        );
        echo '<div class="_title">';
        echo h($attachment->title);
        echo '&nbsp;</div>';
        echo '</div>'.PHP_EOL;
    }
?>
</div>

<ul class="paginator"><?= $this->Paginator->numbers(['first' => '1']) ?></ul>
