<?php
    use Cake\Core\Configure;

    $this->set('sidebar', 'Posts/view');
?>
<div id="PostView">
    <div class="_header">
    <?php
        // admin actions - DELETE, EDIT
    if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
        echo '<div class="_actions">';
        echo $this->Html->link(__('edit'), ['action' => 'edit', $post->id]);
        echo ' ' . __('or') . ' ';
        echo $this->Html->link(
            __('delete'),
            ['action' => 'delete', $post->id],
            ['class' => 'ajax_del_memory', 'id' => 'ProfileMemory' . $post->id],
            __('Are your sure you want to delete this post?')
        );
        echo '</div>';
    }
        echo '<h1>';
        echo $pageTitle = h($post->title);
        $this->set('title_for_layout', $pageTitle);
        echo '</h1>';

        // show date of publish and publisher
        printf(
            __('Published %1$s by %2$s.'),
            $this->Time->timeAgoInWords($post->created, ['format' => Configure::read('outputDateFormat').' HH:mm']),
            $this->Html->link($post->creator->d_n, [
                'controller' => 'Profiles',
                'action' => 'view',
                $post->creator->id
            ])
        );

        // show profiles to which this post is linked
        $linked_to = [];
        foreach ($post->profiles as $profile) {
            if (empty($profile->d_n)) {
                $linked_to[] = __('Unknown');
            } else {
                $linked_to[] = $this->Html->link($profile->d_n, [
                    'controller' => 'Profiles',
                    'action'     => 'view',
                    $profile->id
                ]);
            }
        }
        if (!empty($linked_to)) {
            echo ' ' . __('Linked to') . ' ' . $this->Text->toList($linked_to, __('and')) . '.';
        }
        ?>
    </div>
    <div class="_body">
    <?php
        echo $this->Famiree->autop($post->body);
    ?>
    </div>
</div>
