<?php
    // converted to cakephp 1.3
    use Cake\Core\Configure;
    use Cake\I18n\Date;

?>
<h1><?php
    echo $title_for_layout = h($profile->d_n);
    $this->set(compact('title_for_layout'));

if (!empty($profile->mdn) &&
        ($profile->mdn != $profile->ln)) {
    echo ' ('.h($profile->mdn).')';
}

    $this->set('sidebar', 'Profiles/view');
?>
</h1>
<div>
    <div id="ProfileInfoPanel">
        <ul class="label_value">
            <?php
            if (!empty($profile->loc)) {
                ?>
            <li class="hr">
                <span class="label"><?= __('Location') ?>: </span>
                <span class="value"><?= h($profile->loc) ?></span>
            </li>
                <?php
            }
            ?>
            <li>
                <span class="label"><?= __('Date of Birth') ?>: </span>
                <span class="value"><?php

                if (@checkdate($profile->dob_m, $profile->dob_d, $profile->dob_y)) {
                    $dob = $profile->dob_y.'-'.$profile->dob_m.'-'.$profile->dob_d;
                    echo (new Date($dob))->i18nFormat(Configure::read('outputDateFormat'));

                    // display age
                    echo ' (';
                    if (!$profile->l) {
                        echo __('would be').' ';
                    }
                    echo $this->Famiree->age($dob) . ' ' . __('years old') . ')';
                } elseif (!empty($profile->dob_y)) {
                    echo $profile->dob_y;
                } else {
                    echo '<span class="n_a">' . __('unknown') . '</span>';
                }
                ?></span>
            </li>
            <?php
            if (!empty($profile->plob)) {
                ?>
            <li>
                <span class="label"><?= __('Place of Birth') ?>: </span>
                <span class="value"><?= h($profile->plob) ?></span>
            </li>
                <?php
            }
            ?>

            <?php
            if ($profile->l != 1) {
                ?>
            <li style="border-top: solid 1px #c0c0c0;">
                <span class="label"><?= __('Status') ?>: </span>
                <span class="value"><?= __('Deceased') ?></span>
            </li>
            <li>
                <span class="label"><?= __('Date of Death') ?>: </span>
                <span class="value"><?php
                if (@checkdate($profile->dod_m, $profile->dod_d, $profile->dod_y)) {
                    $dod = $profile->dod_y.'-'.$profile->dod_m.'-'.$profile->dod_d;
                    echo (new Date($dod))->i18nFormat(Configure::read('outputDateFormat'));
                } elseif (!empty($profile->dod_y)) {
                    echo $profile->dod_y;
                } else {
                    echo '<span class="n_a">'.__('unknown').'</span>';
                }
                ?></span>
            </li>
                <?php
                if (!empty($profile->plod)) {
                    ?>
            <li>
                <span class="label"><?= __('Place of Death') ?>: </span>
                <span class="value"><?= h($profile->plod) ?></span>
            </li>
                    <?php
                }
                ?>
                <?php
                if (!empty($profile->cod)) {
                    ?>
            <li>
                <span class="label"><?= __('Cause of Death') ?>: </span>
                <span class="value"><?= h($profile->cod) ?></span>
            </li>
                    <?php
                }
                ?>
                <?php
                if (!empty($profile->plobu)) {
                    ?>
            <li>
                <span class="label"><?= __('Place of Burial') ?>: </span>
                <span class="value"><?= h($profile->plobu) ?></span>
            </li>
                    <?php
                }
                ?>
                <?php
            } // Profile.living = 0
            ?>
    <li class="hr_top">
        <span class="label"><?= __('Immediate Family') ?>: </span>
        <div class="value"><?php
            // person can be child in only one family
        if (!empty($family['parents'])) {
            echo '<div>';
            if (isset($family['parents'][0])) {
                if ($profile->g == 'f') {
                    echo __('Daughter of');
                } elseif ($profile->g == 'm') {
                    echo __('Son of');
                } else {
                    echo __('Child of');
                }
                echo ' '.$this->Html->link($family['parents'][0]->fn, [$family['parents'][0]->id]);
            }
            if (isset($family['parents'][1])) {
                echo ' ' . __('and') . ' ' . $this->Html->link($family['parents'][1]->fn, [$family['parents'][1]->id]);
            }
            echo '</div>';
        }
            // display siblings
        if (!empty($family['siblings'])) {
            echo '<div>';
            if ($profile->g == 'f') {
                echo __('Sister of');
            } elseif ($profile->g == 'm') {
                echo __('Brother of');
            } else {
                echo __('Sibling of');
            }

            $i = 0;
            echo ' ';
            foreach ($family['siblings'] as $sibling) {
                if (sizeof($family['siblings']) > 1 && $i == sizeof($family['siblings']) - 1) {
                    echo __(' and ');
                } elseif ($i > 0) {
                    echo ', ';
                }
                echo $this->Html->link($sibling->fn, [$sibling->id]);
                $i++;
            }
            echo '</div>';
        }
        if (!empty($family['marriages'])) {
            // $family['marriages'] is also user in sidebar for can_delete check
            foreach ($family['marriages'] as $marriage) {
                echo '<div>';
                echo __('Married');
                if (!empty($marriage['spouse'])) {
                    echo ' '.__('to').' ';
                    echo $this->Html->link(
                        $marriage['spouse']->fn,
                        [$marriage['spouse']->id]
                    );
                }
                if (!empty($marriage['children'])) {
                    echo ' ';
                    $child_count = count($marriage['children']);
                    printf(__('with %d %s'), $child_count, __n('child', 'children', $child_count));
                    echo ': ';

                    $i = 0;
                    foreach ($marriage['children'] as $child) {
                        if (sizeof($marriage['children']) > 1 && $i == sizeof($marriage['children']) - 1) {
                            echo ' '.__('and').' ';
                        } elseif ($i > 0) {
                            echo ', ';
                        }
                        echo $this->Html->link($child->fn, [$child->id]);
                        $i++;
                    }
                }
                echo '</div>';
            }
        }
        ?></div>
    </li>
        <?php
        if (!empty($profile->created)) {
            ?>
        <li class="hr_top">
            <span class="label"><?= __('Added') ?>: </span>
            <span class="value small"><?php
            echo $this->Time->timeAgoInWords($profile->created, [
                'format' => Configure::read('outputDateFormat') . ' HH:mm'
            ]);
                                      if (!empty($profile->creator)) {
                                          echo ' ' . __('by') . ' ' . h($profile->creator->d_n);
                                      }
                                        ?>&nbsp;</span>
        </li>
            <?php
        }
        if (!empty($profile->last_login)) {
            ?>
        <li class="hr">
            <span class="label"><?= __('Last Login') ?>: </span>
            <span class="value small"><?php
            echo $this->Time->timeAgoInWords($profile->last_login, [
                'format' => Configure::read('outputDateFormat') . ' HH:mm'
            ]); ?></span>
        </li>
            <?php
        }
        ?>
    </ul>
    <div style="clear: right;">&nbsp;</div>
    <div class="panel" id="PanelProfileAttachment">
        <div class="inner">
        <div class="legend"><?php
        if ($this->currentUser->get('lvl') <= LVL_EDITOR) {
            echo $this->Html->link(
                __('add photo'),
                ['controller' => 'Attachments', 'action' => 'add', 'class' => 'Profile', 'foreignid' => $profile->id],
                ['id' => 'ProfileAddAttachmentLegendLink', 'class' => 'javascript_action']
            );
        }
            echo __('Photo Gallery');

        ?></div>
<?php
if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
    ?>
        <div class="dropdown form" id="FormProfileAttachment">
        <?php
            echo $this->Html->script('jquery.textarearesizer.min');
            echo $this->Form->create(null, ['type' => 'file',
                'id' => 'ProfileViewAttachmentForm',
                'url' => [
                    'controller' => 'Attachments',
                    'action' => 'add',
                    'class' => 'Profile',
                    'foreignid' => $profile->id
                ]
            ]);

            //if ($this->Html->value('Attachment.id')) {
            //  $uuid = $this->Html->value('Attachment.id');
            //} else {
                $uuid = $this->Text->uuid();
            //}

            echo $this->Form->control('id', ['type' => 'hidden', 'id' => 'AttachmentId']);
            echo $this->Form->control('referer', ['type' => 'hidden', 'value' => base64_encode($this->Url->build(null, true))]);
            echo $this->Form->control(
                'Attachment.user_id',
                ['type' => 'hidden', 'value' => $this->currentUser->get('id'), 'id' => 'AttachmentUserId']
            );

            echo $this->Form->control(
                'attachments_links.0.attachment_id',
                ['type' => 'hidden', 'id' => 'AttachmentsLink0AttachmentId']
            );
            echo $this->Form->control(
                'attachments_links.0.foreign_id',
                ['type' => 'hidden', 'value' => $profile->id, 'id' => 'AttachmentsLink0ForeignId']
            );
            echo $this->Form->control(
                'attachments_links.0.class',
                ['type' => 'hidden', 'value' => 'Profile', 'id' => 'AttachmentsLink0Class']
            );
            echo $this->Form->control(
                'filename',
                ['type' => 'file', 'label' => __('Filename').':', 'id' => 'AttachmentFilename']
            );

            echo $this->Form->control('title', [
                'label' => __('Title').':',
                'error' => __('Please enter a title for your attachment.'),
                'id' => 'AttachmentTitle'
            ]);
            echo $this->Form->control(
                'description',
                ['label' => __('Description').':', 'rows' => 4, 'id' => 'AttachmentDescription']
            );
            echo '<div class="input submit">';
            echo $this->Form->button(__('Save'), [
                'type' => 'submit',
                'id'  => 'AttachmentSubmitButton'
            ]);
            echo '<span class="javascript_action">';
            echo ' '.__('or', true).' '.$this->Html->link(
                __('Cancel', true),
                '#',
                ['id' => 'CancelProfileAddAttachment']
            );
            echo '</span>';
            echo '</div>';
            echo $this->Form->end();
        ?>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
            <?php
                // do not hide form it there's submitted data
                //if (!$this->Html->value('Attachment.id')) {
                    echo '$("#FormProfileAttachment").hide();';
                //}
            ?>
                $('#AttachmentDescription:not(.processed)').TextAreaResizer();

                $('#ProfileViewAttachmentForm').submit(function(){
                    $('#AttachmentSubmitButton').attr('disabled', true);
                });

                $('#AttachmentFilename').change(function(){
                    if ($('#AttachmentTitle').val()=='') {
                        var fileName = $('#AttachmentFilename').val();
                        var extractStart = fileName.lastIndexOf('\\')+1;
                        var extractStart2 = fileName.lastIndexOf('/')+1;
                        if (extractStart2 > extractStart) extractStart = extractStart2;

                        fileName = fileName.substring(extractStart, fileName.length);

                        var extractEnd = fileName.lastIndexOf('.');
                        if (extractEnd>=0) fileName = fileName.substring(0, extractEnd);

                        $('#AttachmentTitle').val(fileName);
                    }
                });

                $("#ProfileAddAttachmentLegendLink").click(function () {
                    $("#FormProfileAttachment").show("normal", function(){
                        $("#ProfileAddAttachmentLegendLink").hide();
                    });
                    return false;
                });

                $("#CancelProfileAddAttachment").click(function () {
                    $("#FormProfileAttachment").hide("normal", function(){
                        $("#ProfileAddAttachmentLegendLink").show();
                    });
                    return false;
                });
            });
        </script>
    <?php
} // Auth check for editor level
?>
        <div class="body">
            <?php
            if (!$attachments->isEmpty()) {
                echo '<div id="ProfileAttachments">';
                foreach ($attachments as $attachment) {
                    echo '<div class="profile_attachment" id="'.$attachment->id.'">';

                    // display edit/delete attachment buttons
                    if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
                        echo '<div class="_actions">';
                        echo $this->Html->link(
                            $this->Html->image('edit.gif', [
                                'alt' => __('edit')
                                ]),
                            [
                                'controller' => 'Attachments',
                                'action' => 'edit',
                                $attachment->id
                            ],
                            [
                                'id'     => 'ProfileEditAttachmentLink',
                                'title'  => __('edit'),
                                'escape' => false
                            ]
                        );
                        echo $this->Html->link(
                            $this->Html->image('delete.gif', [
                                'alt' => __('delete')
                                ]),
                            [
                                'controller' => 'attachments',
                                'action' => 'delete',
                                $attachment->id
                            ],
                            [
                                'id'     => 'ProfileDeleteAttachmentLink',
                                'title'  => __('delete'),
                                'escape' => false
                                ],
                            __('Are you sure you want to delete this attachment?')
                        );
                        echo '</div>';
                    }

                    // display image with link to view attachment
                    echo $this->Html->link($this->Html->image(
                        'thumbs/'.$attachment->id.'.png',
                        [
                            'onmouseover' => '$("#ProfileAttachmentFooter").html("'.
                                ((empty($attachment->title))?
                                '&nbsp;':h($attachment->title)).'");',
                            'onmouseout'  => '$("#ProfileAttachmentFooter").html("&nbsp;");'
                        ]
                    ), [
                        'controller' => 'attachments',
                        'action' => 'view',
                        $attachment->id
                    ], ['class' => 'profile_attachment_image', 'escape' => false]);

                    echo '</div>';
                }
                echo '<div style="clear:both;"></div>';
                echo '</div>';
                echo '<div style="clear:both;" id="ProfileAttachmentFooter">&nbsp;</div>';
            } else {
                echo '<div class="n_a">';
                echo __('There are currently no media files for this person. Please do add yours.');
                echo '</div>';
            }
            ?>
        </div>
        </div>
    </div>

    <div class="panel">
        <div class="inner">
        <div class="legend"><?php
            if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
                echo $this->Html->link(__('add'), '#', [
                    'id'    => 'ProfileAddPostLegendLink',
                    'class' => 'javascript_action'
                ]);
            }
            echo __('Memories');
        ?></div>
        <div class="dropdown form" id="FormProfilePost">
            <?php
                echo $this->Form->create(null, [
                    'id' => 'ProfileViewPostForm',
                    'url' => [
                        'controller' => 'Profiles',
                        'action' => 'view',
                        $profile->id
                    ]
                ]);
                echo $this->Form->control('Post.blog_id', [
                    'type'  => 'hidden',
                    'value' => 1
                ]);
                echo $this->Form->control('Post.status', [
                    'type'  => 'hidden',
                    'value' => 2
                ]);
                echo $this->Form->control('Post.creator_id', [
                    'type'  => 'hidden',
                    'value' => $this->currentUser->get('id')
                ]);

                echo $this->Form->control('Category.0.foreign_id', [
                    'type'  => 'hidden',
                    'value' => $profile->id
                ]);
                echo $this->Form->control('Category.0.class', ['type' => 'hidden', 'value' => 'Profile']);

                echo $this->Form->control('Post.title', ['label' => __('Title') . ':', 'id' => 'PostTitle']);

                echo $this->Form->control('Post.body', [
                    'label' => __('Body', true).':',
                    'rows'  => 4,
                    'error' => __('Please enter some memories about this person.'),
                    'id' => 'PostBody'
                ]);

                echo '<div class="input submit">';
                    echo $this->Form->button(__('Save'), [
                        'type' => 'submit',
                        'id'  => 'PostSubmitButton'
                    ]);
                    echo '<span class="javascript_action">';
                    echo ' '.__('or', true).' '.$this->Html->link(
                        __('Cancel', true),
                        '#',
                        ['id' => 'CancelProfileAddPost']
                    );
                    echo '</span>';
                    echo '</div>';
                    echo $this->Form->end();
                    ?>
        </div>
        <div class="body" id="ProfilePostBody">
            <?php
            if (!$posts->isEmpty()) {
                foreach ($posts as $post) {
                    echo '<div class="profile_post" id="ProfilePost' . $post->id . '_div">';
                    echo '<div class="_header">';

                    if ($this->currentUser->exists() && $this->currentUser->get('lvl') <= LVL_EDITOR) {
                        echo '<div class="_actions">';
                        echo $this->Html->link(__('edit'), [
                            'controller' => 'Posts',
                            'action' => 'edit',
                            $post->id
                        ]);
                        echo ' ' . __('or') . ' ';
                        echo $this->Html->link(
                            __('delete'),
                            [
                                'controller' => 'Posts',
                                'action' => 'delete',
                                $post->id
                            ],
                            [
                                'class' => 'ajax_del_post',
                                'id' => 'ProfilePost' . $post->id
                                ],
                            __('Are your sure you want to delete this memory?')
                        );
                        echo '</div>';
                    }

                    echo '<h1>';
                    echo $this->Html->link($post->title, [
                        'controller' => 'Posts',
                        'action' => 'view',
                        $post->id
                    ]);
                    echo '</h1>';

                    // show date of publish and publisher
                    printf(
                        __('Published %1$s by %2$s.'),
                        $this->Time->timeAgoInWords(
                            $post->created,
                            ['format' => Configure::read('outputDateFormat').' HH:mm']
                        ),
                        $this->Html->link($post->creator->d_n, [
                            'controller' => 'Profiles',
                            'action'     => 'view',
                            $post->creator_id])
                    );

                    echo '</div>';

                    echo '<div class="_body">';
                    echo $this->Famiree->autop($body = $this->Famiree->excerpt($post->body));
                    echo '</div>';

                    if ($body != $post['body']) {
                        echo '<div class="_readmore">';
                        echo $this->Html->link(__('Read more...'), [
                            'controller' => 'Posts',
                            'action'     => 'view',
                            $post->id
                        ]);
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<div class="n_a">';
                __('There are currently no memories for this person. Please do add yours.');
                echo '</div>';
            }
            ?>
        </div>
        <?php
            echo $this->Html->script('jquery.textarearesizer.min') . PHP_EOL;
            echo $this->Html->script('ui.core') . PHP_EOL;
            echo $this->Html->script('ui.draggable') . PHP_EOL;
            echo $this->Html->script('ui.droppable') . PHP_EOL;
            echo $this->Html->css('ui.all');
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                // make images draggable
                $(".profile_attachment").draggable({
                    revert: 'invalid'
                });
                $("#SidebarProfileViewAvatar").droppable({
                    drop: function(event, ui) {
                        document.location.href = '<?php echo $this->Url->build([
                            'controller' => 'profiles',
                            'action' => 'edit_avatar',
                            $profile->id
                        ]); ?>/'+$(ui.draggable).attr('id');
                    }
                });

                // hide form when there is no data (but do not hide it when form error occurs)
                <?php
                    //if (!$this->Html->value('Post.creator_id')) {
                      echo '$("#FormProfilePost").hide();';
                    //}
                ?>
                $('#ProfileViewPostForm').submit(function(){
                    $('#PostSubmitButton').attr('disabled', true);
                });

                // show cancel action
                $(".javascript_action").show();

                // ajax event - delete post
                $(".ajax_del_post").click(function(){
                    $.ajax({
                        type: "GET",
                        url: $(this).attr('href'),
                        caller_id: $(this).attr('id'),
                        success: function(data, textStatus) {
                            $('#'+this.caller_id+'_div').remove();
                            if ($('.profile_post').length==0) {
                                $('#ProfilePostBody').html('<div class="n_a"><?php
                                    echo h(__('There are currently no memories for this person. Please do add yours.', true));
                                ?></div>');
                            }
                        }
                    });
                    return false;
                });

                // hide attachment actions
                $(".profile_attachment ._actions").hide();
                $(".profile_attachment").mouseover(function(){
                    $(this).children("._actions").show()
                });
                $(".profile_attachment").mouseout(function(){
                    $(this).children("._actions").hide()
                });

                // add resizer to textarea
                $('#PostBody:not(.processed)').TextAreaResizer();

                // toggle form events
                $("#ProfileAddPostLegendLink").click(function () {
                    $("#FormProfilePost").show("normal", function(){
                        $("#ProfileAddPostLegendLink").hide();
                    });
                    return false;
                });
                $("#CancelProfileAddPost").click(function () {
                    $("#FormProfilePost").hide("normal", function(){
                        $("#ProfileAddPostLegendLink").show();
                    });
                    return false;
                });
            });
        </script>
    </div>
    </div>
</div>
