<?php
    use Cake\Core\Configure;

    $this->set('title_for_layout', __('Import Gedcom file'));
    $this->set('sidebar', '');
?>
<div class="form" id="GedcomImport">
    <h1><?= __('Import Gedcom file') ?></h1>
    <p>
        <?= __('Please upload a Gedcom file.') ?><br />
        <?= __('Profiles from Gedcom will replace entire family tree.') ?><br /><br /></p>
<?php
    echo $this->Form->create(null, ['type' => 'file']);
    echo $this->Form->control('filename', ['type' => 'file', 'label' => __('Gedcom File') . ':']);

    echo '<br />';
    echo $this->Form->submit(__('Import'));
    echo $this->Form->end();
?>
</div>
