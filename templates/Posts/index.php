<?php
    use Cake\Core\Configure;

    $this->set('sidebar', '');
    $this->set('title', _('Posts'));
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
    if ($this->getCurrentUser()->get('lvl') <= LVL_EDITOR) {
        echo '<div class="_actions">';
        printf('<a href="%2$s">%1$s</a>', _('edit'), '/posts/edit/' . $post->id);
        echo ' ' . _('or') . ' ';
        printf('<a href="%2$s" onclick="return confirm();">%1$s</a>', _('delete'), '/posts/delete/' . $post->id);
        echo '</div>';
    }
    printf('<h1><a href="%2$s">%1$s</a></h1>', h($post->title), '/posts/view/' . $post->id);

    // show date of publish and publisher
    printf(_('Published %1$s by %2$s.',
        $post->created,
        sprintf('<a href="%2$s">%1$s</a>', h($post->creator->d_n,), '/profiles/view/' . $post->creator->id)
    ));

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
