<?php
    use Cake\Core\Configure;

    $this->set('sidebar', '');
    $this->set('title_for_layout', __('Posts'));
?>
<div id="DashBoardPosts">
<?php
foreach ($posts as $post) {
    echo '<div class="dashboard_post">';

    if (!empty($post->profiles[0]->ta)) {
        echo '<div id="ProfileHeadshot">';
        echo $this->Html->image('thumbs/' . $post->profiles[0]->ta . '.png', ['id' => 'SidebarAttachmentPreviewImage']);
        echo '</div>';
    }

    echo '<div class="_header">';

    // admin actions - DELETE, EDIT
    if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
        echo '<div class="_actions">';
        echo $this->Html->link(__('edit'), ['action' => 'edit', $post->id]);
        echo ' ' . __('or') . ' ';
        echo $this->Html->link(__('delete'), ['action' => 'delete', $post->id], [
            'confirm' => __('Are your sure you want to delete this post?')
        ]);
        echo '</div>';
    }
    echo '<h1>' . $this->Html->link($post->title, ['action' => 'view', $post->id]) . '</h1>';

    // show date of publish and publisher
    echo __('Published {0} by {1}.',
        $this->Time->timeAgoInWords($post->created, ['format' => Configure::read('outputDateFormat') . ' HH:mm']),
        $this->Html->link($post->creator->d_n, ['controller' => 'Profiles', 'action' => 'view', $post->creator->id])
    );

    // show profiles to which this post is linked
    $linked_to = [];
    foreach ($post->profiles as $profile) {
        if (empty($profile->d_n)) {
            $linked_to[] = __('Unknown');
        } else {
            $linked_to[] = $this->Html->link($profile->d_n, ['controller' => 'Profiles', 'action' => 'view', $profile->id]);
        }
    }
    if (!empty($linked_to)) {
        echo ' ' . __('Linked to {0}.', $this->Text->toList($linked_to, __('and')));
    }

    echo '</div>';

    echo '<div class="_body">';
    echo $this->Famiree->autop($body = $this->Famiree->excerpt($post->body));
    echo '</div>';

    if ($body != $post->body) {
        echo '<div class="_readmore">';
        echo $this->Html->link(__('Read more...'), ['action' => 'view', $post->id]);
        echo '</div>';
    }

    echo '</div>';
}
?>
<ul class="paginator"><?= $this->Paginator->numbers(['first' => '1']) ?></ul>
</div>
