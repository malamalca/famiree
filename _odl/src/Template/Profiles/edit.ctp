<?php
    $this->set('sidebar', 'Profiles/edit');
?>
<div class="form" id="EditProfile">
<?php
        echo $this->Form->create($profile, ['id' => 'ProfileEditForm', 'idPrefix' => 'profile']);
        echo $this->Form->control('id');
        echo $this->Form->control('referer', ['type' => 'hidden', 'default' => base64_encode($this->getRequest()->referer())]);
?>
    <div class="tab" id="EditProfileTabBasics">
    <h1><?php
        echo h($profile->d_n) . ': ' . __('Basics');
    ?>
    </h1>
        <?= $this->element('profile_basics') ?>
    </div>


    <div class="tab" id="EditProfileTabPersonal">
    <h1><?= h($profile->d_n) . ': ' . __('Personal info') ?></h1>
    <fieldset>
        <?php
            echo $this->Form->control('h_c', ['type' => 'select', 'label' => __('Hair Color').':',
                'options' => $this->Famiree->hairColors, 'empty' => true]);
            echo $this->Form->control('e_c', ['type' => 'select', 'label' => __('Eye Color').':',
                'options' => $this->Famiree->eyeColors, 'empty' => true]);
            echo $this->Form->control('n_n', ['type' => 'text', 'label' => __('Nick Names').':']);
            echo $this->Form->control('edu', ['type' => 'text', 'label' => __('Education').':']);
            echo $this->Form->control('job', ['type' => 'text', 'label' => __('Job').':']);
        ?>
    </fieldset>
    </div>


    <div class="tab" id="EditProfileTabInterests">
    <h1><?= h($profile->d_n) . ': ' . __('Interests') ?></h1>
    <fieldset>
        <?php
            echo $this->Form->control('in_i', ['type' => 'text', 'label' => __('Interests').':']);
            echo $this->Form->control('in_a', ['type' => 'text', 'label' => __('Activities').':']);
            echo $this->Form->control('in_p', ['type' => 'text', 'label' => __('People/Heroes').':']);
            echo $this->Form->control('in_c', ['type' => 'text', 'label' => __('Cuisines').':']);
            echo $this->Form->control('in_q', ['type' => 'text', 'label' => __('Quotes').':']);
            echo $this->Form->control('in_m', ['type' => 'text', 'label' => __('Movies').':']);
            echo $this->Form->control('in_tv', ['type' => 'text', 'label' => __('TV Shows').':']);
            echo $this->Form->control('in_mu', ['type' => 'text', 'label' => __('Music').':']);
            echo $this->Form->control('in_b', ['type' => 'text', 'label' => __('Books').':']);
            echo $this->Form->control('in_s', ['type' => 'text', 'label' => __('Sports').':']);
        ?>
    </fieldset>
    </div>

    <div class="tab" id="EditProfileTabRelationships">
    <h1><?= h($profile->d_n) . ': ' . __('Relationships') ?></h1>
    <fieldset>
    <?php
    if ($profile->g == 'm') {
        $marriage_type = [
            't' => __('Wife'),
            'f' => __('Fiancee'),
            'p' => __('Partner'),
            'd' => __('Ex-wife (deceased)'),
            'e' => __('Ex-wife'),
        ];
        $marriage_string = __('%s is his');
    } else {
        $marriage_type = [
            't' => __('Husband'),
            'f' => __('Fiancee'),
            'p' => __('Partner'),
            'd' => __('Ex-husband (deceased)'),
            'e' => __('Ex-husband'),
        ];
        $marriage_string = __('%s is her');
    }

    if ($this->currentUser->exists()) {
        $marriage_string = __('%s is my');
    }

    foreach ($profile->marriages as $i => $marriage) {
        if ($i > 0) {
            echo '<hr />';
        }

        $spouse = $marriage->profiles[0]->id == $profile->id ? $marriage->profiles[1] : $marriage->profiles[0];
        $spouse_string = '';
        if (!empty($spouse->mdn) && $spouse->mdn != $spouse->ln) {
            $spouse_string = sprintf(' <span class="small">(' . __('born %s') . ')</span>', $spouse->mdn);
        }
        $spouse_string = $this->Html->link($spouse->d_n, ['action' => 'view', $spouse->id]) . $spouse_string;

        echo $this->Form->control('marriages.'.$i.'.id', ['type' => 'hidden']);
        ?>
            <div class="input text">
                <label><?= __('Partner') ?>:</label>
                <div class="row">
                    <?= sprintf($marriage_string, $spouse_string) ?>
                    <?= $this->Form->select('marriages.'.$i.'.t', $marriage_type) ?>
                </div>
            </div>
            <div class="input text">
                <label><?= __('Married On') ?>:</label>
                <?php
                    echo $this->Form->text('marriages.'.$i.'.dom_d', ['type' => 'text', 'size' => 2]);
                    echo $this->Form->select('marriages.'.$i.'.dom_m', $this->Famiree->getMonthNames(), ['empty' => true]);
                    echo $this->Form->text('marriages.'.$i.'.dom_y', ['type' => 'text', 'size' => 4]);
                ?>
            </div>
            <?php
            echo $this->Form->control('marriages.'.$i.'.loc', ['type' => 'text', 'label' => __('Married In').':']);
    }
    ?>
    </fieldset>
    </div>

    <?php
        if ($this->currentUser->get('lvl') <= LVL_ADMIN) {
    ?>
    <div class="tab" id="EditProfileTabAdmin">
    <h1><?= h($profile->d_n) . ': ' . __('Administration') ?></h1>
    <fieldset>
        <?php
            echo $this->Form->control('u', ['label' => __('Username') . ':', 'error' => __('Invalid username.')]);
        ?>
        <?php
             echo $this->Form->control('p', ['type' => 'password', 'label' => __('Password') . ':', 'value' => '', 'error' => __('Password is required, format must be valid.'), 'value' => '']);
        ?>
    </fieldset>
    <fieldset>
        <?php
            $privileges = [
                LVL_VIEWER => __('Viewer'),
                LVL_EDITOR => __('Editor'),
                LVL_ADMIN => __('Admin'),
            ];
            if ($this->currentUser->get('lvl') <= LVL_ROOT) {
                $privileges[LVL_ROOT] = __('Root');
            }
            echo $this->Form->control('lvl', ['label' => __('Privileges') . ':', 'type' => 'select', 'options' => $privileges]);
        ?>
    </fieldset>
    </div>
    <?php
        }
    ?>

<?php
        echo '<div class="input submit">';
        echo $this->Form->button(__('Save'), ['type' => 'submit', 'id' => 'ProfileSubmitButton']);

if ($referer = trim(base64_decode($this->getRequest()->getData('referer')))) {
    echo ' ' . __('or') . ' ' . $this->Html->link(__('Cancel'), $referer);
}
        echo '</div>';

        echo $this->Form->end();
?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#EditProfileTabPersonal").hide();
            $("#EditProfileTabInterests").hide();
            $("#EditProfileTabRelationships").hide();
            $("#EditProfileTabAdmin").hide();

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


            $("#EditProfileLinkBasics").click(function(){
                $("#SidebarProfileEditMenu li.active").removeClass("active");
                $("#EditProfileLinkBasics").parent().addClass("active");
                $(".tab").hide();
                $("#EditProfileTabBasics").show();
                return false;
            });

            $("#EditProfileLinkPersonal").click(function(){
                $("#SidebarProfileEditMenu li.active").removeClass("active");
                $("#EditProfileLinkPersonal").parent().addClass("active");
                $(".tab").hide();
                $("#EditProfileTabPersonal").show();
                return false;
            });

            $("#EditProfileLinkInterests").click(function(){
                $("#SidebarProfileEditMenu li.active").removeClass("active");
                $("#EditProfileLinkInterests").parent().addClass("active");
                $(".tab").hide();
                $("#EditProfileTabInterests").show();
                return false;
            });

            $("#EditProfileLinkRelationships").click(function(){
                $("#SidebarProfileEditMenu li.active").removeClass("active");
                $("#EditProfileLinkRelationships").parent().addClass("active");
                $(".tab").hide();
                $("#EditProfileTabRelationships").show();
                return false;
            });

            $("#EditProfileLinkAdmin").click(function(){
                $("#SidebarProfileEditMenu li.active").removeClass("active");
                $("#EditProfileLinkAdmin").parent().addClass("active");
                $(".tab").hide();
                $("#EditProfileTabAdmin").show();
                return false;
            });

            $('#ProfileEditForm').submit(function(){
                $('#ProfileSubmitButton').attr('disabled');
            });
        });
    </script>
</div>
