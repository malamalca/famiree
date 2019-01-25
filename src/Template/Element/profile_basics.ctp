<fieldset>
<div class="input radio_row">
    <label><?= __('Status') ?>:</label>
    <div class="row"><?= $this->Form->radio('l', ['1' => __('Living'), '0' => __('Deceased')]) ?></div>
</div>
</fieldset>
<fieldset>
<div class="input text">
    <label><?= __('Name') ?>:</label>
    <?php
        echo $this->Form->text('fn', ['placeholder' => __('First')]);
        echo $this->Form->text('mn', []);
        echo $this->Form->text('ln', ['placeholder' => __('Last')]);
    ?>
</div>
<div class="input text">
    <label><?= __('Maiden Name') ?>:</label>
    <?= $this->Form->text('mdn') ?>
</div>
<div class="input radio_row">
    <label><?= __('Gender') ?>:</label>
    <div class="row"><?= $this->Form->radio('g', ['m' => __('Male'), 'f' => __('Female')]) ?></div>
</div>
</fieldset>
<fieldset>
    <?= $this->Form->control('e', ['label' => __('Email').':', 'error' => __('Invalid email format.')]) ?>
</fieldset>
<fieldset>
    <div class="input text">
        <label><?= __('Date of Birth') ?>:</label>
        <?php
            echo $this->Form->text('dob_d', ['type' => 'text', 'size' => 2]);
            echo $this->Form->select('dob_m', $this->Famiree->getMonthNames(), ['empty' => true]);
            echo $this->Form->text('dob_y', ['type' => 'text', 'size' => 4]);
        ?>
    </div>
    <div class="input text">
        <label><?= __('Place of Birth') ?>:</label>
        <?= $this->Form->text('plob') ?>
    </div>
</fieldset>
<fieldset>
    <div class="input text">
        <label><?= __('Place of Living') ?>:</label>
        <?= $this->Form->text('loc') ?>
    </div>
</fieldset>
<fieldset id="EditProfileDeathInfo">
    <div class="input text">
        <label><?= __('Date of Death') ?>:</label>
        <?php
            echo $this->Form->text('dod_d', ['type' => 'text', 'size' => 2]);
            echo $this->Form->select('dod_m', $this->Famiree->getMonthNames(), ['empty' => true]);
            echo $this->Form->text('dod_y', ['type' => 'text', 'size' => 4]);
        ?>
    </div>
    <div class="input text">
        <label><?= __('Place of Death') ?>:</label>
        <?= $this->Form->text('plod') ?>
    </div>
    <div class="input text">
        <label><?= __('Cause of Death') ?>:</label>
        <?= $this->Form->text('cod') ?>
    </div>
    <div class="input text">
        <label><?= __('Place of Burial') ?>:</label>
        <?= $this->Form->text('plobu') ?>
    </div>
</fieldset>
