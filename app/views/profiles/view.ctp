<?php
	// converted to cakephp 1.3
?>
<h1><?php
	echo $title_for_layout = $this->Sanitize->html($profile['Profile']['d_n']);
	$this->set(compact('title_for_layout'));
	
	if (!empty($profile['Profile']['mdn']) && 
		($profile['Profile']['mdn'] != $profile['Profile']['ln']))
	{
		echo ' ('.$this->Sanitize->html($profile['Profile']['mdn']).')';
	}
?>
</h1>
<div>
	<div id="ProfileInfoPanel">
		<ul class="label_value">
			<?php
				if (!empty($profile['Profile']['loc'])) {
			?>
			<li class="hr">
				<span class="label"><?php __('Location'); ?>: </span>
				<span class="value"><?php 
					echo $this->Sanitize->html($profile['Profile']['loc']); ?></span>
			</li>
			<?php
				}
			?>
			<li>
				<span class="label"><?php __('Date of Birth'); ?>: </span>
				<span class="value"><?php 
					
					if (@checkdate(
						$profile['Profile']['dob_m'], 
						$profile['Profile']['dob_d'],
						$profile['Profile']['dob_y']))
					{
						$dob = 
							$profile['Profile']['dob_y'].'-'.
							$profile['Profile']['dob_m'].'-'.
							$profile['Profile']['dob_d'];
						echo $this->Date->format(Configure::read('outputDateFormat'), $dob);
						
						// display age
						echo ' (';
						if (!$profile['Profile']['l']) echo __('would be', true).' ';
						echo $this->Date->age($dob).' '.__('years old', true).')';
						
					} else if (!empty($profile['Profile']['dob_y'])) {
						echo $profile['Profile']['dob_y']; 
					} else {
						echo '<span class="n_a">' . __('unknown', true) . '</span>';
					}
				?></span>
			</li>
			<?php
				if (!empty($profile['Profile']['plob'])) {
			?>
			<li>
				<span class="label"><?php __('Place of Birth'); ?>: </span>
				<span class="value"><?php 
					echo $this->Sanitize->html($profile['Profile']['plob']); ?></span>
			</li>
			<?php
				}
			?>
			
			<?php
				if ($profile['Profile']['l'] != 1) {
			?>
			<li style="border-top: solid 1px #c0c0c0;">
				<span class="label"><?php __('Status'); ?>: </span>
				<span class="value"><?php __('Deceased'); ?></span>
			</li>
			<li>
				<span class="label"><?php __('Date of Death'); ?>: </span>
				<span class="value"><?php 
					if (@checkdate(
						$profile['Profile']['dod_m'], 
						$profile['Profile']['dod_d'], 
						$profile['Profile']['dod_y']
					)) {
						echo $this->Date->format(Configure::read('outputDateFormat'), implode('-', array(
							$profile['Profile']['dod_y'],
							$profile['Profile']['dod_m'],
							$profile['Profile']['dod_d']
						)));
					} else if (!empty($profile['Profile']['dod_y'])) {
						echo $profile['Profile']['dod_y']; 
					} else {
						echo '<span class="n_a">'.__('unknown', true).'</span>';
					}
				?></span>
			</li>
			<?php
				if (!empty($profile['Profile']['plod'])) {
			?>
			<li>
				<span class="label"><?php __('Place of Death'); ?>: </span>
				<span class="value"><?php 
					echo $this->Sanitize->html($profile['Profile']['plod']); ?></span>
			</li>
			<?php
				}
			?>
			<?php
				if (!empty($profile['Profile']['cod'])) {
			?>
			<li>
				<span class="label"><?php __('Cause of Death'); ?>: </span>
				<span class="value"><?php 
					echo $this->Sanitize->html($profile['Profile']['cod']); ?></span>
			</li>
			<?php
				}
			?>
			<?php
				if (!empty($profile['Profile']['plobu'])) {
			?>
			<li>
				<span class="label"><?php __('Place of Burial'); ?>: </span>
				<span class="value"><?php 
					echo $this->Sanitize->html($profile['Profile']['plobu']); ?></span>
			</li>
			<?php
				}
			?>
			<?php
				} // Profile.living = 0
			?>
	<li class="hr_top">
		<span class="label"><?php __('Immediate Family'); ?>: </span>
		<div class="value"><?php
			// person can be child in only one family
			if (!empty($family['parents'])) {
				echo '<div>';
				if (isset($family['parents'][0])) {
					if ($profile['Profile']['g']=='f') {
						echo __('Daughter of', true);
					} else if ($profile['Profile']['g']=='m') { 
						echo __('Son of', true);
					} else {
						echo __('Child of', true);
					}
					echo ' '.$this->Html->link($family['parents'][0]['Profile']['fn'],
						array($family['parents'][0]['Profile']['id']));
				}
				if (isset($family['parents'][1])) {
					echo ' '.__('and', true).' '.$this->Html->link($family['parents'][1]['Profile']['fn'],
						array($family['parents'][1]['Profile']['id']));
				}
				echo '</div>';
			}
			// display siblings
			if (!empty($family['siblings'])) {
				echo '<div>';
				if ($profile['Profile']['g']=='f') {
					echo __('Sister of', true);
				} else if ($profile['Profile']['g']=='m') {
					echo __('Brother of', true);
				} else {
					echo __('Sibling of', true);
				}
				
				$i = 0;
				echo ' ';
				foreach ($family['siblings'] as $sibling) {
					if (sizeof($family['siblings'])>1 && $i == sizeof($family['siblings'])-1) {
						echo __(' and ', true);
					} else if ($i>0) {
						echo ', ';
					}
					echo $this->Html->link($sibling['Profile']['fn'], array($sibling['Profile']['id']));
					$i++;
				}
				echo '</div>';
			}
			if (!empty($family['marriages'])) {
				// $family['marriages'] is also user in sidebar for can_delete check
				foreach ($family['marriages'] as $marriage) {
					echo '<div>';
					echo __('Married', true);
					if (!empty($marriage['spouse'])) {
						echo ' '.__('to', true).' ';
						echo $this->Html->link($marriage['spouse']['Profile']['fn'], 
							array($marriage['spouse']['Profile']['id']));
					}
					if (!empty($marriage['children'])) {
						echo ' ';
						$child_count = sizeof($marriage['children']);
						printf(__('with %d %s', true), $child_count, __n('child', 'children', $child_count, true));
						echo ': ';
						
						$i = 0;
						foreach ($marriage['children'] as $child) {
							if (sizeof($marriage['children'])>1 && $i == sizeof($marriage['children'])-1) {
								echo ' '.__('and', true).' ';
							} else if ($i>0) {
								echo ', ';
							}
							echo $this->Html->link($child['Profile']['fn'], array($child['Profile']['id']));
							$i++;
						}
					}
					echo '</div>';
				}
				
			}
		?></div>
	</li>
		<?php
			if (!empty($profile['Profile']['created'])) {
		?>
		<li class="hr_top">
			<span class="label"><?php __('Added'); ?>: </span>
			<span class="value small"><?php
				echo $this->Date->timeAgoInWords($profile['Profile']['created'], array(
					'format' => Configure::read('outputDateFormat') . ' %H:%M'
				));
				if (!empty($profile['Creator'])) {
					echo ' ' . __('by', true) . ' ' . $this->Sanitize->html($profile['Creator']['d_n']);
				}
			?>&nbsp;</span>
		</li>
		<?php
			}
			if (!empty($profile['Profile']['last_login'])) {
		?>
		<li class="hr">
			<span class="label"><?php __('Last Login'); ?>: </span>
			<span class="value small"><?php 
				echo $this->Date->timeAgoInWords($profile['Profile']['last_login'], array(
					'format' => Configure::read('outputDateFormat') . ' %H:%M'
				)); ?></span>
		</li>
		<?php
			}
		?>
	</ul>
	<div style="clear: right;">&nbsp;</div>
	<div class="panel" id="PanelProfileAttachment">
		<div class="inner">
		<div class="legend"><?php
			if ($Auth['Profile']['lvl'] <= LVL_EDITOR) {
				echo $this->Html->link(__('add photo', true), '#',
				array('id' => 'ProfileAddAttachmentLegendLink', 'class' => 'javascript_action'));
			}
			__('Photo Gallery'); 
			
		?></div>
<?php
	if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
?>
		<div class="dropdown form" id="FormProfileAttachment">
			<?php
				echo $this->Html->script('jquery.textarearesizer.min');
				echo $this->Form->create('Attachment', array('type' => 'file',
					'id' => 'ProfileViewAttachmentForm',
					'url' => array(
						'controller' => 'profiles',
						'action' => 'view',
						$profile['Profile']['id']
					)
				));
				
				if ($this->Html->value('Attachment.id')) {
					$uuid = $this->Html->value('Attachment.id');
				} else {
					$uuid = String::uuid();
				}
				echo $this->Form->input('Attachment.id', 
					array('type' => 'hidden', 'value' => $uuid));
				echo $this->Form->input('Attachment.user_id', 
					array('type' => 'hidden', 'value' => $Auth['Profile']['id']));
				
				echo $this->Form->input('AttachmentsLink.0.attachment_id', 
					array('type' => 'hidden', 'value' => $uuid));
				echo $this->Form->input('AttachmentsLink.0.foreign_id', 
					array('type' => 'hidden', 'value' => $profile['Profile']['id']));
				echo $this->Form->input('AttachmentsLink.0.class', 
					array('type' => 'hidden', 'value' => 'Profile'));
				echo $this->Form->input('Attachment.filename', 
					array('type' => 'file', 'label' => __('Filename', true).':'));
				
				echo $this->Form->input('Attachment.title', array(
					'label' => __('Title', true).':',
					'error' => __('Please enter a title for your attachment.', true)
				));
				echo $this->Form->input('Attachment.description', 
					array('label' => __('Description', true).':', 'rows' => 4));
				echo '<div class="input submit">';
				echo $this->Form->submit(__('Save', true), array(
					'div' => false,
					'id'  => 'AttachmentSubmitButton'
				));
				echo '<span class="javascript_action">';
				echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), '#', 
					array('id' => 'CancelProfileAddAttachment'));
				echo '</span>';
				echo '</div>';
				echo $this->Form->end();
			?>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				<?php
					// do not hide form it there's submitted data
					if (!$this->Html->value('Attachment.id')) {
						echo '$("#FormProfileAttachment").hide();';
					}
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
				if (!empty($profile['Attachment'])) {
					echo '<div id="ProfileAttachments">';
					foreach ($profile['Attachment'] as $attachment) {
						echo '<div class="profile_attachment" id="'.$attachment['id'].'">';
						
						// display edit/delete attachment buttons
						if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
							echo '<div class="_actions">';
							echo $this->Html->link($this->Html->image('edit.gif', array(
									'alt'=>__('edit', true)
								)), array(
									'controller'=>'attachments',
									'action'=>'edit',
									$attachment['id']
								),
								array(
									'id'     => 'ProfileEditAttachmentLink',
									'title'  => __('edit', true),
									'escape' => false
								));
							echo $this->Html->link($this->Html->image('delete.gif', array(
									'alt'=>__('delete', true)
								)),
								array(
									'controller'=>'attachments',
									'action'=>'delete',
									$attachment['id']
								),
								array(
									'id'     => 'ProfileDeleteAttachmentLink',
									'title'  => __('delete', true),
									'escape' => false
								), 
								__('Are you sure you want to delete this attachment?', true));
							echo '</div>';
						}
						
						// display image with link to view attachment
						echo $this->Html->link($this->Html->image(
							'thumbs/'.$attachment['id'].'.png',
							array(
								'onmouseover' => '$("#ProfileAttachmentFooter").html("'.
									((empty($attachment['title']))?
									'&nbsp;':$this->Sanitize->html($attachment['title'])).'");', 
								'onmouseout'  => '$("#ProfileAttachmentFooter").html("&nbsp;");'
							)
						), array(
							'controller' => 'attachments',
							'action' => 'view',
							$attachment['id']
						), array('class' => 'profile_attachment_image', 'escape' => false));
						
						echo '</div>';
					}
					echo '<div style="clear:both;"></div>';
					echo '</div>';
					echo '<div style="clear:both;" id="ProfileAttachmentFooter">&nbsp;</div>';
				} else {
					echo '<div class="n_a">';
					__('There are currently no media files for this person. Please do add yours.');
					echo '</div>';
				}
			?>
		</div>
		</div>
	</div>
	
	<div class="panel">
		<div class="inner">
		<div class="legend"><?php
			if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
				echo $this->Html->link(__('add', true), '#', array(
					'id'    => 'ProfileAddPostLegendLink',
					'class' => 'javascript_action'
				));
			}
			__('Memories');
		?></div>
		<div class="dropdown form" id="FormProfilePost">
			<?php
				echo $this->Form->create('Post', array(
					'id' => 'ProfileViewPostForm',
					'url'=>array(
						'controller' => 'profiles',
						'action'     => 'view',
						$profile['Profile']['id']
					)
				));
				echo $this->Form->input('Post.blog_id', array(
					'type'  => 'hidden',
					'value' => 1
				));
				echo $this->Form->input('Post.status', array(
					'type'  => 'hidden',
					'value' => 2
				));
				echo $this->Form->input('Post.creator_id', array(
					'type'  => 'hidden',
					'value' => $Auth['Profile']['id']
				));
					
				echo $this->Form->input('Category.0.foreign_id', array(
					'type'  => 'hidden',
					'value' => $profile['Profile']['id']
				));
				echo $this->Form->input('Category.0.class', array('type'=>'hidden', 'value'=>'Profile'));
					
				echo $this->Form->input('Post.title', array('label'=>__('Title', true).':'));
				
				echo $this->Form->input('Post.body', array(
					'label' => __('Body', true).':',
					'rows'  => 4,
					'error' => __('Please enter some memories about this person.', true)
				));
				
				echo '<div class="input submit">';
					echo $this->Form->submit(__('Save', true), array(
						'div' => false,
						'id'  => 'PostSubmitButton'
					));
					echo '<span class="javascript_action">';
					echo ' '.__('or', true).' '.$this->Html->link(__('Cancel', true), '#', 
						array('id'=>'CancelProfileAddPost'));
					echo '</span>';
				echo '</div>';
				echo $this->Form->end();
			?>
		</div>
		<div class="body" id="ProfilePostBody">
			<?php
				if (!empty($profile['Post'])) {
					foreach ($profile['Post'] as $post) {
						echo '<div class="profile_post" id="ProfilePost'.$post['id'].'_div">';
						echo '<div class="_header">';
						
						if (!empty($Auth) && $Auth['Profile']['lvl'] <= LVL_EDITOR) {
							echo '<div class="_actions">';
							echo $this->Html->link(__('edit', true), array(
								'admin'      => true,
								'plugin'     => 'lil_blogs',
								'controller' => 'posts',
								'action'     => 'edit',
								$post['id']
							));
							echo ' '.__('or', true).' ';
							echo $this->Html->link(__('delete', true),
								array(
									'admin'      => true,
									'plugin'     => 'lil_blogs',
									'controller' => 'posts',
									'action'     => 'delete',
									$post['id']
								),
								array(
									'class'=>'ajax_del_post', 
									'id'=>'ProfilePost'.$post['id']
								), __('Are your sure you want to delete this memory?', true));
							echo '</div>';
						}
						
						echo '<h1>';
						echo $this->LilBlogs->permalink('memories', array('Post' => $post));
						echo '</h1>';
						
						// show date of publish and publisher
						printf(__('Published %1$s by %2$s.', true), $this->Date->timeAgoInWords($post['created'], 
							array('format' => Configure::read('outputDateFormat').' %H:%M')),
							$this->Html->link($post['Author']['d_n'], array(
							'controller' => 'profiles',
							'action'     => 'view',
							$post['Author']['id']))
						);
						
						echo '</div>';
						
						echo '<div class="_body">';
						echo $this->Sanitize->wpautop($body = $this->Quicks->excerpt($post['body']));
						echo '</div>';
						
						if ($body != $post['body']) {
							echo '<div class="_readmore">';
							echo $this->Html->link(__('Read more...', true), array(
								'controller' => 'posts',
								'action'     => 'view',
								$post['id']
							));
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
						document.location.href = '<?php echo $this->Html->url(array(
							'controller' => 'profiles',
							'action' => 'edit_avatar',
							$profile['Profile']['id']
						)); ?>/'+$(ui.draggable).attr('id');
					}
				});
				
				// hide form when there is no data (but do not hide it when form error occurs)
				<?php 
					if (!$this->Html->value('Post.creator_id')) {
						echo '$("#FormProfilePost").hide();';
					}
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
									echo $this->Sanitize->html(__('There are currently no memories for this person. Please do add yours.', true)); 
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