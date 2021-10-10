<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset().PHP_EOL; ?>
	<title><?php
		echo __('Famiree');
		if (!empty($title_for_layout)) {
			echo ' :: ';
			echo $title_for_layout;
		}
	?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />

	<?php echo $this->Html->css('header') . PHP_EOL; ?>
	<?php echo $this->Html->css('tree') . PHP_EOL; ?>

	<?php echo $this->Html->script('jquery.min') . PHP_EOL; ?>
	<?php echo $this->Html->script('jquery.disable.text.select') . PHP_EOL; ?>
</head>
<body>
<?php
    // delete session variables so they dont appear on NEXT page
    // todo
?>
		<div id="header_container"><?php echo $this->element('header'); ?></div>
		<div id="tree_window">
			<!--<div id="tree_centerer">-->
				<div id="tree">
					<ul id="nodes">
<?php
	$spacing_y = 8.5/3;
	$spacing_x = 5/3;
	$node_h = 116/12;
	$node_w = 96/12;
	$space_h = ($spacing_y * 4) - $node_h;
	$space_w = $node_w - ($spacing_x * 3);
	$line_w = 0.083; //width of relationship lines (0.083=1px)

	// optimizations
    $node_link = $this->Html->link('__name__', ['controller'=>'profiles', 'action'=>'view', '__id__']);
	$prune_link = $this->Html->link('+', ['__id__'], ['class'=>'p']);

    // icon for tree action
	$image_tree = $this->Html->image('ico_family_tree.gif', ['class'=>'ico_tree', 'onclick'=>'ShowPopup(__id__);']);
	$tree_link = $this->Html->link(
		$this->Html->image('ico_family_tree.gif', ['alt' => __('Show Tree')]),
		['__id__'],
		['class'  => 'ico_tree', 'title'  => __('Show Tree'), 'escape' => false]
	);

    $image_avatar = $this->Html->link($this->Html->image('thumbs/__thumb__', ['class'=>'avatar']), ['action'=>'view', '__id__'], ['escape' => false]);
	$image_gender = $this->Html->link($this->Html->image('__gender__.png', ['class'=>'avatar']), ['action'=>'view', '__id__'], ['escape' => false]);

	foreach ($tree['p'] as $profile) {
		$node = $profile['Profile'];

		$class = "node";
		if (!empty($node['g']) && $node['id']>0) {
			$class .= " " . $node['g'];
			if (!$node['l']) $class .= "_d";
		}
		if (!isset($node['del'])) $class .= " user";
		if ($current_profile == $node['id']) $class .= " main";

        printf('<li id="TreeNode%4$d" style="bottom: %1$Fem; left: %2$Fem" class="%3$s">' . PHP_EOL,
			$node['y'] * $spacing_y,
			$node['x'] * $spacing_x,
			$class,
			$node['id']
		);

		if (!empty($node['nt']) && $node['nt'] == 'ghost') {
			echo $this->Html->link(__('Add This Person'), ['action' => $node['method'], $node['ref']], ['class'=>'ghost']) . PHP_EOL;
		} else {
			echo '<div class="avatar_wrap">' . PHP_EOL;
				echo '<div class="icon_wrap">' . PHP_EOL;
					if (!$this->currentUser->exists() || $this->currentUser->get('lvl') <= LVL_EDITOR) {
                        echo strtr($image_tree, ['__id__' => $node['id']]) . PHP_EOL;
					} else {
						echo strtr($tree_link, ['__id__' => $node['id']]) . PHP_EOL;
					}

					echo '<span class="ico_media">';
					if ($node['cn_med']) {
						echo $node['cn_med'];
					} else {
						echo '&nbsp;';
					}
					echo '</span>' . PHP_EOL;
					echo '<span class="ico_memories">';
					if ($node['cn_mem']) {
						echo $node['cn_mem'];
					} else {
						echo '&nbsp;';
					}
					echo '</span>' . PHP_EOL;
				echo '</div>' . PHP_EOL;

				// display generic image for living people when user is not authorized
				if (!empty($node['ta']) && ($this->currentUser->exists() || !$node['l'])) {
					echo strtr($image_avatar, ['__id__' => $node['id'], '__thumb__' => $node['ta'] . '.png']) . PHP_EOL;
				} else {
					echo strtr($image_gender, ['__gender__' => $node['g'], '__id__' => $node['id']]) . PHP_EOL;
				}
			echo '</div>'.PHP_EOL;

			if (!$this->currentUser->exists() && $node['l'] == 1) {
				if (!empty($node['dob_y']) && $this->Date->age($node['dob_y'])<=15) {
					echo '<div class="minor">'.__('Minor Child') . '</div>' . PHP_EOL;
				} else {
					// display only first name when no auth
					echo strtr($node_link, ['__name__' => $node['fn'], '__id__' => $node['id']]);
				}
			} else {
				echo strtr($node_link, ['__name__' => $node['d_n'], '__id__' => $node['id']]);

				if (!empty($node['mdn']) && $node['mdn'] != $node['ln']) {
					echo '<div>(' . $node['mdn'] . ')</div>' . PHP_EOL;
				}
			}

			if (!$node['l'] == 1) {
				echo '<sub>';
				if (!empty($node['dob_y']) || !empty($node['dod_y'])) {
					if (!$node['dob_y'] || (!$node['dod_y'] || empty($node['dob_c']))) echo 'b.';
					if (!empty($node['dob_c'])) echo 'c.';
					echo $node['dob_y'];
					echo '-';
					if (!$node['dod_y'] || (!$node['dob_y'] || empty($node['dod_c']))) echo 'd.';
					if (!empty($node['dod_c'])) echo 'c.';
					echo $node['dod_y'];
				} else {
					__('deceased');
				}
				echo '</sub>' . PHP_EOL;
			}

			if (!empty($node['showPrune'])) {
				echo strtr($prune_link, ['__id__' => $node['id']]);
			}
		}

		echo '</li>' . PHP_EOL;
	}
?>
					</ul>
					<div id="unions">
<?php
	foreach ($tree['u'] as $union_id => $union) {
		$spouse_count = 0;
		if (isset($union['spouse_count'])) $spouse_count = $union['spouse_count']-1;

		$parent1 = @$tree['p'][$union['p'][0]]['Profile'];
		$parent2 = @$tree['p'][$union['p'][1]]['Profile'];

		// swap parents if neccesary
		if ($parent2['x'] < $parent1['x']) {
			$tmpParent = $parent1;
			$parent1 = $parent2;
			$parent2 = $tmpParent;
		}
		if (($spouse_count>0) || ($parent1['y'] <= 4) || (isset($union['c']) && sizeof($union['c']) == 0)) {
			// descendant mode; $parent_d_r==direct relative
			$class = 'd';
			if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';
			//if (@$union['t']!='t') $class .= ' x';

			$style = sprintf('bottom: %Fem; ', (($parent1['y'] * $spacing_y) + ($node_h / 2) + $spouse_count - $line_w));
			$style .= 'left: ' . (($parent1['x'] * $spacing_x) + $node_w) . 'em; ';
			$style .= 'width: ' . (($parent2['x'] - $parent1['x']) * $spacing_x - $node_w) . 'em; ';

			echo '<div id="l_'.$parent1['id'].'_'.$parent2['id'].'" class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

			if (!empty($union['c']) && sizeof($union['c'])==1) {
				// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				// 26.12. 2009 changed:
				// $child = $tree['p'][$union['c'][0]['id']]['Profile'];
				// to:
				$child = $tree['p'][$union['c'][0]]['Profile'];
				if ($parent1['x'] <= $child['x'] && $parent2['x'] >= $child['x']) {
					// single child, straight
					$class = 'v';
					if (!empty($child['d_r'])) $class .= ' d_r';
					$style = sprintf('bottom: %Fem; ', (($child['y'] * $spacing_y) + $node_h ));
					$style .= sprintf('left: %Fem; ', (($child['x'] * $spacing_x) + ($node_w / 2) - ($line_w / 2)));
					$style .= sprintf('height: %Fem; ', (($spacing_y * 4) - ($node_h / 2) + $spouse_count));
					echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;
				} else {
					//single child, bent
					$class = 'c';
					if (!empty($child['d_r'])) $class .= ' d_r';
					$style = sprintf('left: %Fem; ', (($child['x'] * $spacing_x) + ($node_w / 2) - $line_w));
					$style .= sprintf('bottom: %Fem; ', (($child['y'] * $spacing_y) + $node_h));
					echo '<div class="'.$class.'" style="'.$style.'">' . PHP_EOL;

						$class = 'rb s';
						if (!empty($child['d_r'])) $class .= ' d_r';
						$style = sprintf('height: %Fem; ', (($node_h / 2) + ($space_h * 2 / 3)));
						$style .= sprintf('width: %Fem; ', ((($parent2['x'] + $parent1['x']) / 2 - $child['x']) * $spacing_x));
						echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

						$class = 'v s';
						if (!empty($child['d_r'])) $class .= ' d_r';
						$style = sprintf('height: %Fem; ', ($space_h / 3 - $line_w));
						echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

					echo '</div>' . PHP_EOL;

				}
			} else if (!empty($union['c']) && sizeof($union['c'])>1) {
				// multiple children
				$bottom = $parent1['y'] * $spacing_y;
                $center = ($parent1['x']+$parent2['x']) / 2 * $spacing_x + ($node_w / 2); // beware there is sum of parents in original version

                // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                // 20.01.2019 added code for multiple spouses and positioning of vertical lines
                if (sizeof($union['c']) > 0  && $spouse_count > 0) {
                    // check if first child position outside bounds
                    $firstChild = $tree['p'][$union['c'][0]]['Profile'];
                    $newCenter = $firstChild['x'] * $spacing_x + ($node_w / 2);
                    if ($newCenter > $center) {
                        $center = $newCenter;
                    }
                    // check last child
                    $lastChild = $tree['p'][$union['c'][sizeof($union['c'])-1]]['Profile'];
                    $newCenter = $lastChild['x'] * $spacing_x + ($node_w / 2);
                    if ($newCenter < $center) {
                        $center = $newCenter;
                    }
                }

				$class = 'v';
				if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';
				$style = sprintf('left: %Fem; ', $center);
				$style .= sprintf('bottom: %Fem; ', ($bottom - ($space_h / 3)));
				$style .= sprintf('height: %Fem; ', (($node_h / 2) + ($space_h / 3)) + $spouse_count);
				echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

				foreach ($union['c'] as $child_u) {
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					// 26.12. 2009 changed:
					// $child = $tree['p'][$child_u['id']]['Profile'];
					// to:
					$child = $tree['p'][$child_u]['Profile'];

					$class = '';
					if (!empty($child['d_r'])) $class .= 'd_r';

					$style = sprintf('height: %Fem; ', ($space_h * 2 / 3));
					$style .= sprintf('bottom: %Fem; ', ($bottom - $space_h));

					if ($child['x']==(int)(($parent1['x']+$parent2['x']) / 2)) {
						// left of center
						$style .= " border-style:solid none none solid;";
						$style .= sprintf('left: %Fem; ', ($child['x'] * $spacing_x + $node_w / 2));
						$style .= sprintf('width: %Fem; ', $line_w / 2);
					} else if ($child['x'] * $spacing_x < $center) {
						// left of center
						$style .= " border-style:solid none none solid;";
						$style .= sprintf('left: %Fem; ', ($child['x'] * $spacing_x + $node_w / 2));
						$style .= sprintf('width: %Fem; ', ($center - $child['x'] * $spacing_x - ($node_w / 2)));
					} else {
						// right of center
						$style .= " border-style:solid solid none none;";
						$style .= sprintf('left: %Fem; ', ($center + $line_w));
						$style .= sprintf('width: %Fem; ', ($child['x'] * $spacing_x - $center + $node_w / 2 - $line_w));
					}
					echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;
				}
			}
		} else {
            // ancestor mode
			$class = 'rbl';
			if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';
			$style = sprintf('left: %Fem; ', ($parent1['x'] * $spacing_x + ($node_w / 2) - $line_w));
			$style .= sprintf('bottom: %Fem; ', ($parent1['y'] * $spacing_y - ($space_h / 3) + $line_w));
			$style .= sprintf('height: %Fem; ', ($space_h / 3));
			$style .= sprintf('width: %Fem; ', (($parent2['x'] - $parent1['x']) * $spacing_x - 0.167));
			echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

			if (!empty($union['c'])) {
				if (sizeof($union['c'])==1) {
					// single child
                    $child = $tree['p'][$union['c'][0]]['Profile'];
                    $childsCenter = (($child['x'] * $spacing_x) + ($node_w / 2) - $line_w);

                    if ($child['x'] < $parent1['x'] && $child['x'] < $parent2['x']) {
                        $parentsCenter = ($parent1['x']+$parent2['x']) / 2 * $spacing_x - ($node_w / 2) - $line_w;
                        // right side
                        $class = 'rb';
                        if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';

                        $style = sprintf('left: %Fem; ', $childsCenter);
                        $style .= sprintf('width: %Fem; ', $parentsCenter);
                        $style .= sprintf('bottom: %Fem; ', ($child['y'] * $spacing_y) + $space_h * 1 / 3 + $node_h);
                        $style .= sprintf('height: %Fem; ', ($space_h * 1 / 3 + $line_w));
                        echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

                        $verticalHeight = $space_h * 1 / 3;
                    } elseif ($child['x'] > $parent1['x'] && $child['x'] > $parent2['x']) {
                        $parentsCenter = ($parent1['x']+$parent2['x']) / 2 * $spacing_x + ($node_w / 2) - $line_w;
                        // left side when only one child
                        $class = 'lb';
                        if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';

                        $style = sprintf('left: %Fem; ', $parentsCenter);
                        $style .= sprintf('width: %Fem; ', $childsCenter - $parentsCenter);
                        $style .= sprintf('bottom: %Fem; ', ($child['y'] * $spacing_y) + $space_h * 1 / 3 + $node_h);
                        $style .= sprintf('height: %Fem; ', ($space_h * 1 / 3 + $line_w));
                        echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

                        $verticalHeight = $space_h * 1 / 3;
                    } else {
                        $verticalHeight = $space_h * 2 / 3;
                    }

                    $class = 'v';
                    if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';

                    $style = sprintf('left: %Fem; ', $childsCenter);
                    $style .= sprintf('bottom: %Fem; ', (($child['y'] * $spacing_y) + $node_h));
                    $style .= sprintf('height: %Fem; ', ($verticalHeight + $line_w));

                    if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';
                    echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;
				} else if (sizeof($union['c'])>1) {
					$child0 = $tree['p'][$union['c'][0]]['Profile'];
					$center = ($parent1['x']+$parent2['x']) / 2 * $spacing_x; // beware there is sum of parents in original version

					$class = 'v';
					if (!empty($parent1['d_r']) || !empty($parent2['d_r'])) $class .= ' d_r';
					$style = sprintf('left: %Fem; ', ($center + $line_w + $node_w / 2));
					//$style .= sprintf('bottom: %Fem; ', (($child0['y'] * $spacing_y) + $node_h + ($space_h / 3))); // why child0???
					$style .= sprintf('bottom: %Fem; ', (($parent1['y'] * $spacing_y) - ($space_h / 3 *2))); // modifed version of prev line

					$style .= sprintf('height: %Fem; ', ($space_h / 3 + 0.167));
					echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;

					foreach ($union['c'] as $child_u) {
						$child = $tree['p'][$child_u]['Profile'];

						$class = '';
						if (!empty($child['d_r'])) $class .= 'd_r';
						$style = sprintf('height: %Fem; ', ($space_h / 3 - $line_w));
						$style .= sprintf('bottom: %Fem; ', ($parent1['y'] * $spacing_y - $space_h));
						if ($child['x'] * $spacing_x < $center) {
							$style .= " border-style:solid none none solid;";
							$style .= sprintf('left: %Fem; ', ($child['x'] * $spacing_x + $node_w / 2));
							$style .= sprintf('width: %Fem; ', ($center - $child['x'] * $spacing_x - $line_w));
						} else if ($child['x'] * $spacing_x > $center) {
							$style .= " border-style:solid solid none none;";
							$style .= sprintf('left: %Fem; ', ($center + $line_w + $node_w / 2 - $line_w));
							$style .= sprintf('width: %Fem; ', ($child['x'] * $spacing_x - $center));
						} else {
							$style .= " border-style:none solid none none;";
							$style .= sprintf('left: %Fem; ', ($center + $node_w / 2 + $line_w));
							$style .= 'width: 0;';
						}
						echo '<div class="'.$class.'" style="'.$style.'">&#160;</div>' . PHP_EOL;
					}
				}
			}
		}
	}
?>
					</div>
				<!--</div>-->
			</div>

			<div id="HiddenPopup" style="display:none;">
				<ul>
					<li><?php echo $this->Html->link(__('Show Tree'), ['__n__'], ['class'=>'toggle']); ?></li>
					<li><?php echo $this->Html->link(__('Edit Profile'), ['action'=>'edit', '__n__'], ['class'=>'toggle']); ?></li>
					<li><?php echo $this->Html->link(__('Add Mother or Father'), ['action'=>'add_parent', '__n__'], ['class'=>'toggle']); ?></li>
					<li><?php echo $this->Html->link(__('Add Partner'), ['action'=>'add_partner', '__n__'], ['class'=>'toggle']); ?></li>
					<li><?php echo $this->Html->link(__('Add Sibling'), ['action'=>'add_sibling', '__n__'], ['class'=>'toggle']); ?></li>
					<li><?php echo $this->Html->link(__('Add Child'), ['action'=>'add_child', '__n__'], ['class'=>'toggle']); ?></li>
				</ul>
			</div>

			<div id="zoomer">
				<div id="zoom_track">&#160;</div>
				<div id="zoom_slider">&#160;</div>
				<?php
					if (!empty($tree['p'][$current_profile]['Profile']['ta']) && ($this->currentUser->exists() || !$tree['p'][$current_profile]['Profile']['l'])) {
						echo $this->Html->image('thumbs/'.$tree['p'][$current_profile]['Profile']['ta'].'.png', ['alt' => $tree['p'][$current_profile]['Profile']['d_n']]) . PHP_EOL;
					} else if ($tree['p'][$current_profile]['Profile']['g']=='f') {
						echo $this->Html->image('f.png', ['alt' => $tree['p'][$current_profile]['Profile']['d_n']]) . PHP_EOL;
					} else {
						echo $this->Html->image('m.png', ['alt' => $tree['p'][$current_profile]['Profile']['d_n']]) . PHP_EOL;
					}
				?>
			</div>
		</div>
        <?php echo $this->Html->script('tree') . PHP_EOL; ?>
		<script type="text/javascript">
			$(document).ready(function() {
				var sel ;
				if(document.selection && document.selection.empty){
					document.selection.empty() ;
				} else if(window.getSelection) {
					sel=window.getSelection();
					var body = document.getElementsByTagName("body")[0];
					sel.collapse(body, 0);
				}
				$('#tree_window').disableTextSelect();

				<?php
					if ($highlightedProfile = $this->getRequest()->getQuery('highlight')) {
						echo 'MoveToNode(\'' . $highlightedProfile . '\');' . PHP_EOL;
					}
				?>
			});
		</script>
</body>
</html>
