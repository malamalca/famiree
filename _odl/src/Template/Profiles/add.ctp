<?php
    use Cake\Utility\Hash;
?>
<div class="form" id="EditProfile">
<?php
    echo $this->Form->create($profile, ['id' => 'ProfileEditForm', 'idPrefix' => 'profile']);
    echo $this->Form->control('id');
    echo $this->Form->control('referer', ['type' => 'hidden']);

    $pageTitle = '';
    switch ($relationship) {
        case 'child':
            $pageTitle = __('Add {0}\'s Child', $baseProfile->d_n);
            break;
        case 'spouse':
            $pageTitle = __('Add {0}\'s Spouse', $baseProfile->d_n);
            break;
        case 'parent':
            $pageTitle = __('Add {0}\'s Parent', $baseProfile->d_n);
            break;
        case 'sibling':
            $pageTitle = __('Add {0}\'s Sibling', $baseProfile->d_n);
            break;
    }
?>
    <div class="tab" id="EditProfileTabBasics">
    <h1><?= h($pageTitle) ?>
    </h1>
        <?php
            if ($relationship == 'child') {
                echo $this->Form->hidden('units.0.kind', ['value' => 'c']);
                echo '<fieldset>';
                echo $this->Form->control('units.0.union_id', [
                    'type' => 'select',
                    'label' => __('Select Other parent') . ':',
                    'empty' => '-- ' . __('create new family') . ' --',
                    'options' => Hash::combine($marriages, '{n}.spouse._matchingData.Units.union_id', '{n}.spouse.d_n')
                ]);
                echo '</fieldset>';
            }
            if ($relationship == 'spouse') {
                $familyChildren = [];
                foreach ($marriages as $marriage) {
                    $familyChildren[$marriage['children'][0]->_matchingData['Units']->union_id] =
                        Hash::reduce($marriage['children'], '{n}.d_n', function($merge, $key) { if ($merge!='') $merge .= ', '; return $merge . $key;});
                }
                echo $this->Form->hidden('units.0.kind', ['value' => 'p']);
                echo '<fieldset>';
                echo $this->Form->control('units.0.union_id', [
                    'type' => 'select',
                    'label' => __('Parent for children') . ':',
                    'empty' => '-- ' . __('create new family') . ' --',
                    'options' => $familyChildren
                ]);
                echo '</fieldset>';
            }

            if ($relationship == 'parent') {
                $unionId = null;
                if (isset($family['parents'][0])) {
                    $unionId = $family['parents'][0]->_matchingData['Units']->union_id;
                } else if (isset($family['siblings'][0])) {
                    $unionId = $family['siblings'][0]->_matchingData['Units']->union_id;
                }

                echo $this->Form->hidden('units.0.kind', ['value' => 'p']);
                echo $this->Form->hidden('units.0.union_id', ['value' => $unionId]);
            }

            if ($relationship == 'sibling') {
                $unionId = null;
                if (isset($siblings[0])) {
                    $unionId = $siblings[0]->_matchingData['Units']->union_id;
                }
                echo $this->Form->hidden('units.0.kind', ['value' => 'c']);
                echo $this->Form->hidden('units.0.union_id', ['value' => $unionId]);
            }
        ?>
        <?= $this->element('profile_basics') ?>
    </div>


<?php
        echo '<div class="input submit">';
        echo $this->Form->button(__('Save'), ['type' => 'submit', 'id' => 'ProfileSubmitButton']);

if ($referer = trim(base64_decode($this->getRequest()->getData('referer')))) {
    echo ' '.__('or').' '.$this->Html->link(__('Cancel'), $referer);
}
    echo '</div>';

       echo $this->Form->end();
?>

<script type="text/javascript">
    $(document).ready(function() {
        <?php if ($profile->l) { ?>
            // hide death info form living people
            $("#EditProfileDeathInfo").hide();
        <?php } ?>

        $("#profile-l-0").click(function(){
            $("#EditProfileDeathInfo").show();
        });
        $("#profile-l-1").click(function(){
            $("#EditProfileDeathInfo").hide();
        });
    });
</script>
