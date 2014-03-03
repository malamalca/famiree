<?php
	$this->set('sidebar', '');
?>
<h1><?php __('Search Results'); ?></h1>

<div id="IndexSearchPaginateHeader">
<?php
	echo $this->Paginator->counter(array('format' => 'Page %page% of %pages%, showing %current% records out of %count% total.'));
?>
</div>

<div class="index">
<?php
	echo '<table>';
	echo '<thead>';
	echo '<tr>';
	echo '<th>&nbsp;</th>';
	echo '<th>&nbsp;</th>';
	echo '<th>&nbsp;</th>';
	echo '</tr>';
	echo '</thead>';
	
	$i = 1;
	foreach ($profiles as $profile) {
		echo '<tr class="profile_list' . ((($i++ % 2) == 1) ? ' alt_row' : '') . '">' . PHP_EOL;
		
		echo '<td class="profile_list_ta">';
		if (!empty($profile['Profile']['ta'])) {
			echo $this->Html->link(
				$this->Html->image(
					'thumbs/' . $profile['Profile']['ta'] . '.png',
					array('class' => 'avatar')
				),
				array('action' => 'view', $profile['Profile']['id']),
				array('escape' => false)
			);
		} else {
			echo $this->Html->link(
				$this->Html->image(
					$profile['Profile']['g'] . '.png',
					array('class' => 'avatar')
				),
				array('action' => 'view', $profile['Profile']['id']),
				array('escape' => false)
			);
		}
		echo '</td>' . PHP_EOL;
		
		echo '<td class="profile_list_data">';
		echo '<h1>';
		echo $this->Html->link(
			$profile['Profile']['d_n'],
			array('action' => 'view', $profile['Profile']['id'])
		);
		echo '</h1>';
		if ($profile['Profile']['l']) {
			if ($age = $this->Quicks->profileAge($profile['Profile'])) {
				printf('%d years old', $age);
				if (!empty($profile['Profile']['loc'])) echo ', ';
			}
		} else {
			__('Deceased');
			if (!empty($profile['Profile']['loc'])) echo ', ';
		}
		echo $this->Sanitize->html($profile['Profile']['loc']);
		echo '</td>' . PHP_EOL;
		
		echo '<td class="profile_list_actions">';
		echo '<ul>';
			echo '<li>';
			echo $this->Html->link(__('View Tree', true), array(
				'plugin'     => null,
				'controller' => 'profiles',
				'action'     => 'tree', 
				$profile['Profile']['id']
			));
			echo '</li>';
			echo '<li>';
			echo $this->Html->link(__('Show Profile', true), array(
				'plugin'     => null,
				'controller' => 'profiles',
				'action'     => 'view', 
				$profile['Profile']['id']
			));
			echo '</li>';
		echo '</ul>';
		echo '</td>' . PHP_EOL;
		
		echo '</tr>' . PHP_EOL;
	}
	
	echo '<tfoot>';
	echo '<tr>';
	echo '<td colspan="3">';
		$this->Paginator->options(array('url' => array_merge($this->passedArgs, array('criterio' => $criterio))));
		echo $this->Paginator->numbers();
	echo '</td>';
	echo '</tr>';
	echo '</tfoot>';
	
	echo '</table>';
?>
</div>