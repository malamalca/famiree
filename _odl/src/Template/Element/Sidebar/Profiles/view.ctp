<div id="ProfileHeadshot">
<?php
    //attachment id
if (!empty($profile->ta)) {
    echo $this->Html->image(
        [
            'controller' => 'Attachments',
            'action' => 'display',
            $profile->ta,
            'medium'
        ],
        ['id' => 'SidebarProfileViewAvatar']
    );
    //echo $this->Html->image('thumbs/Profile/'.$profile['Profile']['id'].'/'.$profile['Profile']['ta']);
} else {
    echo $this->Html->image('add_photo_' . $profile->g . '.gif', ['id' => 'SidebarProfileViewAvatar']);
}
?>
</div>
<?php
    if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
?>
    <ul>
        <li><?php
            echo $this->Html->image('ico_family_tree.gif', ['class' => 'icon']);
            echo $this->Famiree->link(__('[Show tree] for this person'), ['controller' => 'Profiles', 'action' => 'tree', $profile->id]);
        ?></li>
        <?php
            if ($this->currentUser->exists()) {
        ?>
        <li><?php
            echo $this->Html->image('ico_profile_edit.gif', ['class' => 'icon']);
            echo $this->Famiree->link(__('[Edit] person\'s data'), ['controller' => 'Profiles', 'action' => 'edit', $profile->id]);
        ?></li>
        <li><?php
            echo $this->Html->image('ico_avatar.gif', ['class' => 'icon']);
            echo $this->Famiree->link(__('[Change] person\'s avatar'), ['controller' => 'Profiles', 'action' => 'edit_avatar', $profile->id]);
        ?></li>
        <li><?php
            echo $this->Html->image('ico_reorder.gif', ['class' => 'icon']);
            echo $this->Famiree->link(__('[Reorder] children'), ['controller' => 'Profiles', 'action' => 'reorder_children', $profile->id]);
        ?></li>
        <li><?php
            if (empty($family['children'])) {
                echo $this->Html->image('ico_delete.png', ['class' => 'icon']);
                echo $this->Famiree->link(__('[Delete] profile'), ['controller' => 'Profiles', 'action' => 'delete', $profile->id], null, __('Are you sure you want to delete profile?'));
            }
        ?></li>
        <?php
            }
        ?>
    </ul>
<br />
<?php
    } // level check
?>
<?php
    if (!empty($profile->h_c) || !empty($profile->e_c) || !empty($profile->n_n) || !empty($profile->job) || !empty($profile->edu)) {
?>
<div class="panel">
    <div class="inner">
    <div class="legend"><?= __('Personal') ?></div>
    <ul class="label_value">
    <?php
        if (!empty($profile->h_c)) {
    ?>
        <li>
            <span class="label"><?= __('Hair Color') ?>:</span>
            <span class="value"><?= $this->Famiree->hairColor($profile->h_c) ?></span>
        </li>
    <?php
        }
        if (!empty($profile->e_c)) {
    ?>
        <li>
            <span class="label"><?= __('Eye Color') ?>:</span>
            <span class="value"><?= $this->Famiree->eyeColor($profile->e_c) ?></span>
        </li>
    <?php
        }
        if (!empty($profile->n_n)) {
    ?>
        <li>
            <span class="label"><?= __('Nick Names') ?>:</span>
            <span class="value"><?= h($profile->n_n); ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->edu)) {
    ?>
        <li>
            <span class="label"><?= __('Education') ?>:</span>
            <span class="value"><?= h($profile->edu); ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->job)) {
    ?>
        <li>
            <span class="label"><?= __('Job') ?>:</span>
            <span class="value"><?= h($profile->job); ?>&nbsp;</span>
        </li>
    <?php
        }
    ?>
    </ul>
    </div>
</div>
<?php
}

// display interests panel
if (!empty($profile->in_i) ||
    !empty($profile->in_a) ||
    !empty($profile->in_p) ||
    !empty($profile->in_c) ||
    !empty($profile->in_q) ||
    !empty($profile->in_m) ||
    !empty($profile->in_tv) ||
    !empty($profile->in_mu) ||
    !empty($profile->in_b) ||
    !empty($profile->in_s)
) {
?>
<div class="panel">
    <div class="inner">
    <div class="legend"><?= __('Interests') ?></div>
    <ul class="label_value">
    <?php
        if (!empty($profile->in_i)) {
    ?>
        <li>
            <span class="label"><?= __('Interests') ?>:</span>
            <span class="value"><?= h($profile->in_i) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->in_a)) {
    ?>
        <li>
            <span class="label"><?= __('Activities') ?>:</span>
            <span class="value"><?= h($profile->in_a) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->in_p)) {
    ?>
        <li>
            <span class="label"><?= __('People/Heroes') ?>:</span>
            <span class="value"><?= h($profile->in_p) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->in_c)) {
    ?>
        <li>
            <span class="label"><?= __('Cuisines') ?>:</span>
            <span class="value"><?= h($profile->in_c) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->in_q)) {
    ?>
        <li>
            <span class="label"><?= __('Quotes') ?>:</span>
            <span class="value"><?= g($profile->in_q) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->n_m)) {
    ?>
        <li>
            <span class="label"><?= __('Movies') ?>:</span>
            <span class="value"><?= h($profile->in_m) ?>&nbsp;</span>
        </li>
            <?php
    }
    if (!empty($profile->in_tv)) {
        ?>
        <li>
            <span class="label"><?= __('TV Shows') ?>:</span>
            <span class="value"><?= h($profile->in_tv) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->in_mu)) {
    ?>
        <li>
            <span class="label"><?= __('Music') ?>:</span>
            <span class="value"><?= h($profile->in_mu) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->in_b)) {
    ?>
        <li>
            <span class="label"><?php __('Books') ?>:</span>
            <span class="value"><?php h($profile->in_b) ?>&nbsp;</span>
        </li>
    <?php
        }
        if (!empty($profile->in_s)) {
    ?>
        <li>
            <span class="label"><?= __('Sports') ?>:</span>
            <span class="value"><?= h($profile->in_s) ?>&nbsp;</span>
        </li>
    <?php
        }
    ?>
    </ul>
    </div>
</div>
<?php
}
?>
