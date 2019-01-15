<?php
    $this->set('sidebar', '');
?>
<h1><?= __('Search Results for "{0}"', h($criterio)) ?></h1>

<div id="IndexSearchPaginateHeader">
<?php
    echo $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total.')]);
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
    if (!empty($profile->ta)) {
        echo $this->Html->link(
            $this->Html->image('thumbs/' . $profile->ta . '.png', ['class' => 'avatar']),
            ['action' => 'view', $profile->id],
            ['escape' => false]
        );
    } else {
        echo $this->Html->link(
            $this->Html->image($profile->g . '.png', ['class' => 'avatar']),
            ['action' => 'view', $profile->id],
            ['escape' => false]
        );
    }
    echo '</td>' . PHP_EOL;

    echo '<td class="profile_list_data">';
    echo '<h1>';
    echo $this->Html->link($profile->d_n, ['action' => 'view', $profile->id]);
    echo '</h1>';
    if ($profile->l) {
        if ($age = $profile->age()) {
            printf('%d years old', $age);
            if (!empty($profile->loc)) {
                echo ', ';
            }
        }
    } else {
        echo __('Deceased');
        if (!empty($profile->loc)) {
            echo ', ';
        }
    }
    echo h($profile->loc);
    echo '</td>' . PHP_EOL;

    echo '<td class="profile_list_actions">';
    echo '<ul>';
        echo '<li>';
        echo $this->Html->link(__('View Tree'), ['action' => 'tree', $profile->id]);
        echo '</li>';
        echo '<li>';
        echo $this->Html->link(__('Show Profile'), ['action' => 'view', $profile->id]);
        echo '</li>';
    echo '</ul>';
    echo '</td>' . PHP_EOL;

    echo '</tr>' . PHP_EOL;
}
    echo '</table>';
    echo '<div class="paginator">';
        $this->Paginator->options(['url' => array_merge($this->passedArgs, ['criterio' => $criterio])]);
    echo $this->Paginator->numbers();
    echo '</div>';
?>
</div>
