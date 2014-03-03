<?php
	if (!empty($sidebar))
	foreach ($sidebar as $panel_name => $panel) {
		if ($panel['visible']) {
			echo '<div class="panel">' . PHP_EOL;
			foreach ($panel['items'] as $li_name => $li) {
				if (@$li['visible']) {
					echo '<h1' . ($li['active'] ? ' class="active"' : '') . '>' . PHP_EOL;
					echo $html->link($li['title'], $li['url'], $li['params']) . PHP_EOL;
					echo '</h1>' . PHP_EOL;
					if (!empty($li['expand']) && !empty($li['submenu'])) {
						echo '<ul>' . PHP_EOL;
						foreach ($li['submenu'] as $lis) {
							if (@$lis['visible']) {
								echo '<li' . ($lis['active'] ? ' class="active"' : '') . '>' . PHP_EOL;
								echo $html->link($lis['title'], $lis['url'], $lis['params']) . PHP_EOL;
								echo '</li>' . PHP_EOL;
							}
						}
						echo '</ul>' . PHP_EOL;
					}
				}
			}
			echo '</div>' . PHP_EOL;
		}
	}
?>