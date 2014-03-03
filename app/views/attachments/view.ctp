<div id="AttachmentDetails">
<?php
	
	$added_by = __('anonymous', true);
	if (!empty($attachment['Attachment']['user_id'])) {
		if ($Auth['Profile']['id']==$attachment['Attachment']['user_id']) {
			$added_by = __('Me', true);
		} else {
			$added_by = $attachment['Profile']['d_n'];
		}
		$added_by = $this->Html->link($added_by, array(
			'controller'=>'profiles', 
			'action'=>'view', 
			$attachment['Attachment']['user_id']
		));
	}
	
	echo '<div>';
	printf(__('Added by %1$s, %2$s.', true),
		$added_by,
		$this->Date->timeAgoInWords($attachment['Attachment']['created'], 
			array('format' => Configure::read('outputDateFormat').' %H:%M'))
	);
	echo '</div>';
	
	$belongsTo = array();
	foreach ($attachment['AttachmentsLink'] as $alink) {
		if (empty($alink['Profile'])) {
			$belongsTo[] = __('Unknown', true);
		} else {
			if ($Auth['Profile']['id'] == $alink['Profile']['id']) {
				$bName = __('Me', true);
			} else {
				$bName = $alink['Profile']['d_n'];
			}
			$belongsTo[] = $this->Html->link($bName, array(
				'controller'=>'profiles', 
				'action'=>'view', 
				$alink['Profile']['id']
			));
		}
	}
	
	if (!empty($belongsTo)) {
		echo '<div>';
		__('Belongs to');
		echo ' '.$text->toList($belongsTo, __('and', true)).'.';
		echo '</div>';
	}
?>
</div>
<h1>
<?php 
	__('Attachment');
	if (!empty($attachment['Attachment']['title'])) {
		echo ': '; 
		echo $this->Sanitize->html($attachment['Attachment']['title']);
	}
?>
</h1>
<div>
	<?php
		echo $this->Html->image(
			$this->Html->url(
				array(
					'controller'=>'attachments', 
					'action'=>'display', 
					$attachment['Attachment']['id'], 
					'large',
					$quicks->slug($attachment['Attachment']['title']).'.'.
						strtolower($attachment['Attachment']['ext'])
				), true
			), array(
				'id' => 'AttachmentImage'
			)
		);
	?>
</div>
<?php
	if (!empty($attachment['Attachment']['description'])) {
		echo '<div>';
		echo $this->Sanitize->html($attachment['Attachment']['description']);
		echo '</div>';
	}
?>
<div id="NoteForm" class="form">
<?php
	echo $this->Form->Create('Imgnote', array('url' => array('controller' => 'imgnotes', 'action' => 'add')));
	echo '<fieldset>';
	echo '<legend>'.__('Add Note', true).'</legend>';
	echo $this->Form->input('attachment_id', array(
		'type'=>'hidden', 
		'value'=>$attachment['Attachment']['id'])
	);
	echo $this->Form->input('referer', array(
		'type'=>'hidden', 
		'value'=>base64_encode(Router::url(null, true)))
	);
	echo $this->Form->input('x1',     array('type' => 'hidden'));
	echo $this->Form->input('y1',     array('type' => 'hidden'));
	echo $this->Form->input('width',  array('type' => 'hidden'));
	echo $this->Form->input('height', array('type' => 'hidden'));
	
	//echo $this->Form->input('profile_title', array('type'=>'text', 'label'=>__('Profile', true).':'));
	echo $this->Form->input('profile_id', array('type' => 'hidden'));
	echo $this->Form->input('note', array(
		'type'=>'text',
		'label' => __('Note', true).':',
		'after' => ' '.$this->Html->image('ico_avatar_check.gif', array(
			'style' => 'display: none;',
			'id' => 'ImageAvatarCheck'
		))
	));
	echo $this->Form->input('crop_to_new', array(
		'type' => 'checkbox', 
		'label' => __('Crop and create new image', true)
	));
	
	echo '<div class="input submit">';
	echo $this->Form->submit(__('Save', true), array('div'=>false));
	echo ' '.__('or', true).' <span class="link" id="CancelNoteLink">'.__('Cancel', true).'</span>';
	echo '</div>';
	echo $this->Form->end();
	
	echo $this->Html->script('jquery.imgareaselect-0.8.min');
	echo $this->Html->script('jquery.imgnotes-0.2');
	
	echo $this->Html->script('ui.core');
	echo $this->Html->script('ui.autocomplete');
	echo $this->Html->css('imgnotes');
	echo $this->Html->css('ui.all');
?>
</div>
<script type="text/javascript">
	<?php
		echo 'var notes = [';
		$i = 0;
		foreach ($attachment['Imgnote'] as $imgnote) {
			if ($i++>0) echo ',';
			echo '{"x1":"'.$imgnote['x1'].'","y1":"'.$imgnote['y1'].
				'","height":"'.$imgnote['height'].'","width":"'.$imgnote['width'].
				'","note":"'.$imgnote['note'].'","id":"'.$imgnote['id'].'"'.
				(!empty($imgnote['profile_id'])?',"url":"'.$this->Html->url(array('controller'=>'profiles', 'action'=>'view', $imgnote['profile_id'])).'"':'').'}';
		}
		echo '];';
	?>
	
	var deleteNoteLink = '<?php echo addslashes($this->Html->link($this->Html->image('delete.gif', array(
									'alt'=>__('delete', true)
								)),
								array(
									'controller'=>'imgnotes',
									'action'=>'delete',
									'__noteid__'
								),
								array(
									'id'     => 'DeleteImgNoteLink',
									'title'  => __('delete', true),
									'escape' => false
								), 
								__('Are you sure you want to delete this note?', true))); ?>';
	
	$(document).ready(function() {
		$('#ImgnoteNote').autocomplete({
			url:'<?php echo $this->Html->url(array('controller'=>'profiles', 'action'=>'autocomplete')); ?>', 
			dataType:"text",
			width:"240px",
			formatResult: function(row) {
				$('#ImageAvatarCheck').hide();
				return row[1];
			},
			formatItem: function(data, i, total) {
				return data[1];
			},
			search: function() {
				$('#ImgnoteProfileId').val('');
				$('#ImageAvatarCheck').hide();
			},
			result: function(data, row) {
				$('#ImgnoteProfileId').val(row[0]);
				$('#ImageAvatarCheck').show();
			}
		});
		
		$('#AttachmentImage').imgNotes(notes, {deleteLink: deleteNoteLink });
		
		$('#CancelNoteLink').click(function() {
			$('#AttachmentImage').imgAreaSelect({ hide: true });
			$('#NoteForm').hide();
		});

		$('#AddNoteLink').click(function() {
			<?php
				// do a 10% frame from middle
				$w = round($large_sizes['width'] * .2);
				$x1 = round($large_sizes['width']/2 - $w/2);
				$x2 = $x1 + $w;
				$h = round($large_sizes['height'] * .2);
				$y1 = round($large_sizes['height']/2 - $h/2);
				$y2 = $y1 + $h;
				
				printf('var frame = {onSelectChange: ShowAddNote, '.
					'handles: true, '.
					'x1:%1$s, x2:%2$s, '.
					'y1:%3$s, y2:%4$s, '.
					'width:%5$s, height:%6$s};', $x1, $x2, $y1, $y2, $w, $h).PHP_EOL;
			?>
			ShowAddNote('#AttachmentImage', frame);
			
			$('#NoteForm').show();
			$('#AttachmentImage').imgAreaSelect(frame);
			
			return false;
		});
	});
	
	function ShowAddNote(img, area) {
		imgOffset = $(img).position();
		form_left  = parseInt(imgOffset.left) + parseInt(area.x1);
		form_top   = parseInt(imgOffset.top) + parseInt(area.y1) + parseInt(area.height)+5;
			
		$('#NoteForm').css({ left: form_left + 'px', top: form_top + 'px'});
		
		$('#NoteForm').show();
		
		$('#NoteForm').css("z-index", 10000);
		$('#ImgnoteX1').val(area.x1);
		$('#ImgnoteY1').val(area.y1);
		$('#ImgnoteHeight').val(area.height);
		$('#ImgnoteWidth').val(area.width);

	}
</script>