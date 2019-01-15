<?php
    use Cake\Core\Configure;
?>
<div class="panel">
    <div class="inner">
    <div class="legend"><?= __('Statistics') ?></div>
    <div class="center">
    <?php
        echo '<div id="SidebarStatsCntF">';
        echo '<span>' . $counts['f'] . '</span>';
        echo __('No. of Females', true);
        echo '</div>';

        echo '<div id="SidebarStatsCntM">';
        echo '<span>' . $counts['m'] . '</span>';
        echo __('No. of Males', true);
        echo '</div>';

        echo '<div id="SidebarStatsCntTotal">';
        printf('Total no. of people: %d', $counts['m'] + $counts['f']);
        echo '</div>';
    ?>
    </div>
    </div>
</div>

<div class="panel">
    <div class="inner">
    <div class="legend"><?= __('Memories') ?></div>
    <ul>
    <?php
        if (!empty($posts)) {
            foreach ($posts as $post) {
                echo '<li>';

                $linked_to = [];
                foreach ($post->profiles as $post_link) {
                    $linked_to[] = h($post_link->d_n);
                }
                if (empty($linked_to)) {
                    $linked_to[] = __('my past');
                }

                printf(
                __('About %1$s written by %2$s %3$s'),
                    $this->Html->link(
                    $this->Text->toList($linked_to, __('and')), ['controller' => 'Posts', 'action' => 'view', $post->id]),
                    $this->Html->link($post->creator->d_n, ['controller' => 'Profiles', 'action' => 'view', $post->creator->id]),
                    '<span class="light">(' . $this->Time->timeAgoInWords(
                        $post->created,
                        ['accuracy' => 'day', 'format' => Configure::read('outputDateFormat')]
                    ).')</span>'
                );
                echo '</li>';
            }
        }
    ?>
        <li class="right"><?= $this->Html->link(__('All Posts'), ['controller' => 'Posts', 'action' => 'index']) ?></li>
    </ul>
    </div>
</div>

<div class="panel">
    <div class="inner">
    <div class="legend"><?= __('Log') ?></div>
    <ul>
        <?php
            foreach ($logs as $log) {
                echo '<li>';
                switch ($log->class) {
                    case 'Profile':
                        if (in_array($log->action, ['add', 'edit'])) {
                            if ($log->foreign_id == $this->currentUser->get('id') && $log->user_id == $this->currentUser->get('id')) {
                                echo __('I\'ve edited my own profile.');
                            } elseif ($log->foreign_id == $this->currentUser->get('id')) {
                                echo __('{0} has edited my profile.',
                                    $this->Html->link($log->user->d_n, ['controller' => 'Profiles', 'action' => 'view', $log->foreign_id])
                                );
                            } elseif ($log->foreign_id == $log->user_id) {
                                echo __('{0} has edited his own profile.',
                                    $this->Html->link($log->user->d_n, ['controller' => 'Profiles', 'action' => 'view', $log->foreign_id])
                                );
                            } else {
                                if ($log->action == 'add') {
                                    $message = __('%1$s has been added by %2$s');
                                } else {
                                    $message = __('%1$s has been edited by %2$s');
                                }

                                $userTitle = __('Unknown');
                                printf(
                                    $message,
                                    $this->Html->link($log->title, ['controller' => 'Profiles', 'action' => 'view', $log->foreign_id]),
                                    empty($log->user_id) ? __('Guest') :
                                        $this->Html->link(
                                            ($log->user_id == $this->currentUser->get('id')) ? __('Me') : $log->user->d_n,
                                            ['controller' => 'Profiles', 'action' => 'view', $log->user_id]
                                        )
                                );
                            }
                        } elseif ($log->action == 'delete') {
                            printf(
                                __('Profile "%1$s" has been deleted by %2$s'),
                                $log->title,
                                $this->Html->link(
                                    ($log->user_id == $this->currentUser->get('id')) ? __('Me') : $log->user->d_n,
                                    ['controller' => 'Profiles', 'action' => 'view', $log->user_id]
                                )
                            );
                        }
                        break;
                    case 'Post':
                        if ($log->action == 'delete') {
                            printf(
                                __('Post "%1$s" has been deleted by %2$s.'),
                                $log->title,
                                $this->Html->link(($log->user_id == $this->currentUser->get('id')) ? __('Me') : $log->user->d_n,
                                    ['controller' => 'Profiles', 'action' => 'view', $log->user_id]
                                )
                            );
                        } else {
                            if ($log->action == 'add') {
                                $message = __('Post "%1$s" has been added by %2$s');
                            } else {
                                $message = __('Post "%1$s" has been edited by %2$s');
                            }
                            printf(
                                $message,
                                (
                                    !empty($log->post) ?
                                    $this->Html->link($log->title, ['controller' => 'Posts', 'action' => 'view', $log->foreign_id]) : $log->title
                                ),
                                $this->Html->link(
                                    ($post->user_id == $this->currentUser->get('id')) ? __('Me') : $log->user->d_n,
                                    ['controller' => 'Profiles', 'action' => 'view', $log->user_id])
                            );
                        }
                        break;
                    case 'Attachment':
                        if ($log->action == 'add') {
                            $message = __('Attachment "%1$s" has been added by %2$s');
                        } else {
                            $message = __('Attachment "%1$s" has been edited by %2$s');
                        }
                        printf(
                            $message,
                            empty($log->attachment) ? __('Unknown') :
                            $this->Html->link($log->title, ['controller' => 'Attachments', 'action' => 'view', $log->foreign_id]),
                            empty($log->user_id) ? __('Unknown') :
                                $this->Html->link(
                                    $log->user_id == $this->currentUser->get('id') ? __('Me') : $log->user->d_n,
                                    ['controller' => 'Profiles', 'action' => 'view', $log->user_id]
                                )
                        );
                        break;
                    case 'ImgNote':
                        if ($log->action == 'add' && !empty($log->imgnote)) {
                            printf(
                                __('A note has been added to %1$s by %2$s.'),
                                $this->Html->link(__('image'), ['controller' => 'Attachments', 'action' => 'view', $log->imgnote->attachment_id]),
                                $this->Html->link(
                                    $log->user_id == $this->currentUser->get('id') ? __('Me') : $log->user->d_n,
                                    ['controller' => 'Profiles', 'action' => 'view', $log->user_id]
                                )
                            );
                        }
                        break;
                }
                echo ' <span class="light">';
                echo '('.$this->Time->timeAgoInWords($log->created, ['accuracy' => 'day', 'format' => Configure::read('outputDateFormat')]).')';
                echo '</span>';
                echo '</li>';
            }
        ?>
    </ul>
    </div>
</div>
